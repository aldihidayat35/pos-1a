@extends('layouts.app')

@section('title', 'Kategori Produk — PasarHub')
@section('page_title', 'Kategori Master Produk')
@section('page_subtitle', 'Strukturisasi pengelompokan komoditas pasar, sembako, dan aneka dagangan.')

@section('content')
<div class="space-y-6">
    <!-- Sub-Nav Tabs -->
    @include('master.partials.nav')

    <!-- Categories Table Card (Full Width) -->
    <div data-async-table="categories-table" class="w-full bg-white rounded-3xl border border-[#ECEBF4] overflow-hidden shadow-sm">
        <!-- Table Header Controls: Search, Per Page, & Tambah Button -->
        <div class="p-4 sm:p-5 border-b border-[#ECEBF4] flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white">
            <div>
                <h3 class="font-bold text-sm text-textMain">Daftar Kategori Terdaftar</h3>
                <p class="text-xs text-textMuted">Total {{ $categories->total() }} kategori produk</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Live Search Input -->
                <div class="relative flex-1 sm:w-60">
                    <input type="text" 
                           data-table-search="categories-table" 
                           value="{{ request('search') }}" 
                           placeholder="Cari kode, nama, deskripsi..." 
                           class="w-full pl-8 pr-3 py-2 text-xs bg-panelBg border border-[#E6E4F0] rounded-xl focus:bg-white focus:outline-none focus:border-sidebar transition font-medium">
                    <svg class="w-3.5 h-3.5 text-textMuted absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <!-- Per Page Selector -->
                <select data-table-perpage="categories-table" class="text-xs bg-panelBg border border-[#E6E4F0] px-3 py-2 rounded-xl font-bold text-sidebar focus:outline-none focus:border-sidebar transition cursor-pointer">
                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10 / hal</option>
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 / hal</option>
                    <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25 / hal</option>
                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50 / hal</option>
                </select>

                <!-- Tambah Kategori Button -->
                <a href="{{ route('master.categories.create') }}" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Kategori</span>
                </a>
            </div>
        </div>

            <!-- DESKTOP VIEW: Structured Clean Table (hidden on mobile) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-panelBg/80 border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4">Kode</th>
                            <th class="py-3.5 px-4">Nama Kategori</th>
                            <th class="py-3.5 px-4">Kategori Induk</th>
                            <th class="py-3.5 px-4 text-center">Jumlah Produk</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ECEBF4]">
                        @forelse($categories as $cat)
                            <tr class="hover:bg-panelBg/40 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-sidebar">{{ $cat->code }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-textMain text-sm block">{{ $cat->name }}</span>
                                    @if($cat->description)
                                        <span class="text-[11px] text-textMuted">{{ $cat->description }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-textMuted">
                                    {{ $cat->parent?->name ?? '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-sidebar/5 text-sidebar border border-sidebar/10">
                                        {{ $cat->products_count }} SKU
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($cat->is_active)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <form method="POST" action="{{ route('master.categories.destroy', $cat) }}" 
                                          data-confirm="Apakah Anda yakin ingin menghapus kategori [{{ $cat->name }}]?" data-confirm-title="Hapus Kategori" data-confirm-btn="Ya, Hapus" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-textMuted hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Kategori">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-textMuted text-xs">Belum ada kategori terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- MOBILE VIEW: Card Stack Layout (visible on mobile only) -->
            <div class="block md:hidden p-3.5 space-y-3">
                @forelse($categories as $cat)
                    <div class="bg-panelBg/40 border border-[#ECEBF4] rounded-2xl p-4 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-mono font-bold text-xs text-sidebar bg-sidebar/5 px-2 py-0.5 rounded border border-sidebar/10">{{ $cat->code }}</span>
                                <h4 class="font-bold text-textMain text-sm mt-1">{{ $cat->name }}</h4>
                                @if($cat->description)
                                    <p class="text-[11px] text-textMuted mt-0.5">{{ $cat->description }}</p>
                                @endif
                            </div>
                            <div>
                                @if($cat->is_active)
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs pt-2 border-t border-[#ECEBF4]">
                            <div>
                                <span class="text-[10px] text-textMuted block uppercase font-bold">Kategori Induk</span>
                                <span class="font-semibold text-textMain">{{ $cat->parent?->name ?? '— (Root)' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-textMuted block uppercase font-bold">Produk Terkait</span>
                                <span class="font-semibold text-sidebar">{{ $cat->products_count }} Base SKU</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-[#ECEBF4] flex justify-end">
                            <form method="POST" action="{{ route('master.categories.destroy', $cat) }}" 
                                  data-confirm="Apakah Anda yakin ingin menghapus kategori [{{ $cat->name }}]?" data-confirm-title="Hapus Kategori" data-confirm-btn="Ya, Hapus" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus Kategori
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-textMuted text-xs">Belum ada kategori terdaftar.</div>
                @endforelse
            </div>

            <!-- Pagination Footer -->
            @if($categories->hasPages())
                <div class="p-4 border-t border-[#ECEBF4] bg-panelBg/30">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
