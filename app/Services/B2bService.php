<?php

namespace App\Services;

use App\Models\B2bOrder;
use App\Models\B2bOrderItem;
use App\Models\InventoryBalance;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Location;
use App\Models\Member;
use App\Models\PickupTicket;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\StockMovement;
use App\Models\StockOperation;
use App\Models\StockReservation;
use App\Models\User;
use DomainException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class B2bService
{
    public function __construct(
        protected CreditPolicyService $creditPolicyService,
        protected PricingService $pricingService
    ) {}

    /**
     * Atomically create a B2B order, reserve physical stock, enforce credit policy,
     * and issue a one-time cryptographic pickup ticket.
     */
    public function createOrder(array $data, User $creator): array
    {
        return DB::transaction(function () use ($data, $creator) {
            $memberId = $data['member_id'];
            $fulfillmentLocationId = $data['fulfillment_location_id'];
            $paymentTerm = $data['payment_term'] ?? B2bOrder::TERM_CASH;
            $itemsData = $data['items'] ?? [];
            $notes = $data['notes'] ?? null;

            if (empty($itemsData)) {
                throw new DomainException('Pesanan B2B harus memiliki minimal 1 barang.');
            }

            // 1. Lock Member row for UPDATE to avoid credit limit race conditions
            $member = Member::where('id', $memberId)->lockForUpdate()->firstOrFail();
            $location = Location::where('id', $fulfillmentLocationId)->firstOrFail();

            // 2. Sort items by product_id ascending to guarantee lock ordering (prevent InnoDB deadlocks)
            usort($itemsData, fn($a, $b) => $a['product_id'] <=> $b['product_id']);

            // Calculate Due Date based on Payment Term
            $dueDate = null;
            if ($paymentTerm === B2bOrder::TERM_NET_7) {
                $dueDate = Carbon::today()->addDays(7);
            } elseif ($paymentTerm === B2bOrder::TERM_NET_14) {
                $dueDate = Carbon::today()->addDays(14);
            } elseif ($paymentTerm === B2bOrder::TERM_NET_30) {
                $dueDate = Carbon::today()->addDays(30);
            }

            // 3. Pre-calculate totals and snapshot product costs
            $subtotal = 0.00;
            $lineItems = [];
            $discountPercent = (float) ($member->tier?->discount_percent ?? 0.00);

            foreach ($itemsData as $row) {
                $productId = $row['product_id'];
                $productUomId = $row['product_uom_id'] ?? null;
                $inputQty = (float) $row['quantity'];
                $customPrice = isset($row['unit_price']) ? (float) $row['unit_price'] : null;

                if ($inputQty <= 0) {
                    throw new DomainException('Kuantitas pesanan harus lebih besar dari 0.');
                }

                $product = Product::where('id', $productId)->firstOrFail();

                // Conversion factor calculation
                $conversionFactor = 1.00;
                if ($productUomId) {
                    $uom = ProductUom::where('id', $productUomId)
                        ->where('product_id', $productId)
                        ->firstOrFail();
                    $conversionFactor = (float) $uom->conversion_factor;
                }
                $baseQty = $inputQty * $conversionFactor;

                // Base price & tier discount
                $basePrice = $customPrice ?? (float) $product->price;
                $itemDiscount = ($basePrice * ($discountPercent / 100));
                $netUnitPrice = max(0.00, $basePrice - $itemDiscount);
                $lineSubtotal = round($netUnitPrice * $inputQty, 2);
                $subtotal += $lineSubtotal;

                $lineItems[] = [
                    'product' => $product,
                    'product_uom_id' => $productUomId,
                    'quantity_input' => $inputQty,
                    'quantity_base' => $baseQty,
                    'unit_price' => $netUnitPrice,
                    'discount_percent' => $discountPercent,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $taxAmount = 0.00; // Tax configuration
            $totalAmount = $subtotal + $taxAmount;

            // 4. Validate Credit Policy (Plafon Kredit & Overdue Check)
            $this->creditPolicyService->validateCreditOrder($member, $totalAmount, $paymentTerm);

            // 5. Lock Inventory Balance rows and verify Available Stock
            foreach ($lineItems as &$item) {
                $productId = $item['product']->id;
                $baseQty = $item['quantity_base'];

                $balance = InventoryBalance::where('location_id', $fulfillmentLocationId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$balance) {
                    throw new DomainException("Stok produk '{$item['product']->name}' belum terdaftar di lokasi {$location->name}.");
                }

                $available = (float) $balance->on_hand_qty - (float) $balance->reserved_qty;
                if ($available < $baseQty) {
                    throw new DomainException(
                        "Stok '{$item['product']->name}' di {$location->name} tidak mencukupi untuk direservasi. Tersedia: " .
                        number_format($available, 2) . ", Diminta: " . number_format($baseQty, 2) . "."
                    );
                }

                $item['balance'] = $balance;
                $item['cogs_snapshot'] = (float) $balance->avg_unit_cost;
            }
            unset($item);

            // 6. Generate Unique Order Number
            $prefix = 'B2B-' . date('Ymd') . '-';
            $lastOrder = B2bOrder::where('order_number', 'LIKE', $prefix . '%')
                ->orderByDesc('order_number')
                ->first();
            $nextSeq = $lastOrder ? ((int) substr($lastOrder->order_number, -4)) + 1 : 1;
            do {
                $orderNumber = $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
                $nextSeq++;
            } while (B2bOrder::where('order_number', $orderNumber)->exists());

            // 7. Create B2bOrder Header
            $order = B2bOrder::create([
                'order_number' => $orderNumber,
                'member_id' => $member->id,
                'creator_user_id' => $creator->id,
                'fulfillment_location_id' => $fulfillmentLocationId,
                'payment_term' => $paymentTerm,
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'discount_amount' => 0.00,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'status' => B2bOrder::STATUS_RESERVED,
                'notes' => $notes,
                'placed_at' => now(),
                'ready_at' => now(),
            ]);

            // 8. Create Order Items & Atomically Reserve Stock
            foreach ($lineItems as $item) {
                $orderItem = B2bOrderItem::create([
                    'b2b_order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_uom_id' => $item['product_uom_id'],
                    'quantity_input' => $item['quantity_input'],
                    'quantity_base' => $item['quantity_base'],
                    'unit_price' => $item['unit_price'],
                    'cogs_snapshot' => $item['cogs_snapshot'],
                    'discount_percent' => $item['discount_percent'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Create Stock Reservation
                StockReservation::create([
                    'b2b_order_id' => $order->id,
                    'b2b_order_item_id' => $orderItem->id,
                    'location_id' => $fulfillmentLocationId,
                    'product_id' => $item['product']->id,
                    'reserved_qty_base' => $item['quantity_base'],
                    'status' => StockReservation::STATUS_ACTIVE,
                    'reserved_at' => now(),
                ]);

                // Increment reserved_qty on balance
                $balance = $item['balance'];
                $balance->reserved_qty = (float) $balance->reserved_qty + $item['quantity_base'];
                $balance->save();
            }

            // 9. Generate One-Time Cryptographic Pickup Ticket
            $rawToken = 'TK-' . strtoupper(Str::random(12));
            $tokenHash = hash('sha256', $rawToken);
            $ticketPrefix = 'TKT-' . date('Ymd') . '-';
            $lastTicket = PickupTicket::where('ticket_number', 'LIKE', $ticketPrefix . '%')
                ->orderByDesc('ticket_number')
                ->first();
            $nextTicketSeq = $lastTicket ? ((int) substr($lastTicket->ticket_number, -4)) + 1 : 1;
            do {
                $ticketNumber = $ticketPrefix . str_pad((string) $nextTicketSeq, 4, '0', STR_PAD_LEFT);
                $nextTicketSeq++;
            } while (PickupTicket::where('ticket_number', $ticketNumber)->exists());

            $ticket = PickupTicket::create([
                'b2b_order_id' => $order->id,
                'ticket_number' => $ticketNumber,
                'token_hash' => $tokenHash,
                'raw_token_preview' => $rawToken,
                'fulfillment_location_id' => $fulfillmentLocationId,
                'status' => PickupTicket::STATUS_ISSUED,
                'issued_at' => now(),
                'expires_at' => Carbon::now()->addDays(7),
            ]);

            return [
                'order' => $order->load(['member.tier', 'fulfillmentLocation', 'items.product', 'pickupTicket']),
                'ticket' => $ticket,
                'raw_token' => $rawToken,
            ];
        }, attempts: 3);
    }

    /**
     * Redeem One-Time QR Pickup Ticket at Fulfillment Location:
     * - Atomically checks ticket status (must be ISSUED)
     * - Prevents double redemption (scan ulang DITOLAK instan)
     * - Converts stock reservation into physical stock-out (movement Ungu #8B5CF6)
     * - Marks ticket REDEEMED and order HANDED_OVER
     * - Issues Accounts Receivable Invoice
     */
    public function redeemPickupTicket(string $tokenOrNumber, User $redeemer, Location $activeLocation): array
    {
        return DB::transaction(function () use ($tokenOrNumber, $redeemer, $activeLocation) {
            $input = trim($tokenOrNumber);
            $tokenHash = hash('sha256', $input);

            // Search by token hash, ticket_number, or raw_token_preview
            $ticket = PickupTicket::where(function ($q) use ($input, $tokenHash) {
                $q->where('token_hash', $tokenHash)
                  ->orWhere('ticket_number', $input)
                  ->orWhere('raw_token_preview', $input);
            })->lockForUpdate()->first();

            if (!$ticket) {
                throw new DomainException('Tiket serah terima QR tidak ditemukan. Pastikan token atau nomor tiket valid.');
            }

            // 1. Double Redemption Protection
            if ($ticket->status === PickupTicket::STATUS_REDEEMED) {
                $redeemedTime = $ticket->redeemed_at ? $ticket->redeemed_at->format('d/m/Y H:i') : '-';
                $redeemerName = $ticket->redeemedBy?->name ?? 'Staf';
                throw new DomainException(
                    "Tiket pickup #{$ticket->ticket_number} SUDAH DIGUNAKAN (Already Redeemed) pada {$redeemedTime} oleh {$redeemerName}. Pengambilan ganda dicegah oleh sistem."
                );
            }

            if ($ticket->status !== PickupTicket::STATUS_ISSUED) {
                throw new DomainException("Tiket pickup #{$ticket->ticket_number} tidak aktif (Status: {$ticket->status}).");
            }

            // 2. Fulfillment Location Guard
            if ($ticket->fulfillment_location_id !== $activeLocation->id) {
                $destLocation = Location::find($ticket->fulfillment_location_id);
                throw new DomainException(
                    "Lokasi serah terima tidak sesuai. Tiket ini dialokasikan di '{$destLocation?->name}', sedangkan Anda berada di lokasi '{$activeLocation->name}'."
                );
            }

            $order = B2bOrder::where('id', $ticket->b2b_order_id)->lockForUpdate()->firstOrFail();

            if ($order->status === B2bOrder::STATUS_HANDED_OVER) {
                throw new DomainException("Pesanan #{$order->order_number} sudah berstatus diserahkan (HANDED_OVER).");
            }

            // 3. Atomically Convert Reservation into Physical Stock-Out
            $items = $order->items()->with('product')->orderBy('product_id', 'asc')->get();

            // Create Stock Operation Header
            $operation = StockOperation::create([
                'operation_uuid' => (string) Str::uuid(),
                'operation_type' => 'B2B_PICKUP',
                'document_type' => 'B2B_ORDER',
                'document_id' => $order->id,
                'document_number' => $order->order_number,
                'location_id' => $activeLocation->id,
                'actor_user_id' => $redeemer->id,
                'posted_at' => now(),
                'notes' => "Serah terima pesanan B2B #{$order->order_number} via Tiket QR #{$ticket->ticket_number}",
            ]);

            foreach ($items as $item) {
                $balance = InventoryBalance::where('location_id', $activeLocation->id)
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $qtyBase = (float) $item->quantity_base;

                // Decrement BOTH on_hand_qty and reserved_qty atomically
                $balance->on_hand_qty = max(0.00, (float) $balance->on_hand_qty - $qtyBase);
                $balance->reserved_qty = max(0.00, (float) $balance->reserved_qty - $qtyBase);
                $balance->save();

                // Record purple stock movement (movement.outbound / #8B5CF6)
                StockMovement::create([
                    'operation_id' => $operation->id,
                    'location_id' => $activeLocation->id,
                    'product_id' => $item->product_id,
                    'delta_qty_base' => -$qtyBase,
                    'unit_cost_snapshot' => (float) $balance->avg_unit_cost,
                    'movement_type' => 'B2B_PICKUP',
                    'semantic_status' => 'outbound',
                    'occurred_at' => now(),
                ]);

                // Fulfill Stock Reservation
                StockReservation::where('b2b_order_item_id', $item->id)
                    ->where('status', StockReservation::STATUS_ACTIVE)
                    ->update([
                        'status' => StockReservation::STATUS_FULFILLED,
                        'released_at' => now(),
                    ]);
            }

            // 4. Mark Ticket as REDEEMED
            $ticket->update([
                'status' => PickupTicket::STATUS_REDEEMED,
                'redeemed_at' => now(),
                'redeemed_by_user_id' => $redeemer->id,
            ]);

            // 5. Mark Order as HANDED_OVER
            $order->update([
                'status' => B2bOrder::STATUS_HANDED_OVER,
                'handed_over_at' => now(),
            ]);

            // 6. Issue Invoice
            $invoicePrefix = 'INV-B2B-' . date('Ymd') . '-';
            $lastInvoice = Invoice::where('invoice_number', 'LIKE', $invoicePrefix . '%')
                ->orderByDesc('invoice_number')
                ->first();
            $nextInvSeq = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, -4)) + 1 : 1;
            do {
                $invoiceNumber = $invoicePrefix . str_pad((string) $nextInvSeq, 4, '0', STR_PAD_LEFT);
                $nextInvSeq++;
            } while (Invoice::where('invoice_number', $invoiceNumber)->exists());
            $isCash = $order->payment_term === B2bOrder::TERM_CASH;

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'b2b_order_id' => $order->id,
                'member_id' => $order->member_id,
                'invoice_date' => Carbon::today(),
                'due_date' => $order->due_date ?? Carbon::today(),
                'total_amount' => $order->total_amount,
                'paid_amount' => $isCash ? $order->total_amount : 0.00,
                'outstanding_amount' => $isCash ? 0.00 : $order->total_amount,
                'status' => $isCash ? Invoice::STATUS_PAID : Invoice::STATUS_OPEN,
            ]);

            if ($isCash) {
                InvoicePayment::create([
                    'invoice_id' => $invoice->id,
                    'member_id' => $order->member_id,
                    'amount' => $order->total_amount,
                    'payment_method' => 'CASH',
                    'reference_number' => 'CASH-PICKUP-' . $ticket->ticket_number,
                    'recorded_by_id' => $redeemer->id,
                    'notes' => 'Pembayaran tunai lunas saat serah terima barang di lokasi.',
                    'paid_at' => now(),
                ]);
            }

            return [
                'order' => $order->fresh(),
                'ticket' => $ticket->fresh(),
                'invoice' => $invoice->fresh(),
            ];
        }, attempts: 3);
    }

    /**
     * Cancel an active B2B order and release all reserved stock.
     */
    public function cancelOrder(B2bOrder $order, User $user, string $reason = ''): B2bOrder
    {
        return DB::transaction(function () use ($order, $user, $reason) {
            $lockedOrder = B2bOrder::where('id', $order->id)->lockForUpdate()->firstOrFail();

            if (!$lockedOrder->canBeCancelled()) {
                throw new DomainException("Pesanan #{$lockedOrder->order_number} berstatus {$lockedOrder->status} tidak dapat dibatalkan.");
            }

            // Release all active reservations
            $reservations = StockReservation::where('b2b_order_id', $lockedOrder->id)
                ->where('status', StockReservation::STATUS_ACTIVE)
                ->get();

            foreach ($reservations as $res) {
                $balance = InventoryBalance::where('location_id', $res->location_id)
                    ->where('product_id', $res->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($balance) {
                    $balance->reserved_qty = max(0.00, (float) $balance->reserved_qty - (float) $res->reserved_qty_base);
                    $balance->save();
                }

                $res->update([
                    'status' => StockReservation::STATUS_RELEASED,
                    'released_at' => now(),
                ]);
            }

            // Revoke ticket if exists
            if ($lockedOrder->pickupTicket && $lockedOrder->pickupTicket->status === PickupTicket::STATUS_ISSUED) {
                $lockedOrder->pickupTicket->update(['status' => PickupTicket::STATUS_REVOKED]);
            }

            $lockedOrder->update([
                'status' => B2bOrder::STATUS_CANCELLED,
                'notes' => trim($lockedOrder->notes . ' | Dibatalkan oleh ' . $user->name . ': ' . $reason),
            ]);

            return $lockedOrder->fresh();
        });
    }

    /**
     * Record a debt payment against an open/overdue B2B invoice.
     */
    public function payInvoice(Invoice $invoice, float $amount, string $paymentMethod, ?string $reference, User $recordedBy, ?string $notes = null): InvoicePayment
    {
        return DB::transaction(function () use ($invoice, $amount, $paymentMethod, $reference, $recordedBy, $notes) {
            $lockedInvoice = Invoice::where('id', $invoice->id)->lockForUpdate()->firstOrFail();

            if ($amount <= 0) {
                throw new DomainException('Jumlah pembayaran harus lebih dari 0.');
            }

            if ($amount > (float) $lockedInvoice->outstanding_amount) {
                $formattedMax = number_format($lockedInvoice->outstanding_amount, 0, ',', '.');
                throw new DomainException("Pembayaran melebihi sisa tagihan. Maksimal pelunasan: Rp {$formattedMax}.");
            }

            $payment = InvoicePayment::create([
                'invoice_id' => $lockedInvoice->id,
                'member_id' => $lockedInvoice->member_id,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'reference_number' => $reference,
                'recorded_by_id' => $recordedBy->id,
                'notes' => $notes,
                'paid_at' => now(),
            ]);

            $newPaid = (float) $lockedInvoice->paid_amount + $amount;
            $newOutstanding = max(0.00, (float) $lockedInvoice->outstanding_amount - $amount);
            $newStatus = $newOutstanding <= 0 ? Invoice::STATUS_PAID : Invoice::STATUS_PARTIALLY_PAID;

            $lockedInvoice->update([
                'paid_amount' => $newPaid,
                'outstanding_amount' => $newOutstanding,
                'status' => $newStatus,
            ]);

            return $payment;
        });
    }
}
