@extends('layouts.app')

@section('title', 'Manajemen Pengguna — PasarHub')
@section('page_title', 'Manajemen Pengguna & Penugasan Jabatan')
@section('page_subtitle', 'Kelola akun staf, penugasan peran jabatan (RBAC), dan batas otorisasi lokasi operasional toko/gudang.')

@section('content')
<div class="space-y-6">
    <!-- Top Context & Global Flash Messages -->
    @include('master.partials.nav')

    <!-- RBAC Sub-Tabs Switcher (Daftar Pengguna vs Matriks Hak Akses) -->
    <div class="flex items-center gap-2 p-1.5 bg-panelBg rounded-2xl border border-[#ECEBF4] w-fit">
        <a href="{{ route('master.users.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-sidebar text-white shadow-sm">
            <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>Daftar Pengguna ({{ $metrics['total'] }})</span>
        </a>
        <a href="{{ route('master.roles.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 text-textMuted hover:text-sidebar hover:bg-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span>Matriks Hak Akses Role (Centang-Centang)</span>
        </a>
    </div>

    <!-- Metric Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-panelBg p-4 sm:p-5 rounded-2xl border border-[#ECEBF4] flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-textMuted uppercase tracking-wider block">Total Pengguna</span>
                <h3 class="text-xl sm:text-2xl font-black text-sidebar mt-0.5">{{ number_format($metrics['total']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sidebar/10 text-sidebar flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-panelBg p-4 sm:p-5 rounded-2xl border border-[#ECEBF4] flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-textMuted uppercase tracking-wider block">Akun Aktif</span>
                <h3 class="text-xl sm:text-2xl font-black text-emerald-600 mt-0.5">{{ number_format($metrics['active']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-panelBg p-4 sm:p-5 rounded-2xl border border-[#ECEBF4] flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-textMuted uppercase tracking-wider block">Nonaktif / Terkunci</span>
                <h3 class="text-xl sm:text-2xl font-black text-rose-600 mt-0.5">{{ number_format($metrics['inactive']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
        </div>

        <div class="bg-panelBg p-4 sm:p-5 rounded-2xl border border-[#ECEBF4] flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-textMuted uppercase tracking-wider block">Jabatan / Role</span>
                <h3 class="text-xl sm:text-2xl font-black text-brandOrange mt-0.5">{{ number_format($metrics['roles_count']) }} <span class="text-xs font-normal text-textMuted">Tipe</span></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-brandOrange/10 text-brandOrange flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Actions & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
        <form method="GET" action="{{ route('master.users.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto flex-1">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-[200px]">
                <svg class="w-4 h-4 text-textMuted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama, username, email, telepon..." 
                       class="w-full pl-10 pr-4 py-2 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
            </div>

            <!-- Role Filter -->
            <select name="role_id" onchange="this.form.submit()" class="px-3 py-2 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                <option value="">Semua Jabatan / Role</option>
                @foreach($roles as $r)
                    <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>{{ $r->label }}</option>
                @endforeach
            </select>

            <!-- Status Filter -->
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <!-- Location Filter -->
            <select name="location_id" onchange="this.form.submit()" class="px-3 py-2 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                <option value="">Semua Lokasi</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                @endforeach
            </select>

            @if(request()->hasAny(['search', 'role_id', 'status', 'location_id']))
                <a href="{{ route('master.users.index') }}" class="px-3 py-2 bg-panelBg hover:bg-slate-200 text-textMuted rounded-xl text-xs font-bold transition">
                    Reset
                </a>
            @endif
        </form>

        <button type="button" onclick="openCreateUserModal()" 
                class="w-full md:w-auto px-4 py-2.5 bg-sidebar hover:bg-sidebar/95 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center justify-center gap-2 shrink-0">
            <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Pengguna Baru</span>
        </button>
    </div>

    <!-- Users Table Container -->
    <div class="bg-white rounded-3xl border border-[#ECEBF4] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-panelBg/70 border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Kontak (Email / Telepon)</th>
                        <th class="py-3 px-4">Jabatan / Role</th>
                        <th class="py-3 px-4">Akses Lokasi Toko / Gudang</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ECEBF4]">
                    @forelse($users as $u)
                        @php
                            $role = $u->primaryRole();
                            $isSelf = auth()->id() === $u->id;
                        @endphp
                        <tr class="hover:bg-panelBg/30 transition">
                            <!-- Name & Avatar -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-sidebar text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-textMain text-xs flex items-center gap-1.5">
                                            <span class="truncate">{{ $u->name }}</span>
                                            @if($isSelf)
                                                <span class="px-1.5 py-0.2 rounded-md bg-sidebar/10 text-sidebar text-[9px] font-bold">Anda</span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-textMuted font-mono block">@<span>{{ $u->username ?: '-' }}</span></span>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td class="py-3.5 px-4">
                                <div class="space-y-0.5">
                                    <span class="text-textMain font-medium block">{{ $u->email }}</span>
                                    <span class="text-[10px] text-textMuted font-mono block">{{ $u->phone ?: 'Tidak ada telepon' }}</span>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td class="py-3.5 px-4">
                                @if($role)
                                    @php
                                        $roleColor = match($role->name) {
                                            'super_admin' => 'bg-sidebar text-white',
                                            'owner' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                            'kepala_toko' => 'bg-amber-100 text-amber-800 border-amber-300',
                                            'staf_toko' => 'bg-cyan-100 text-cyan-800 border-cyan-300',
                                            'kepala_gudang' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                                            'staf_gudang' => 'bg-blue-100 text-blue-800 border-blue-300',
                                            'sales' => 'bg-purple-100 text-purple-800 border-purple-300',
                                            'b2b_mitra' => 'bg-slate-100 text-slate-800 border-slate-300',
                                            default => 'bg-panelBg text-textMain border-[#ECEBF4]',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold border {{ $roleColor }} inline-block">
                                        {{ $role->label }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                        Tanpa Role
                                    </span>
                                @endif
                            </td>

                            <!-- Locations -->
                            <td class="py-3.5 px-4">
                                @if($u->isSuperAdmin() || $u->isOwner())
                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">
                                        🌍 Seluruh Lokasi (Global)
                                    </span>
                                @else
                                    <div class="flex flex-wrap gap-1 max-w-[240px]">
                                        @forelse($u->locations as $loc)
                                            <span class="px-2 py-0.5 rounded-lg bg-panelBg border border-[#ECEBF4] text-[10px] font-semibold text-textMain">
                                                {{ $loc->name }}
                                            </span>
                                        @empty
                                            <span class="text-[10px] text-amber-700 font-medium">⚠️ Belum ada lokasi ditugaskan</span>
                                        @endforelse
                                    </div>
                                @endif
                            </td>

                            <!-- Status Toggle -->
                            <td class="py-3.5 px-4 text-center">
                                @if($isSelf)
                                    <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px]">
                                        Aktif
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('master.users.toggle_status', $u) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                title="Klik untuk {{ $u->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun"
                                                class="px-2.5 py-1 rounded-xl text-[10px] font-bold transition {{ $u->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200' : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200' }}">
                                            {{ $u->is_active ? '● Aktif' : '○ Nonaktif' }}
                                        </button>
                                    </form>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                            onclick='openEditUserModal(@json($u), {{ $role ? $role->id : "null" }}, @json($u->locations->pluck("id")))'
                                            class="p-1.5 rounded-lg bg-panelBg hover:bg-sidebar hover:text-white text-textMuted transition" title="Edit Pengguna">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    @if(!$isSelf)
                                        <form method="POST" action="{{ route('master.users.destroy', $u) }}" class="inline" data-confirm="Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}? Tindakan ini tidak dapat dibatalkan." data-confirm-title="Hapus Pengguna" data-confirm-btn="Ya, Hapus">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-panelBg hover:bg-rose-600 hover:text-white text-textMuted transition" title="Hapus Pengguna">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-xs text-textMuted">
                                Tidak ada data pengguna yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-[#ECEBF4] bg-panelBg/40">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ============================================================
     MODAL: TAMBAH PENGGUNA BARU
     ============================================================ -->
<div id="createUserModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-[#ECEBF4] max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
            <div>
                <h3 class="font-display font-bold text-base text-textMain">Tambah Pengguna Baru</h3>
                <p class="text-xs text-textMuted mt-0.5">Daftarkan akun staf dan pilih peran jabatan (Role RBAC).</p>
            </div>
            <button type="button" onclick="closeCreateUserModal()" class="text-textMuted hover:text-textMain p-1 rounded-lg">✕</button>
        </div>

        <form method="POST" action="{{ route('master.users.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-textMain mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                           class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain mb-1">Username Login <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" required placeholder="budi_kasir"
                           class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-mono font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" placeholder="08123456789"
                           class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-mono font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-bold text-textMain mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="budi@pasar.local"
                           class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-bold text-textMain mb-1">Kata Sandi (Password) <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter"
                           class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-mono font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-bold text-textMain mb-1">Jabatan / Role Operasional <span class="text-rose-500">*</span></label>
                    <select name="role_id" required class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                        <option value="">-- Pilih Jabatan Pengguna --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->label }} ({{ $r->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-bold text-textMain mb-1">Penugasan Lokasi Operasional</label>
                    <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto p-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs">
                        @foreach($locations as $loc)
                            <label class="flex items-center gap-2 cursor-pointer hover:text-sidebar">
                                <input type="checkbox" name="location_ids[]" value="{{ $loc->id }}" class="rounded text-sidebar focus:ring-sidebar">
                                <span class="truncate">{{ $loc->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <span class="text-[10px] text-textMuted mt-1 block">Catatan: Super Admin & Owner otomatis memiliki akses ke seluruh lokasi.</span>
                </div>

                <div class="col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer mt-1">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-sidebar focus:ring-sidebar">
                        <span class="text-xs font-bold text-textMain">Akun Aktif (Dapat Login)</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#ECEBF4]">
                <button type="button" onclick="closeCreateUserModal()" class="px-4 py-2 bg-panelBg hover:bg-slate-200 text-textMuted font-bold text-xs rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl shadow transition">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     MODAL: EDIT PENGGUNA
     ============================================================ -->
<div id="editUserModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-[#ECEBF4] max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
            <div>
                <h3 class="font-display font-bold text-base text-textMain">Edit Akun Pengguna</h3>
                <p class="text-xs text-textMuted mt-0.5">Ubah rincian profil, jabatan role, dan penugasan lokasi.</p>
            </div>
            <button type="button" onclick="closeEditUserModal()" class="text-textMuted hover:text-textMain p-1 rounded-lg">✕</button>
        </div>

        <form id="editUserForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-textMain mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="edit_name" required
                           class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain mb-1">Username Login <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" id="edit_username" required
                           class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-mono font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain mb-1">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" id="edit_phone"
                           class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-mono font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-bold text-textMain mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" id="edit_email" required
                           class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-bold text-textMain mb-1">Ganti Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" minlength="6" placeholder="Biarkan kosong jika tetap menggunakan sandi lama"
                           class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-mono font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-bold text-textMain mb-1">Jabatan / Role Operasional <span class="text-rose-500">*</span></label>
                    <select name="role_id" id="edit_role_id" required class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->label }} ({{ $r->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-xs font-bold text-textMain mb-1">Penugasan Lokasi Operasional</label>
                    <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto p-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs" id="editLocationsContainer">
                        @foreach($locations as $loc)
                            <label class="flex items-center gap-2 cursor-pointer hover:text-sidebar">
                                <input type="checkbox" name="location_ids[]" value="{{ $loc->id }}" class="edit-loc-cb rounded text-sidebar focus:ring-sidebar">
                                <span class="truncate">{{ $loc->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer mt-1">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded text-sidebar focus:ring-sidebar">
                        <span class="text-xs font-bold text-textMain">Akun Aktif (Dapat Login)</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#ECEBF4]">
                <button type="button" onclick="closeEditUserModal()" class="px-4 py-2 bg-panelBg hover:bg-slate-200 text-textMuted font-bold text-xs rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl shadow transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateUserModal() {
        document.getElementById('createUserModal').classList.remove('hidden');
    }
    function closeCreateUserModal() {
        document.getElementById('createUserModal').classList.add('hidden');
    }

    function openEditUserModal(user, roleId, locationIds) {
        document.getElementById('editUserForm').action = `/master/users/${user.id}`;
        document.getElementById('edit_name').value = user.name || '';
        document.getElementById('edit_username').value = user.username || '';
        document.getElementById('edit_email').value = user.email || '';
        document.getElementById('edit_phone').value = user.phone || '';
        document.getElementById('edit_is_active').checked = Boolean(user.is_active);

        if (roleId) {
            document.getElementById('edit_role_id').value = roleId;
        }

        // Set locations checkboxes
        const locSet = new Set(locationIds || []);
        document.querySelectorAll('.edit-loc-cb').forEach(cb => {
            cb.checked = locSet.has(parseInt(cb.value));
        });

        document.getElementById('editUserModal').classList.remove('hidden');
    }
    function closeEditUserModal() {
        document.getElementById('editUserModal').classList.add('hidden');
    }
</script>
@endsection
