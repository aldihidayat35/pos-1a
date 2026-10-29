@extends('layouts.app')

@section('title', 'Katalog Master Produk — PasarHub')
@section('page_title', 'Katalog Master Produk & Konversi Satuan')
@section('page_subtitle', 'Kelola data produk pasar, SKU unik, satuan dasar, dan konversi multi-satuan bertingkat.')

@section('content')
<div class="space-y-6">
    <!-- Sub-Nav Tabs -->
    @include('master.partials.nav')

    <!-- Quick Stats Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-panelBg p-5 rounded-2xl border border-[#ECEBF4] flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-textMuted uppercase tracking-wider">Total Produk Terdaftar</span>
                <h3 class="text-2xl font-extrabold text-sidebar mt-1">{{ \App\Models\Product::count() }} <span class="text-xs font-normal text-textMuted">SKU</span></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sidebar/10 text-sidebar flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>

        <div class="bg-panelBg p-5 rounded-2xl border border-[#ECEBF4] flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-textMuted uppercase tracking-wider">Konversi Multi-UOM</span>
                <h3 class="text-2xl font-extrabold text-brandOrange mt-1">{{ \App\Models\ProductUom::where('is_base', false)->count() }} <span class="text-xs font-normal text-textMuted">Aturan</span></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-brandOrange/10 text-brandOrange flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
        </div>

        <div class="bg-panelBg p-5 rounded-2xl border border-[#ECEBF4] flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-textMuted uppercase tracking-wider">Kategori Pasar Aktif</span>
                <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ \App\Models\ProductCategory::where('is_active', true)->count() }} <span class="text-xs font-normal text-textMuted">Kategori</span></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Actions & Filter Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-[#ECEBF4] shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 sm:gap-4">
            <form method="GET" action="{{ route('master.products.index') }}" class="flex-1 flex flex-col md:flex-row md:items-center gap-2.5 sm:gap-3">
                <!-- Search Input: Full width on mobile, flexible on desktop -->
                <div class="relative w-full md:w-60 lg:w-72 xl:w-80">
                    <svg class="w-4 h-4 text-textMuted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" 
                           data-table-search="products-table"
                           value="{{ request('search') }}" 
                           placeholder="Cari SKU, nama produk, barcode..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <!-- Dropdown Filters: 2 equal cols on mobile, inline on desktop -->
                <div class="grid grid-cols-2 md:flex md:items-center gap-2.5 sm:gap-3 w-full md:w-auto">
                    <select name="category_id" class="w-full md:w-auto px-3 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <select name="status" class="w-full md:w-auto px-3 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hanya Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Hanya Nonaktif</option>
                    </select>
                </div>

                <!-- Per Page & Action Buttons: 2 equal cols on mobile, inline on desktop -->
                <div class="grid grid-cols-2 md:flex md:items-center gap-2.5 sm:gap-3 w-full md:w-auto">
                    <select data-table-perpage="products-table" name="per_page" class="w-full md:w-auto px-3 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-bold text-sidebar focus:bg-white focus:outline-none focus:border-sidebar transition cursor-pointer">
                        <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12 / hal</option>
                        <option value="25" {{ request('per_page', 12) == 25 ? 'selected' : '' }}>25 / hal</option>
                        <option value="50" {{ request('per_page', 12) == 50 ? 'selected' : '' }}>50 / hal</option>
                    </select>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 md:flex-none px-4 py-2.5 bg-sidebar hover:bg-sidebarDark text-white text-xs font-semibold rounded-xl transition shadow-xs text-center">
                            Filter
                        </button>

                        @if(request()->hasAny(['search', 'category_id', 'status', 'per_page']))
                            <a href="{{ route('master.products.index') }}" class="px-3 py-2.5 bg-panelBg hover:bg-gray-200 text-textMuted text-xs font-semibold rounded-xl transition text-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Tambah Produk Button: Full width on mobile, auto shrink on desktop -->
            <div class="w-full lg:w-auto shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-[#ECEBF4]">
                <a href="{{ route('master.products.create') }}" class="w-full lg:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Produk Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Products Table Card with Async & Mobile Card-Stack -->
    <div data-async-table="products-table" class="bg-white rounded-2xl border border-[#ECEBF4] overflow-hidden shadow-sm">
        <!-- DESKTOP VIEW: Structured Table (hidden on mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-panelBg/80 border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4 w-16 text-center">Foto</th>
                        <th class="py-3.5 px-4">SKU & Barcode</th>
                        <th class="py-3.5 px-4">Nama Produk & Kategori</th>
                        <th class="py-3.5 px-4">Satuan Dasar (Base UOM)</th>
                        <th class="py-3.5 px-4">Konversi Satuan Bertingkat</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ECEBF4]">
                    @forelse($products as $p)
                        <tr class="hover:bg-panelBg/40 transition">
                            <!-- Foto Produk (Click to Enlarge Modal) -->
                            <td class="py-3 px-4 text-center align-middle">
                                <x-product-thumbnail :product="$p" size="md" />
                            </td>

                            <!-- SKU & Barcode -->
                            <td class="py-3.5 px-4 font-mono">
                                <span class="font-bold text-sidebar bg-sidebar/5 px-2 py-0.5 rounded border border-sidebar/10">{{ $p->sku }}</span>
                                @php
                                    $basePu = $p->productUoms->firstWhere('is_base', true);
                                @endphp
                                @if($basePu && $basePu->barcode)
                                    <div class="text-[10px] text-textMuted mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                        <span>{{ $basePu->barcode }}</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Nama Produk & Kategori -->
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-textMain text-sm block">{{ $p->name }}</span>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-panelBg text-textMuted border border-[#ECEBF4]">
                                    {{ $p->category->name ?? '-' }}
                                </span>
                            </td>

                            <!-- Base UOM -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-sidebar/10 text-sidebar border border-sidebar/20">
                                    <span>{{ $p->baseUom->name }}</span>
                                    <span class="text-[10px] font-normal text-textMuted">({{ $p->baseUom->symbol }})</span>
                                </span>
                                <span class="block text-[10px] text-textMuted mt-1">Dimensi: {{ $p->baseUom->dimension }}</span>
                            </td>

                            <!-- Multi-UOM Conversions -->
                            <td class="py-3.5 px-4">
                                @php
                                    $conversions = $p->productUoms->where('is_base', false);
                                @endphp
                                @if($conversions->count() > 0)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($conversions as $conv)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-900 border border-amber-200"
                                                  title="Barcode: {{ $conv->barcode ?: 'Tidak ada' }}">
                                                <strong>1 {{ $conv->uom->symbol }}</strong> = {{ rtrim(rtrim(number_format($conv->factor_to_base, 3), '0'), '.') }} {{ $p->baseUom->symbol }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-[11px] text-textMuted italic">Hanya satuan dasar</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 text-center">
                                @if($p->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('master.products.edit', $p) }}" 
                                       class="p-2 text-textMuted hover:text-sidebar hover:bg-panelBg rounded-xl transition" title="Edit Produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    @if($p->is_active)
                                        <form method="POST" action="{{ route('master.products.destroy', $p) }}" 
                                              data-confirm="Apakah Anda yakin ingin menonaktifkan produk [{{ $p->name }}]?" data-confirm-title="Nonaktifkan Produk" data-confirm-btn="Ya, Nonaktifkan" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-textMuted hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Nonaktifkan Produk">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-textMuted">
                                <div class="w-12 h-12 rounded-2xl bg-panelBg mx-auto flex items-center justify-center text-textMuted mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <p class="font-semibold text-textMain text-sm">Tidak ada produk ditemukan</p>
                                <p class="text-xs text-textMuted mt-0.5">Silakan sesuaikan filter pencarian atau tambahkan produk baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MOBILE VIEW: Card Stack Layout (visible on mobile only) -->
        <div class="block md:hidden p-3.5 sm:p-4 space-y-3.5">
            @forelse($products as $p)
                <div class="bg-white border border-[#ECEBF4] rounded-2xl p-4 space-y-3 shadow-xs">
                    <!-- Top Row: SKU Badge on left, Status Pill on right (Perfect Symmetry) -->
                    <div class="flex items-center justify-between gap-2 pb-1">
                        <span class="font-mono font-bold text-[11px] text-sidebar bg-sidebar/5 px-2.5 py-0.5 rounded-lg border border-sidebar/10">
                            {{ $p->sku }}
                        </span>
                        @if($p->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                Nonaktif
                            </span>
                        @endif
                    </div>

                    <!-- Middle Row: Product Thumbnail, Name, and Category -->
                    <div class="flex items-center gap-3">
                        <x-product-thumbnail :product="$p" size="lg" />
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-textMain text-sm leading-snug line-clamp-2">
                                {{ $p->name }}
                            </h4>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-panelBg text-textMuted border border-[#ECEBF4] mt-1">
                                {{ $p->category->name ?? 'Tanpa Kategori' }}
                            </span>
                        </div>
                    </div>

                    <!-- Specs Box: Symmetrical 2 Columns with Clean Vertical Divider -->
                    <div class="bg-panelBg/70 rounded-xl p-3 border border-[#ECEBF4] grid grid-cols-2 gap-3 divide-x divide-[#ECEBF4]">
                        <div class="pr-2">
                            <span class="text-[10px] text-textMuted block uppercase font-bold tracking-wider">Satuan Dasar</span>
                            <span class="font-bold text-sidebar text-xs mt-0.5 block truncate">
                                {{ $p->baseUom->name }} ({{ $p->baseUom->symbol }})
                            </span>
                        </div>
                        <div class="pl-3">
                            <span class="text-[10px] text-textMuted block uppercase font-bold tracking-wider">Konversi Bertingkat</span>
                            @php
                                $mobileConvs = $p->productUoms->where('is_base', false);
                            @endphp
                            @if($mobileConvs->count() > 0)
                                <span class="font-bold text-textMain text-xs mt-0.5 block truncate">
                                    {{ $mobileConvs->count() }} Tingkat UOM
                                </span>
                            @else
                                <span class="text-textMuted text-xs italic mt-0.5 block">
                                    Hanya Satuan Dasar
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions: Full-Width Symmetrical Button Row -->
                    <div class="grid {{ $p->is_active ? 'grid-cols-2' : 'grid-cols-1' }} gap-2.5 pt-1 w-full">
                        <a href="{{ route('master.products.edit', $p) }}" 
                           class="w-full py-2.5 px-3 text-xs font-bold text-sidebar bg-sidebar/10 hover:bg-sidebar hover:text-white rounded-xl transition flex items-center justify-center gap-2 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Edit Produk</span>
                        </a>

                        @if($p->is_active)
                            <form method="POST" action="{{ route('master.products.destroy', $p) }}" 
                                  data-confirm="Apakah Anda yakin ingin menonaktifkan produk [{{ $p->name }}]?" data-confirm-title="Nonaktifkan Produk" data-confirm-btn="Ya, Nonaktifkan" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-2.5 px-3 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-600 hover:text-white rounded-xl transition flex items-center justify-center gap-2 shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    <span>Nonaktifkan</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-textMuted text-xs">Tidak ada produk ditemukan.</div>
            @endforelse
        </div>

        <!-- Pagination Footer -->
        @if($products->hasPages())
            <div class="p-4 border-t border-[#ECEBF4] bg-panelBg/30">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
