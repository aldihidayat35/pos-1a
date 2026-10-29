@extends('layouts.app')

@section('title', 'Suplayer & Pemasok — PasarHub')
@section('page_title', 'Master Pemasok (Suplayer)')
@section('page_subtitle', 'Pusat data mitra petani, distributor resmi, dan produsen pasokan pasar.')

@section('content')
<div class="space-y-6">
    <!-- Sub-Nav Tabs -->
    @include('master.partials.nav')

    <!-- Suppliers Table Card (Full Width) -->
    <div data-async-table="suppliers-table" class="w-full bg-white rounded-3xl border border-[#ECEBF4] overflow-hidden shadow-sm">
        <div class="p-4 sm:p-5 border-b border-[#ECEBF4] flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white">
            <div>
                <h3 class="font-bold text-sm text-textMain">Daftar Suplayer Aktif</h3>
                <p class="text-xs text-textMuted">Total {{ $suppliers->total() }} mitra pemasok terdaftar</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Search Input -->
                <div class="relative flex-1 sm:w-60">
                    <input type="text" 
                           data-table-search="suppliers-table" 
                           value="{{ request('search') }}" 
                           placeholder="Cari suplayer / kontak..." 
                           class="w-full pl-8 pr-3 py-2 text-xs bg-panelBg border border-[#E6E4F0] rounded-xl focus:bg-white focus:outline-none focus:border-sidebar transition font-medium">
                    <svg class="w-3.5 h-3.5 text-textMuted absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Per Page Selector -->
                <select data-table-perpage="suppliers-table" class="text-xs bg-panelBg border border-[#E6E4F0] px-3 py-2 rounded-xl font-bold text-sidebar focus:outline-none focus:border-sidebar transition cursor-pointer">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / hal</option>
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 / hal</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / hal</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / hal</option>
                </select>

                <!-- Tambah Suplayer Button -->
                <a href="{{ route('master.suppliers.create') }}" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Suplayer</span>
                </a>
            </div>
        </div>

            <!-- Desktop View: Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-panelBg/80 border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4">Kode</th>
                            <th class="py-3.5 px-4">Nama Suplayer</th>
                            <th class="py-3.5 px-4">Kontak & Telepon</th>
                            <th class="py-3.5 px-4">Alamat</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ECEBF4]">
                        @forelse($suppliers as $sup)
                            <tr class="hover:bg-panelBg/40 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-sidebar">{{ $sup->supplier_code }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-textMain text-sm block">{{ $sup->name }}</span>
                                    @if($sup->email)
                                        <span class="text-[11px] text-textMuted">{{ $sup->email }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-semibold text-textMain block">{{ $sup->contact_person ?: '—' }}</span>
                                    <span class="text-[11px] text-textMuted">{{ $sup->phone ?: '—' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-textMuted max-w-xs truncate">
                                    {{ $sup->address ?: '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($sup->is_active)
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if($sup->is_active)
                                        <form method="POST" action="{{ route('master.suppliers.destroy', $sup) }}" 
                                              data-confirm="Apakah Anda yakin ingin menonaktifkan suplayer [{{ $sup->name }}]?" data-confirm-title="Nonaktifkan Suplayer" data-confirm-btn="Ya, Nonaktifkan" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-textMuted hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Nonaktifkan Suplayer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-textMuted text-xs">Belum ada suplayer terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: Card Stack -->
            <div class="block md:hidden divide-y divide-[#ECEBF4]">
                @forelse($suppliers as $sup)
                    <div class="p-4 space-y-3 hover:bg-panelBg/30 transition">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="inline-block px-2 py-0.5 rounded-md font-mono text-[10px] font-bold bg-sidebar/10 text-sidebar border border-sidebar/20">
                                    {{ $sup->supplier_code }}
                                </span>
                                <h4 class="font-bold text-sm text-textMain mt-1">{{ $sup->name }}</h4>
                                @if($sup->email)
                                    <p class="text-[11px] text-textMuted">{{ $sup->email }}</p>
                                @endif
                            </div>
                            <div>
                                @if($sup->is_active)
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs bg-panelBg/60 p-2.5 rounded-xl border border-[#ECEBF4]">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-textMuted block">Kontak PIC</span>
                                <span class="font-semibold text-textMain">{{ $sup->contact_person ?: '—' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-textMuted block">Telepon / WA</span>
                                @if($sup->phone)
                                    <a href="tel:{{ $sup->phone }}" class="font-semibold text-sidebar hover:underline">{{ $sup->phone }}</a>
                                @else
                                    <span class="text-textMuted">—</span>
                                @endif
                            </div>
                            <div class="col-span-2 pt-1 border-t border-[#ECEBF4]/60">
                                <span class="text-[10px] uppercase font-bold text-textMuted block">Alamat Gudang</span>
                                <span class="text-textMuted text-[11px]">{{ $sup->address ?: '—' }}</span>
                            </div>
                        </div>

                        @if($sup->is_active)
                            <div class="flex items-center justify-end pt-1">
                                <form method="POST" action="{{ route('master.suppliers.destroy', $sup) }}" 
                                      data-confirm="Apakah Anda yakin ingin menonaktifkan suplayer [{{ $sup->name }}]?" data-confirm-title="Nonaktifkan Suplayer" data-confirm-btn="Ya, Nonaktifkan" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        Nonaktifkan
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-8 px-4 text-center text-textMuted text-xs">Belum ada suplayer terdaftar.</div>
                @endforelse
            </div>

            @if($suppliers->hasPages())
                <div class="p-4 border-t border-[#ECEBF4] bg-panelBg/30">
                    {{ $suppliers->links() }}
                </div>
            @endif
        </div>
</div>
@endsection
