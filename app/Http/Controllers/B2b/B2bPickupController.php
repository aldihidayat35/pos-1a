<?php

namespace App\Http\Controllers\B2b;

use App\Http\Controllers\Controller;
use App\Models\PickupTicket;
use App\Services\B2bService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class B2bPickupController extends Controller
{
    public function __construct(
        protected B2bService $b2bService
    ) {}

    /**
     * Display QR Ticket Scanner & Manual Input interface.
     */
    public function scan(Request $request): View
    {
        $user = $request->user();
        $activeLocation = $user->getActiveLocation();

        // Recent redeemed tickets at this location
        $recentRedemptions = PickupTicket::where('fulfillment_location_id', $activeLocation?->id)
            ->where('status', PickupTicket::STATUS_REDEEMED)
            ->with(['order.member', 'redeemedBy'])
            ->latest('redeemed_at')
            ->take(10)
            ->get();

        // Pending tickets at this location
        $pendingTickets = PickupTicket::where('fulfillment_location_id', $activeLocation?->id)
            ->where('status', PickupTicket::STATUS_ISSUED)
            ->with(['order.member'])
            ->latest('issued_at')
            ->take(10)
            ->get();

        return view('b2b.pickup.scan', compact('user', 'activeLocation', 'recentRedemptions', 'pendingTickets'));
    }

    /**
     * AJAX Verify ticket code before submitting redemption.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate(['token' => 'required|string']);
        $input = trim($request->token);
        $tokenHash = hash('sha256', $input);

        $ticket = PickupTicket::where(function ($q) use ($input, $tokenHash) {
            $q->where('token_hash', $tokenHash)
              ->orWhere('ticket_number', $input)
              ->orWhere('raw_token_preview', $input);
        })->with(['order.member.tier', 'order.items.product', 'fulfillmentLocation', 'redeemedBy'])->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket pickup tidak ditemukan. Pastikan token atau nomor tiket valid.',
            ], 404);
        }

        $activeLocation = $request->user()->getActiveLocation();
        $locationMismatch = $activeLocation && $ticket->fulfillment_location_id !== $activeLocation->id;

        return response()->json([
            'success' => true,
            'ticket' => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'token_preview' => $ticket->raw_token_preview,
                'status' => $ticket->status,
                'issued_at' => $ticket->issued_at?->format('d/m/Y H:i'),
                'redeemed_at' => $ticket->redeemed_at?->format('d/m/Y H:i'),
                'redeemed_by' => $ticket->redeemedBy?->name,
                'location_mismatch' => $locationMismatch,
                'destination_location' => $ticket->fulfillmentLocation?->name,
                'active_location' => $activeLocation?->name,
            ],
            'order' => [
                'id' => $ticket->order->id,
                'order_number' => $ticket->order->order_number,
                'status' => $ticket->order->status,
                'payment_term' => $ticket->order->payment_term,
                'total_amount' => $ticket->order->total_amount,
                'total_formatted' => 'Rp ' . number_format($ticket->order->total_amount, 0, ',', '.'),
                'member_name' => $ticket->order->member->name,
                'member_code' => $ticket->order->member->member_code,
                'items_count' => $ticket->order->items->count(),
                'items' => $ticket->order->items->map(fn($it) => [
                    'name' => $it->product->name,
                    'qty' => (float) $it->quantity_input,
                    'subtotal' => 'Rp ' . number_format($it->subtotal, 0, ',', '.'),
                ]),
            ],
        ]);
    }

    /**
     * Process Redemption / Serah Terima Barang B2B.
     */
    public function redeem(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = $request->user();
        $activeLocation = $user->getActiveLocation();

        if (!$activeLocation) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Silakan pilih lokasi aktif sebelum melakukan serah terima.'], 422);
            }
            return back()->with('error', 'Silakan pilih lokasi aktif (Gudang/Toko) sebelum melakukan serah terima.');
        }

        try {
            $result = $this->b2bService->redeemPickupTicket($request->token, $user, $activeLocation);
            $order = $result['order'];
            $ticket = $result['ticket'];

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Serah terima berhasil! Tiket #{$ticket->ticket_number} telah digunakan.",
                    'order_id' => $order->id,
                ]);
            }

            return redirect()->route('b2b.orders.show', $order)
                ->with('success', "Serah terima berhasil! Tiket #{$ticket->ticket_number} telah digunakan. Stok reservasi dikonversi menjadi stok keluar (Ungu), dan faktur telah dibukukan.");
        } catch (DomainException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
