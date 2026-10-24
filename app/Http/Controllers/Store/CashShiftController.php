<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\CashShift;
use App\Models\Location;
use App\Models\Sale;
use App\Services\PosService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashShiftController extends Controller
{
    public function __construct(
        protected PosService $posService
    ) {}

    /**
     * Display cash shifts list and reconciliation dashboard
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('pos.cash_shift') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola shift laci kasir.');
        }

        $activeLocation = $user->getActiveLocation() ?? Location::where('type', 'STORE')->first();

        $query = CashShift::with(['cashier', 'location', 'validator']);

        if (!$user->isSuperAdmin() && !$user->isOwner()) {
            $query->where('location_id', $activeLocation->id);
        }

        $shifts = $query->orderByDesc('id')->paginate(10);
        $activeShift = $this->posService->getActiveShift($user, $activeLocation);

        $shiftSales = $activeShift ? Sale::where('cash_shift_id', $activeShift->id)->where('status', 'POSTED')->get() : collect();
        $activeShiftCashSales = $shiftSales->where('payment_method', 'CASH')->sum('total_amount');
        $activeShiftNonCashSales = $shiftSales->where('payment_method', '!=', 'CASH')->sum('total_amount');

        $canManage = $user->hasPermission('pos.cash_shift') || $user->isSuperAdmin();
        $isSupervisor = $user->hasRole('kepala_toko') || $user->isSuperAdmin();

        return view('store.shifts.index', compact(
            'shifts',
            'activeShift',
            'activeShiftCashSales',
            'activeShiftNonCashSales',
            'user',
            'activeLocation',
            'canManage',
            'isSupervisor'
        ));
    }

    /**
     * Open a new cash drawer shift
     */
    public function open(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('pos.cash_shift') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk membuka shift kasir.');
        }

        $validated = $request->validate([
            'starting_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $activeLocation = $user->getActiveLocation() ?? Location::where('type', 'STORE')->first();

        $shift = $this->posService->openShift(
            $activeLocation,
            $user,
            (float) $validated['starting_cash'],
            $validated['notes'] ?? null
        );

        return back()->with('success', "Shift kasir {$shift->shift_number} berhasil dibuka dengan modal awal laci Rp " . number_format($shift->starting_cash, 0, ',', '.') . ".");
    }

    /**
     * Close active shift with blind cash count
     */
    public function close(CashShift $shift, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('pos.cash_shift') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk menutup shift kasir.');
        }

        if ($shift->cashier_user_id !== $user->id && !$user->isSuperAdmin() && !$user->hasRole('kepala_toko')) {
            abort(403, 'Akses Ditolak: Anda hanya dapat menutup shift laci milik akun Anda sendiri.');
        }

        $validated = $request->validate([
            'actual_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $closedShift = $this->posService->closeShift($shift, (float) $validated['actual_cash'], $validated['notes'] ?? null);

            $diffMsg = '';
            if (abs($closedShift->difference) > 0.01) {
                $diffMsg = " Terdapat selisih kas " . ($closedShift->difference < 0 ? 'Kurang' : 'Lebih') . " sebesar Rp " . number_format(abs($closedShift->difference), 0, ',', '.') . " yang telah diteruskan ke audit.";
            } else {
                $diffMsg = " Uang fisik pas sesuai pembukuan sistem (0 selisih).";
            }

            return back()->with('success', "Shift kasir {$closedShift->shift_number} berhasil ditutup!{$diffMsg}");
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Validate closed shift by Kepala Toko / Super Admin
     */
    public function validateShift(CashShift $shift, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasRole('kepala_toko') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Kepala Toko atau Administrator yang berwenang memvalidasi penutupan shift.');
        }

        $shift->update([
            'status' => 'VALIDATED',
            'validated_by' => $user->id,
            'validated_at' => now(),
        ]);

        return back()->with('success', "Rekonsiliasi shift {$shift->shift_number} telah divalidasi sah oleh {$user->name}.");
    }
}
