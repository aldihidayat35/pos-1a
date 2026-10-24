<?php

namespace App\Http\Controllers\B2b;

use App\Http\Controllers\Controller;
use App\Models\B2bOrder;
use App\Models\Location;
use App\Models\Member;
use App\Models\Product;
use App\Services\B2bService;
use App\Services\CreditPolicyService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class B2bOrderController extends Controller
{
    public function __construct(
        protected B2bService $b2bService,
        protected CreditPolicyService $creditPolicyService
    ) {}

    /**
     * Display listing of B2B Orders.
     */
    public function index(Request $request): View
    {
        $query = B2bOrder::with(['member.tier', 'fulfillmentLocation', 'creator', 'pickupTicket'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_term')) {
            $query->where('payment_term', $request->payment_term);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('order_number', 'like', "%{$s}%")
                  ->orWhereHas('member', fn($mq) => $mq->where('name', 'like', "%{$s}%"));
            });
        }

        $orders = $query->paginate(15)->withQueryString();
        $user = $request->user();
        $activeLocation = $user->getActiveLocation();

        return view('b2b.orders.index', compact('orders', 'user', 'activeLocation'));
    }

    /**
     * Show form for creating a new B2B Order.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $activeLocation = $user->getActiveLocation();

        $b2bMembers = Member::where('type', Member::TYPE_B2B)
            ->where('is_active', true)
            ->with('tier')
            ->orderBy('name')
            ->get();

        $locations = Location::where('type', '!=', Location::TYPE_TRANSIT)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = Product::where('is_active', true)
            ->with(['baseUom', 'uoms.uom', 'balances'])
            ->orderBy('name')
            ->get();

        return view('b2b.orders.create', compact('user', 'activeLocation', 'b2bMembers', 'locations', 'products'));
    }

    /**
     * Store a newly created B2B Order.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'fulfillment_location_id' => 'required|exists:locations,id',
            'payment_term' => 'required|in:CASH,NET_7,NET_14,NET_30',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_uom_id' => 'nullable|exists:product_uoms,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        try {
            $result = $this->b2bService->createOrder($validated, $request->user());
            $order = $result['order'];

            return redirect()->route('b2b.orders.show', $order)
                ->with('success', "Pesanan B2B #{$order->order_number} berhasil dibuat! Stok telah direservasi dan Tiket QR telah diterbitkan.");
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display specific B2B Order with status stepper and QR Ticket.
     */
    public function show(B2bOrder $order, Request $request): View
    {
        $order->load([
            'member.tier',
            'fulfillmentLocation',
            'creator',
            'items.product.baseUom',
            'items.productUom.uom',
            'pickupTicket.redeemedBy',
            'invoice.payments',
        ]);

        $user = $request->user();
        $activeLocation = $user->getActiveLocation();

        return view('b2b.orders.show', compact('order', 'user', 'activeLocation'));
    }

    /**
     * Cancel an active order and release reservations.
     */
    public function cancel(Request $request, B2bOrder $order): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|min:3|max:255',
        ]);

        try {
            $this->b2bService->cancelOrder($order, $request->user(), $request->reason);
            return back()->with('success', "Pesanan #{$order->order_number} berhasil dibatalkan dan seluruh stok reservasi telah dilepas.");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * AJAX Endpoint to fetch member's real-time credit exposure.
     */
    public function creditCheck(Member $member): JsonResponse
    {
        $exposure = $this->creditPolicyService->getMemberCreditExposure($member);
        return response()->json([
            'success' => true,
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'code' => $member->member_code,
                'tier' => $member->tier?->name ?? 'NON-TIER',
                'discount_percent' => (float) ($member->tier?->discount_percent ?? 0),
            ],
            'exposure' => $exposure,
        ]);
    }
}
