@extends('layouts.app')

@section('title', 'Matriks Hak Akses Role — PasarHub')
@section('page_title', 'Matriks Hak Akses & Perizinan Jabatan (RBAC)')
@section('page_subtitle', 'Atur hak akses setiap jabatan secara visual menggunakan sistem centang-centang untuk membatasi atau membuka fitur operasional.')

@section('content')
<div class="space-y-6">
    <!-- Top Context & Global Flash Messages -->
    @include('master.partials.nav')

    <!-- RBAC Sub-Tabs Switcher (Daftar Pengguna vs Matriks Hak Akses) -->
    <div class="flex items-center gap-2 p-1.5 bg-panelBg rounded-2xl border border-[#ECEBF4] w-fit">
        <a href="{{ route('master.users.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 text-textMuted hover:text-sidebar hover:bg-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>Daftar Pengguna</span>
        </a>
        <a href="{{ route('master.roles.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-sidebar text-white shadow-sm">
            <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span>Matriks Hak Akses Role (Centang-Centang)</span>
        </a>
    </div>

    <!-- Main Layout: Left Role Selector (Col-4) & Right Permission Matrix (Col-8) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left 4 Cols: Role Cards Selector -->
        <div class="lg:col-span-4 space-y-3">
            <div class="bg-white rounded-3xl border border-[#ECEBF4] p-4 shadow-sm space-y-2.5">
                <div class="flex items-center justify-between pb-2 border-b border-[#ECEBF4]">
                    <span class="text-xs font-bold text-textMain uppercase tracking-wider">Pilih Jabatan / Role:</span>
                    <span class="text-[10px] text-textMuted font-mono">{{ $roles->count() }} Role</span>
                </div>

                <div class="space-y-1.5">
                    @foreach($roles as $r)
                        @php
                            $isSelected = $selectedRole->id === $r->id;
                            $isSuperAdmin = $r->name === 'super_admin';
                        @endphp
                        <a href="{{ route('master.roles.index', ['role_id' => $r->id]) }}" 
                           class="p-3 rounded-2xl border transition flex items-center justify-between gap-2.5 block {{ $isSelected ? 'bg-sidebar text-white border-sidebar shadow-md' : 'bg-panelBg/70 border-[#ECEBF4] hover:bg-white text-textMain hover:border-sidebar' }}">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <h4 class="font-bold text-xs truncate leading-snug {{ $isSelected ? 'text-white' : 'text-textMain' }}">{{ $r->label }}</h4>
                                    @if($isSuperAdmin)
                                        <span class="text-[9px] px-1.5 py-0.2 rounded-md font-extrabold bg-brandOrange text-white uppercase shrink-0">Root</span>
                                    @endif
                                </div>
                                <p class="text-[10px] line-clamp-1 mt-0.5 {{ $isSelected ? 'text-white/70' : 'text-textMuted' }}">{{ $r->description }}</p>
                            </div>

                            <div class="flex flex-col items-end gap-1 shrink-0 text-right">
                                <span class="px-2 py-0.5 rounded-lg text-[9px] font-bold font-mono {{ $isSelected ? 'bg-white/20 text-white' : 'bg-white text-sidebar border border-[#ECEBF4]' }}">
                                    {{ $r->permissions_count }} Izin
                                </span>
                                <span class="text-[9px] {{ $isSelected ? 'text-white/70' : 'text-textMuted' }}">
                                    {{ $r->users_count }} Akun
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right 8 Cols: Interactive Permission Checkbox Matrix -->
        <div class="lg:col-span-8 space-y-4">
            <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 sm:p-6 shadow-sm space-y-5">
                
                <!-- Role Header & Quick Tools -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-[#ECEBF4]">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-sidebar text-white uppercase tracking-wider">
                                {{ $selectedRole->label }}
                            </span>
                            <span class="text-xs text-textMuted font-mono">({{ $selectedRole->name }})</span>
                        </div>
                        <p class="text-xs text-textMuted mt-1">
                            {{ $selectedRole->description }}
                        </p>
                    </div>

                    @if($selectedRole->name !== 'super_admin')
                        <!-- Quick Check / Uncheck Actions -->
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" onclick="toggleAllCheckboxes(true)" 
                                    class="px-3 py-1.5 bg-panelBg hover:bg-slate-200 text-sidebar font-bold text-[11px] rounded-xl transition border border-[#ECEBF4]">
                                Centang Semua
                            </button>
                            <button type="button" onclick="toggleAllCheckboxes(false)" 
                                    class="px-3 py-1.5 bg-panelBg hover:bg-slate-200 text-textMuted font-bold text-[11px] rounded-xl transition border border-[#ECEBF4]">
                                Batalkan Semua
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Anti-lockout Protection Alert for Super Admin -->
                @if($selectedRole->name === 'super_admin')
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-3 shadow-xs">
                        <div class="w-8 h-8 rounded-xl bg-brandOrange text-white flex items-center justify-center font-bold text-sm shrink-0">
                            🔒
                        </div>
                        <div>
                            <span class="font-bold text-amber-950 block">Proteksi Anti-Lockout Sistem Aktif:</span>
                            <span class="text-[11px] text-amber-800">Jabatan Super Administrator selalu memiliki hak akses penuh ke seluruh 23 modul sistem tanpa batasan demi menjamin keutuhan dan pemulihan sistem.</span>
                        </div>
                    </div>
                @endif

                <!-- Permission Checkboxes Form -->
                <form method="POST" action="{{ route('master.roles.update', $selectedRole) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        @foreach($groupedPermissions as $moduleKey => $permissions)
                            @php
                                $meta = $moduleMeta[$moduleKey] ?? [
                                    'title' => strtoupper($moduleKey),
                                    'color' => 'bg-panelBg text-textMain border-[#ECEBF4]'
                                ];
                                $totalInModule = count($permissions);
                                $activeInModule = $permissions->whereIn('id', $activePermissionIds)->count();
                            @endphp

                            <div class="rounded-2xl border border-[#ECEBF4] overflow-hidden bg-panelBg/30">
                                <!-- Module Header Bar -->
                                <div class="px-4 py-2.5 bg-panelBg border-b border-[#ECEBF4] flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full {{ $activeInModule === $totalInModule ? 'bg-emerald-500' : ($activeInModule > 0 ? 'bg-amber-500' : 'bg-slate-300') }}"></span>
                                        <h3 class="font-bold text-xs text-textMain">{{ $meta['title'] }}</h3>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-mono text-textMuted font-bold">
                                            {{ $activeInModule }} / {{ $totalInModule }} Aktif
                                        </span>
                                        @if($selectedRole->name !== 'super_admin')
                                            <button type="button" onclick="checkModule('{{ $moduleKey }}')" class="text-[10px] font-bold text-sidebar hover:underline ml-1">
                                                Pilih Modul Ini
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Checkbox Grid for this Module -->
                                <div class="p-3.5 grid grid-cols-1 md:grid-cols-2 gap-2.5 bg-white">
                                    @foreach($permissions as $perm)
                                        @php
                                            $isChecked = in_array($perm->id, $activePermissionIds) || $selectedRole->name === 'super_admin';
                                        @endphp
                                        <label class="p-2.5 rounded-xl border border-[#ECEBF4] hover:border-sidebar hover:bg-panelBg/40 transition flex items-start gap-3 cursor-pointer group {{ $isChecked ? 'bg-panelBg/60 border-sidebar/40' : 'bg-white' }}">
                                            <input type="checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $perm->id }}"
                                                   data-module="{{ $moduleKey }}"
                                                   class="perm-checkbox mt-0.5 w-4 h-4 rounded text-sidebar focus:ring-sidebar border-[#ECEBF4] transition cursor-pointer"
                                                   {{ $isChecked ? 'checked' : '' }}
                                                   {{ $selectedRole->name === 'super_admin' ? 'disabled' : '' }}>
                                            
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center justify-between gap-1">
                                                    <span class="font-bold text-xs text-textMain group-hover:text-sidebar transition leading-tight">
                                                        {{ $perm->label }}
                                                    </span>
                                                </div>
                                                <span class="text-[10px] text-textMuted font-mono block mt-0.5">
                                                    {{ $perm->name }}
                                                </span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($selectedRole->name !== 'super_admin')
                        <!-- Floating/Sticky Save Button Bar -->
                        <div class="pt-4 border-t border-[#ECEBF4] flex items-center justify-between gap-4">
                            <span class="text-xs text-textMuted">
                                Perubahan akan langsung berdampak pada <strong>{{ $selectedRole->users_count }} pengguna</strong> dengan jabatan ini.
                            </span>
                            <button type="submit" class="px-6 py-3 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                                <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Simpan Perubahan Hak Akses</span>
                            </button>
                        </div>
                    @endif
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    function toggleAllCheckboxes(check) {
        document.querySelectorAll('.perm-checkbox:not(:disabled)').forEach(cb => {
            cb.checked = check;
        });
    }

    function checkModule(moduleKey) {
        const cbs = Array.from(document.querySelectorAll(`.perm-checkbox[data-module="${moduleKey}"]:not(:disabled)`));
        const allChecked = cbs.every(cb => cb.checked);
        cbs.forEach(cb => cb.checked = !allChecked);
    }
</script>
@endsection
