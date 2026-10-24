<?php

namespace App\Http\Controllers\B2b;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\B2bService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class B2bInvoiceController extends Controller
{
    public function __construct(
        protected B2bService $b2bService
    ) {}

    /**
     * Display listing of Invoices & Receivables.
     */
    public function index(Request $request): View
    {
        $query = Invoice::with(['member.tier', 'order'])->latest('invoice_date');

        if ($request->filled('status')) {
            if ($request->status === 'OVERDUE') {
                $query->where('outstanding_amount', '>', 0)
                      ->where('due_date', '<', now()->toDateString());
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhereHas('member', fn($mq) => $mq->where('name', 'like', "%{$s}%"));
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        $stats = [
            'total_invoiced' => Invoice::sum('total_amount'),
            'total_paid' => Invoice::sum('paid_amount'),
            'total_outstanding' => Invoice::whereIn('status', [Invoice::STATUS_OPEN, Invoice::STATUS_PARTIALLY_PAID, Invoice::STATUS_OVERDUE])->sum('outstanding_amount'),
            'overdue_count' => Invoice::where('outstanding_amount', '>', 0)->where('due_date', '<', now()->toDateString())->count(),
        ];

        $user = $request->user();
        $activeLocation = $user->getActiveLocation();

        return view('b2b.invoices.index', compact('invoices', 'stats', 'user', 'activeLocation'));
    }

    /**
     * Display detailed invoice view.
     */
    public function show(Invoice $invoice, Request $request): View
    {
        $invoice->load(['member.tier', 'order.items.product', 'order.fulfillmentLocation', 'payments.recordedBy']);
        $user = $request->user();
        $activeLocation = $user->getActiveLocation();

        return view('b2b.invoices.show', compact('invoice', 'user', 'activeLocation'));
    }

    /**
     * Settle or record payment for an open/overdue invoice.
     */
    public function pay(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:CASH,TRANSFER,GIRO,OTHER',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $this->b2bService->payInvoice(
                $invoice,
                (float) $validated['amount'],
                $validated['payment_method'],
                $validated['reference_number'] ?? null,
                $request->user(),
                $validated['notes'] ?? null
            );

            return back()->with('success', "Pembayaran piutang faktur #{$invoice->invoice_number} sebesar Rp " . number_format($validated['amount'], 0, ',', '.') . " berhasil dicatat.");
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
