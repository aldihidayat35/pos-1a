<?php

namespace App\Services;

use App\Models\B2bOrder;
use App\Models\Invoice;
use App\Models\Member;
use DomainException;
use Illuminate\Support\Carbon;

class CreditPolicyService
{
    /**
     * Calculate comprehensive real-time credit exposure for a Member.
     *
     * Total Exposure = Invoiced Outstanding (Unpaid invoices)
     *                + Un-invoiced Active Orders with Credit Terms (Placed/Reserved/Ready)
     *
     * Note: Once an order is handed over, an invoice is issued and the order is no longer
     * counted in un-invoiced orders, guaranteeing ZERO double counting.
     */
    public function getMemberCreditExposure(Member $member): array
    {
        $creditLimit = (float) $member->credit_limit;

        // 1. Invoiced Outstanding (Faktur tempo yang belum lunas)
        $invoicedOutstanding = (float) Invoice::where('member_id', $member->id)
            ->whereIn('status', [Invoice::STATUS_OPEN, Invoice::STATUS_PARTIALLY_PAID, Invoice::STATUS_OVERDUE])
            ->sum('outstanding_amount');

        // 2. Un-invoiced Active Credit Exposure (Pesanan tempo aktif yang belum diserahterimakan/difakturkan)
        $unInvoicedActiveExposure = (float) B2bOrder::where('member_id', $member->id)
            ->whereIn('payment_term', [B2bOrder::TERM_NET_7, B2bOrder::TERM_NET_14, B2bOrder::TERM_NET_30])
            ->whereIn('status', [B2bOrder::STATUS_PLACED, B2bOrder::STATUS_RESERVED, B2bOrder::STATUS_READY_FOR_PICKUP])
            ->sum('total_amount');

        $totalExposure = $invoicedOutstanding + $unInvoicedActiveExposure;
        $availableCredit = max(0.00, $creditLimit - $totalExposure);
        $utilizationPercent = $creditLimit > 0 ? round(($totalExposure / $creditLimit) * 100, 1) : 0;

        // 3. Check for Overdue Invoices
        $today = Carbon::today()->toDateString();
        $overdueInvoices = Invoice::where('member_id', $member->id)
            ->where('outstanding_amount', '>', 0)
            ->where('due_date', '<', $today)
            ->get();

        $hasOverdue = $overdueInvoices->isNotEmpty();
        $overdueAmount = (float) $overdueInvoices->sum('outstanding_amount');

        return [
            'credit_limit' => $creditLimit,
            'invoiced_outstanding' => $invoicedOutstanding,
            'un_invoiced_active_exposure' => $unInvoicedActiveExposure,
            'total_exposure' => $totalExposure,
            'available_credit' => $availableCredit,
            'utilization_percent' => $utilizationPercent,
            'has_overdue_invoices' => $hasOverdue,
            'overdue_count' => $overdueInvoices->count(),
            'overdue_amount' => $overdueAmount,
        ];
    }

    /**
     * Strictly validate proposed order against member's credit limit and overdue status.
     * Throws DomainException on policy breach (translated to HTTP 422 at controller).
     */
    public function validateCreditOrder(Member $member, float $orderAmount, string $paymentTerm): void
    {
        // Cash transactions (Cash Before Delivery) bypass credit checks
        if ($paymentTerm === B2bOrder::TERM_CASH) {
            return;
        }

        if ($member->type !== Member::TYPE_B2B) {
            throw new DomainException('Hanya rekanan resmi bertipe B2B yang berhak mengajukan transaksi pembayaran tempo.');
        }

        $exposure = $this->getMemberCreditExposure($member);

        // Rule 1: Zero Tolerance for Overdue Debt
        if ($exposure['has_overdue_invoices']) {
            $formattedOverdue = number_format($exposure['overdue_amount'], 0, ',', '.');
            throw new DomainException(
                "Transaksi tempo DITOLAK (System Lock): Mitra '{$member->name}' memiliki {$exposure['overdue_count']} faktur piutang yang telah jatuh tempo (Total: Rp {$formattedOverdue}). Harap selesaikan pelunasan sebelum membuat pesanan baru."
            );
        }

        // Rule 2: Hard Lock on Credit Limit Exposure
        $proposedExposure = $exposure['total_exposure'] + $orderAmount;
        if ($proposedExposure > $exposure['credit_limit']) {
            $formattedLimit = number_format($exposure['credit_limit'], 0, ',', '.');
            $formattedUsed = number_format($exposure['total_exposure'], 0, ',', '.');
            $formattedAvail = number_format($exposure['available_credit'], 0, ',', '.');
            $formattedReq = number_format($orderAmount, 0, ',', '.');

            throw new DomainException(
                "Transaksi tempo DITOLAK (Plafon Kredit Terlampaui): Nilai pesanan (Rp {$formattedReq}) melebihi sisa plafon kredit yang tersedia (Rp {$formattedAvail}). Plafon: Rp {$formattedLimit}, Terpakai: Rp {$formattedUsed}."
            );
        }
    }
}
