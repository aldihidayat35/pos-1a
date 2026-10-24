<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users with search, role filter, and metrics
     */
    public function index(Request $request): View
    {
        $query = User::with(['roles', 'locations']);

        // Search by keyword
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role_id')) {
            $roleId = $request->role_id;
            $query->whereHas('roles', function ($q) use ($roleId) {
                $q->where('roles.id', $roleId);
            });
        }

        // Filter by status (active/inactive)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by location
        if ($request->filled('location_id')) {
            $locId = $request->location_id;
            $query->whereHas('locations', function ($q) use ($locId) {
                $q->where('locations.id', $locId);
            });
        }

        $perPage = min(max((int) $request->input('per_page', 15), 5), 100);
        $users = $query->orderBy('name')->paginate($perPage)->withQueryString();

        $roles = Role::orderBy('id')->get();
        $locations = Location::active()->orderBy('name')->get();

        $metrics = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'roles_count' => Role::count(),
        ];

        return view('master.users.index', compact('users', 'roles', 'locations', 'metrics'));
    }

    /**
     * Store a newly created user in storage
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'location_ids' => 'nullable|array',
            'location_ids.*' => 'exists:locations,id',
            'is_active' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
            'preferred_locale' => 'id',
        ]);

        // Attach primary role
        $user->roles()->sync([$validated['role_id']]);

        // Attach locations
        if (!empty($validated['location_ids'])) {
            $user->locations()->sync($validated['location_ids']);
        }

        $role = Role::find($validated['role_id']);

        return redirect()
            ->route('master.users.index')
            ->with('success', "Pengguna [{$user->name}] berhasil dibuat dan ditugaskan ke jabatan [{$role->label}].");
    }

    /**
     * Update the specified user in storage
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'location_ids' => 'nullable|array',
            'location_ids.*' => 'exists:locations,id',
            'is_active' => 'nullable|boolean',
        ]);

        // Safety: Do not allow deactivating self
        if ($user->id === auth()->id() && !$request->boolean('is_active', true)) {
            return redirect()
                ->route('master.users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang aktif digunakan login.');
        }

        // Safety: Do not demote last superadmin
        if ($user->isSuperAdmin()) {
            $newRole = Role::find($validated['role_id']);
            if ($newRole && $newRole->name !== 'super_admin') {
                $superAdminCount = User::whereHas('roles', fn($q) => $q->where('name', 'super_admin'))->where('is_active', true)->count();
                if ($superAdminCount <= 1) {
                    return redirect()
                        ->route('master.users.index')
                        ->with('error', 'Sistem harus memiliki setidaknya satu Super Administrator aktif. Jabatan tidak dapat diubah.');
                }
            }
        }

        $user->name = $validated['name'];
        $user->username = strtolower($validated['username']);
        $user->email = strtolower($validated['email']);
        $user->phone = $validated['phone'] ?? null;
        $user->is_active = $request->boolean('is_active', true);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Sync role
        $user->roles()->sync([$validated['role_id']]);

        // Sync locations
        $locations = $validated['location_ids'] ?? [];
        $user->locations()->sync($locations);

        return redirect()
            ->route('master.users.index')
            ->with('success', "Data pengguna [{$user->name}] berhasil diperbarui.");
    }

    /**
     * Toggle active/inactive status
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('master.users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        if ($user->isSuperAdmin() && $user->is_active) {
            $activeSuperAdmins = User::whereHas('roles', fn($q) => $q->where('name', 'super_admin'))->where('is_active', true)->count();
            if ($activeSuperAdmins <= 1) {
                return redirect()
                    ->route('master.users.index')
                    ->with('error', 'Tidak dapat menonaktifkan satu-satunya Super Administrator sistem.');
            }
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('master.users.index')
            ->with('success', "Akun pengguna [{$user->name}] berhasil {$statusStr}.");
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('master.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->isSuperAdmin()) {
            $superAdminCount = User::whereHas('roles', fn($q) => $q->where('name', 'super_admin'))->count();
            if ($superAdminCount <= 1) {
                return redirect()
                    ->route('master.users.index')
                    ->with('error', 'Tidak dapat menghapus satu-satunya Super Administrator sistem.');
            }
        }

        $name = $user->name;
        $user->roles()->detach();
        $user->locations()->detach();
        $user->delete();

        return redirect()
            ->route('master.users.index')
            ->with('success', "Pengguna [{$name}] berhasil dihapus dari sistem.");
    }
}
