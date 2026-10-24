<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Services\StockOpnameService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockOpnameController extends Controller
{
    public function __construct(
        protected StockOpnameService $opnameService
    ) {}

    /**
     * Display list of Stock Opname sessions
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasAnyPermission(['opname.blind_count', 'opname.reconcile', 'opname.finalize']) && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses modul Stock Opname.');
        }

        $activeLocation = $user->getActiveLocation();
        $query = StockOpname::with(['location', 'openedBy', 'finalizedBy', 'category']);

        // Filter by location
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        } elseif ($activeLocation) {
            $query->where('location_id', $activeLocation->id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $opnames = $query->orderByDesc('id')->paginate(10)->withQueryString();

        // Calculate summary counters
        $statsLocationId = $request->filled('location_id') ? $request->location_id : ($activeLocation?->id);
        $baseStatQuery = StockOpname::query();
        if ($statsLocationId) {
            $baseStatQuery->where('location_id', $statsLocationId);
        }

        $stats = [
            'total' => (clone $baseStatQuery)->count(),
            'active' => (clone $baseStatQuery)->whereIn('status', [StockOpname::STATUS_OPEN, StockOpname::STATUS_COUNTING, StockOpname::STATUS_RECONCILIATION])->count(),
            'finalized' => (clone $baseStatQuery)->where('status', StockOpname::STATUS_FINALIZED)->count(),
            'total_loss' => (clone $baseStatQuery)->where('status', StockOpname::STATUS_FINALIZED)->sum('total_loss_value'),
        ];

        $locations = Location::where('is_active', true)->whereIn('type', ['WAREHOUSE', 'STORE'])->get();

        return view('opname.index', compact('user', 'activeLocation', 'opnames', 'stats', 'locations'));
    }

    /**
     * Show form to create a new Stock Opname session
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasAnyPermission(['opname.reconcile', 'opname.finalize']) && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Kepala Toko atau Kepala Gudang yang dapat membuka sesi Opname baru.');
        }

        $activeLocation = $user->getActiveLocation();
        $locations = Location::where('is_active', true)->whereIn('type', ['WAREHOUSE', 'STORE'])->get();
        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->where('track_stock', true)->orderBy('name')->get();

        return view('opname.create', compact('user', 'activeLocation', 'locations', 'categories', 'products'));
    }

    /**
     * Store new Stock Opname session
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasAnyPermission(['opname.reconcile', 'opname.finalize']) && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk membuka sesi Stock Opname.');
        }

        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'scope_type' => 'required|in:ALL,CATEGORY,CUSTOM',
            'category_id' => 'nullable|required_if:scope_type,CATEGORY|exists:product_categories,id',
            'product_ids' => 'nullable|required_if:scope_type,CUSTOM|array',
            'product_ids.*' => 'exists:products,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $location = Location::findOrFail($request->location_id);

        try {
            $opname = $this->opnameService->createSession($location, $user, [
                'scope_type' => $request->scope_type,
                'category_id' => $request->category_id,
                'product_ids' => $request->product_ids ?? [],
                'notes' => $request->notes,
            ]);

            return redirect()->route('opname.show', $opname->id)
                ->with('success', "Sesi Stock Opname #{$opname->opname_number} berhasil dibuka. Silakan lakukan penghitungan fisik (Blind Count).");
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display detail of a Stock Opname session
     */
    public function show(StockOpname $opname, Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasAnyPermission(['opname.blind_count', 'opname.reconcile', 'opname.finalize']) && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin melihat sesi ini.');
        }

        // If user is only a blind counter (staff) and session is in open/counting, send directly to blind count screen
        $canReconcile = $user->hasPermission('opname.reconcile') || $user->isSuperAdmin() || $user->isOwner();
        if (!$canReconcile && $opname->isOpen()) {
            return redirect()->route('opname.count', $opname->id);
        }

        $opname->load(['location', 'openedBy', 'finalizedBy', 'category', 'items.product.baseUom', 'items.counter']);

        return view('opname.show', compact('user', 'opname', 'canReconcile'));
    }

    /**
     * Blind count screen for staff (Does NOT render system quantity!)
     */
    public function count(StockOpname $opname, Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('opname.blind_count') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk menginput hitungan fisik (Blind Count).');
        }

        $opname->load(['location', 'items.product.baseUom', 'items.counter']);

        return view('opname.count', compact('user', 'opname'));
    }

    /**
     * Save physical count submitted by staff
     */
    public function saveCount(StockOpname $opname, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('opname.blind_count') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk menginput hitungan fisik.');
        }

        $request->validate([
            'counts' => 'required|array',
            'counts.*' => 'nullable|numeric|min:0',
        ]);

        try {
            $this->opnameService->recordCount($opname, $user, $request->counts);

            if ($request->has('proceed_to_reconcile') && ($user->hasPermission('opname.reconcile') || $user->isSuperAdmin())) {
                $this->opnameService->moveToReconciliation($opname, $user);
                return redirect()->route('opname.reconcile', $opname->id)
                    ->with('success', 'Hasil hitung fisik tersimpan. Sesi kini beralih ke tahap Rekonsiliasi & Input Alasan Selisih.');
            }

            return back()->with('success', 'Hasil penghitungan fisik berhasil disimpan ke sistem.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reconciliation view for supervisor/head (Shows expected vs counted, variance, reason selector)
     */
    public function reconcile(StockOpname $opname, Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('opname.reconcile') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Kepala Toko atau Kepala Gudang yang dapat mengakses layar Rekonsiliasi Selisih.');
        }

        if ($opname->isOpen()) {
            $this->opnameService->moveToReconciliation($opname, $user);
        }

        $opname->load(['location', 'items.product.baseUom', 'items.counter']);
        $reasonLabels = StockOpnameItem::getReasonLabels();

        return view('opname.reconcile', compact('user', 'opname', 'reasonLabels'));
    }

    /**
     * Save reconciliation reasons
     */
    public function saveReconcile(StockOpname $opname, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('opname.reconcile') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'reasons' => 'required|array',
            'reasons.*.reason_code' => 'required|string',
            'reasons.*.notes' => 'nullable|string|max:255',
        ]);

        try {
            $this->opnameService->saveReconcileReasons($opname, $request->reasons);
            return back()->with('success', 'Alasan selisih opname berhasil disimpan.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Finalize Stock Opname (Atomic adjustment & Red movement posting)
     */
    public function finalize(StockOpname $opname, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('opname.finalize') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk memfinalisasi Stock Opname.');
        }

        if ($user->isOwner()) {
            abort(403, 'Akses Ditolak: Owner hanya memiliki hak akses read-only dan tidak diizinkan memposting opname.');
        }

        $request->validate([
            'final_notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->opnameService->finalize($opname, $user, $request->final_notes);
            return redirect()->route('opname.show', $opname->id)
                ->with('success', "Sesi Stock Opname #{$opname->opname_number} berhasil difinalisasi. Saldo inventaris telah disesuaikan dan mutasi audit telah tercatat.");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel session
     */
    public function cancel(StockOpname $opname, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('opname.finalize') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'cancel_reason' => 'required|string|max:255',
        ]);

        try {
            $this->opnameService->cancelSession($opname, $user, $request->cancel_reason);
            return redirect()->route('opname.index')->with('success', "Sesi Opname #{$opname->opname_number} telah dibatalkan.");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
