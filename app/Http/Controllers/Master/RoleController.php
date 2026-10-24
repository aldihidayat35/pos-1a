<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Module display labels and ordering for the Permission Matrix
     */
    public const MODULE_GROUPS = [
        'pos'         => ['title' => 'Kasir & POS Ritel', 'icon' => 'shopping-cart', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        'emergency'   => ['title' => 'Stok Emergency Toko', 'icon' => 'truck', 'color' => 'bg-amber-50 text-amber-800 border-amber-200'],
        'transfer'    => ['title' => 'Transfer Antar Lokasi', 'icon' => 'arrows-right-left', 'color' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
        'purchasing'  => ['title' => 'Operasional Gudang & PO', 'icon' => 'cube', 'color' => 'bg-blue-50 text-blue-700 border-blue-200'],
        'opname'      => ['title' => 'Stok Opname Fisik', 'icon' => 'clipboard-document-check', 'color' => 'bg-purple-50 text-purple-700 border-purple-200'],
        'b2b'         => ['title' => 'Kemitraan & Penjualan B2B', 'icon' => 'user-group', 'color' => 'bg-cyan-50 text-cyan-700 border-cyan-200'],
        'inventory'   => ['title' => 'Master Produk & Inventaris', 'icon' => 'tag', 'color' => 'bg-violet-50 text-violet-700 border-violet-200'],
        'dashboard'   => ['title' => 'Dasbor & Finansial', 'icon' => 'chart-pie', 'color' => 'bg-teal-50 text-teal-700 border-teal-200'],
        'reports'     => ['title' => 'Laporan & Ekspor Data', 'icon' => 'arrow-down-tray', 'color' => 'bg-slate-50 text-slate-700 border-slate-200'],
        'audit'       => ['title' => 'Audit & Feed Anomali (Khusus Super Admin)', 'icon' => 'shield-exclamation', 'color' => 'bg-rose-50 text-rose-700 border-rose-200'],
        'system'      => ['title' => 'Pengaturan Sistem & Pengguna', 'icon' => 'cog-6-tooth', 'color' => 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200'],
    ];

    /**
     * Display the Role & Permission Matrix view
     */
    public function index(Request $request): View
    {
        $roles = Role::withCount('users', 'permissions')->get();

        // Selected role defaults to request param or first non-super_admin role
        $selectedRoleId = $request->query('role_id');
        $selectedRole = $selectedRoleId 
            ? $roles->firstWhere('id', (int) $selectedRoleId) 
            : ($roles->firstWhere('name', '!=', 'super_admin') ?? $roles->first());

        if (!$selectedRole) {
            $selectedRole = $roles->first();
        }

        // Active permissions for the selected role
        $activePermissionIds = $selectedRole->permissions->pluck('id')->toArray();

        // Load all permissions and group them by module
        $allPermissions = Permission::orderBy('module')->orderBy('id')->get();
        $groupedPermissions = $allPermissions->groupBy('module');

        $moduleMeta = self::MODULE_GROUPS;

        return view('master.roles.index', compact(
            'roles',
            'selectedRole',
            'activePermissionIds',
            'groupedPermissions',
            'moduleMeta'
        ));
    }

    /**
     * Update permissions for a specific role
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        // 1. Anti-lockout security check: Super Admin cannot be stripped of permissions
        if ($role->name === 'super_admin') {
            return redirect()
                ->route('master.roles.index', ['role_id' => $role->id])
                ->with('error', 'Hak akses Super Administrator dilindungi oleh sistem keamanan (Anti-Lockout) dan tidak dapat dikurangi.');
        }

        // 2. Validate input
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissionIds = $validated['permissions'] ?? [];

        // 3. Sync permissions
        $role->permissions()->sync($permissionIds);

        return redirect()
            ->route('master.roles.index', ['role_id' => $role->id])
            ->with('success', "Hak akses untuk jabatan [{$role->label}] berhasil diperbarui ({$role->permissions()->count()} izin aktif). Semua pengguna dengan jabatan ini langsung mendapatkan hak akses terbaru.");
    }
}
