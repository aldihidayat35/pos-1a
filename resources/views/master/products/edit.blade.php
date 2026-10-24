@extends('layouts.app')

@section('title', 'Edit Produk — ' . $product->name)
@section('page_title', 'Edit Master Produk')
@section('page_subtitle', 'Perbarui data SKU, nama, kategori, barcode, atau aturan konversi satuan.')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Sub-Nav Tabs -->
    @include('master.partials.nav')

    <!-- Form Container -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-[#ECEBF4] shadow-sm">
        <div class="border-b border-[#ECEBF4] pb-4 mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-textMain">Edit Produk: {{ $product->name }}</h2>
                <p class="text-xs text-textMuted mt-0.5">SKU: <span class="font-mono font-bold text-sidebar">{{ $product->sku }}</span></p>
            </div>
            <a href="{{ route('master.products.index') }}" class="text-xs font-semibold text-textMuted hover:text-sidebar px-3 py-2 rounded-xl hover:bg-panelBg transition">
                ← Kembali ke Katalog
            </a>
        </div>

        <form method="POST" action="{{ route('master.products.update', $product) }}" id="productForm" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- SKU -->
                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        SKU Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                           class="w-full px-4 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-mono font-bold text-sidebar uppercase focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Kategori Produk <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" required
                            class="w-full px-4 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Nama Produk -->
            <div>
                <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                    Nama Produk Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="productNameInput" value="{{ old('name', $product->name) }}" required 
                       class="w-full px-4 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
            </div>

            <!-- Base UOM & Base Barcode -->
            <div class="p-5 bg-panelBg rounded-2xl border border-[#ECEBF4] space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-sidebar text-white flex items-center justify-center text-xs font-bold">1</div>
                    <h3 class="text-xs font-bold text-sidebar uppercase tracking-wider">Satuan Dasar (Base UOM) & Barcode</h3>
                </div>

                @php
                    $baseProductUom = $product->productUoms->firstWhere('is_base', true);
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-textMuted mb-1.5">
                            Satuan Dasar Buku Stok <span class="text-red-500">*</span>
                        </label>
                        <select name="base_uom_id" id="baseUomSelect" required
                                class="w-full px-3.5 py-2.5 bg-white border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar transition">
                            @foreach($uoms as $u)
                                <option value="{{ $u->id }}" data-dimension="{{ $u->dimension }}" data-symbol="{{ $u->symbol }}"
                                    {{ old('base_uom_id', $product->base_uom_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->symbol }}) — Dimensi: {{ $u->dimension }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-textMuted mb-1.5">
                            Barcode Satuan Dasar (Opsional)
                        </label>
                        <input type="text" name="base_barcode" value="{{ old('base_barcode', $baseProductUom?->barcode) }}"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#E6E4F0] rounded-xl text-xs font-mono font-medium text-textMain focus:outline-none focus:border-sidebar transition">
                    </div>
                </div>
            </div>

            <!-- Multi-UOM Conversions -->
            <div class="p-5 bg-white rounded-2xl border border-[#ECEBF4] space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-brandOrange text-white flex items-center justify-center text-xs font-bold">2</div>
                        <div>
                            <h3 class="text-xs font-bold text-textMain uppercase tracking-wider">Konversi Multi-Satuan Bertingkat</h3>
                            <p class="text-[11px] text-textMuted">Aturan paket grosir, karung, atau karton.</p>
                        </div>
                    </div>

                    <button type="button" id="btnAddConversion" class="px-3 py-1.5 bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Tambah Konversi</span>
                    </button>
                </div>

                <div id="conversionRowsContainer" class="space-y-3">
                    @php
                        $conversions = $product->productUoms->where('is_base', false);
                    @endphp

                    @forelse($conversions as $idx => $conv)
                        <div id="conv-row-{{ $conv->id }}" class="p-3.5 bg-panelBg rounded-xl border border-[#ECEBF4] grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-4">
                                <label class="block text-[10px] font-bold text-textMuted uppercase mb-1">Satuan Konversi</label>
                                <select name="conversions[{{ $idx }}][uom_id]" required class="w-full px-3 py-2 bg-white border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar">
                                    @foreach($uoms as $u)
                                        @if($u->dimension === $product->baseUom->dimension && $u->id != $product->base_uom_id)
                                            <option value="{{ $u->id }}" {{ $conv->uom_id == $u->id ? 'selected' : '' }}>
                                                {{ $u->name }} ({{ $u->symbol }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-[10px] font-bold text-textMuted uppercase mb-1">Faktor = x Satuan Dasar</label>
                                <div class="relative">
                                    <input type="number" step="0.000001" min="0.000001" name="conversions[{{ $idx }}][factor_to_base]" value="{{ (float)$conv->factor_to_base }}" required class="w-full px-3 py-2 bg-white border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain pr-10 focus:outline-none focus:border-sidebar">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-textMuted">{{ $product->baseUom->symbol }}</span>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-[10px] font-bold text-textMuted uppercase mb-1">Barcode Kemasan</label>
                                <input type="text" name="conversions[{{ $idx }}][barcode]" value="{{ $conv->barcode }}" class="w-full px-3 py-2 bg-white border border-[#E6E4F0] rounded-xl text-xs font-mono font-medium text-textMain focus:outline-none focus:border-sidebar">
                            </div>

                            <div class="sm:col-span-2 flex items-center justify-end gap-2 pt-4 sm:pt-0">
                                <label class="flex items-center gap-1 text-[11px] font-bold text-textMain cursor-pointer">
                                    <input type="checkbox" name="conversions[{{ $idx }}][is_sale]" value="1" {{ $conv->is_sale ? 'checked' : '' }} class="rounded text-sidebar">
                                    <span>Jual</span>
                                </label>
                                <button type="button" onclick="document.getElementById('conv-row-{{ $conv->id }}').remove()" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus Konversi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p id="noConversionText" class="text-xs text-textMuted italic py-3 text-center bg-panelBg rounded-xl border border-dashed border-[#ECEBF4]">
                            Belum ada konversi tambahan.
                        </p>
                    @endforelse
                </div>
            </div>

            <!-- Status Aktif & Deskripsi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">Status Produk</label>
                    <select name="is_active" class="w-full px-4 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                        <option value="1" {{ $product->is_active ? 'selected' : '' }}>Aktif (Tersedia untuk Transaksi)</option>
                        <option value="0" {{ !$product->is_active ? 'selected' : '' }}>Nonaktif (Diarsipkan)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">Lacak Saldo Fisik</label>
                    <select name="track_stock" class="w-full px-4 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                        <option value="1" {{ $product->track_stock ? 'selected' : '' }}>Ya (Wajib Kontrol Stok & Ledger)</option>
                        <option value="0" {{ !$product->track_stock ? 'selected' : '' }}>Tidak (Barang Non-Stok / Jasa)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">Deskripsi / Catatan Produk</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-[#ECEBF4] flex items-center justify-end gap-3">
                <a href="{{ route('master.products.index') }}" class="px-5 py-2.5 bg-panelBg hover:bg-gray-200 text-textMuted text-xs font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition shadow-sm">
                    Perbarui Master Produk
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const baseUomSelect = document.getElementById('baseUomSelect');
    const btnAddConversion = document.getElementById('btnAddConversion');
    const conversionContainer = document.getElementById('conversionRowsContainer');
    let convRowIndex = 100;
    const allUoms = @json($uoms);

    btnAddConversion.addEventListener('click', function() {
        const selectedBaseOption = baseUomSelect.options[baseUomSelect.selectedIndex];
        const baseDimension = selectedBaseOption.getAttribute('data-dimension');
        const baseSymbol = selectedBaseOption.getAttribute('data-symbol');

        const validUoms = allUoms.filter(u => u.dimension === baseDimension && u.id != baseUomSelect.value);

        if (validUoms.length === 0) {
            alert(`Tidak ada satuan konversi yang tersedia untuk dimensi ${baseDimension}.`);
            return;
        }

        const noText = document.getElementById('noConversionText');
        if (noText) noText.remove();

        convRowIndex++;
        const row = document.createElement('div');
        row.id = `conv-row-${convRowIndex}`;
        row.className = 'p-3.5 bg-panelBg rounded-xl border border-[#ECEBF4] grid grid-cols-1 sm:grid-cols-12 gap-3 items-center animate-fadeIn';

        let optionsHtml = '<option value="">Pilih Satuan Konversi</option>';
        validUoms.forEach(u => {
            optionsHtml += `<option value="${u.id}">${u.name} (${u.symbol})</option>`;
        });

        row.innerHTML = `
            <div class="sm:col-span-4">
                <label class="block text-[10px] font-bold text-textMuted uppercase mb-1">Satuan Konversi</label>
                <select name="conversions[${convRowIndex}][uom_id]" required class="w-full px-3 py-2 bg-white border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar">
                    ${optionsHtml}
                </select>
            </div>

            <div class="sm:col-span-3">
                <label class="block text-[10px] font-bold text-textMuted uppercase mb-1">Faktor = x Satuan Dasar</label>
                <div class="relative">
                    <input type="number" step="0.000001" min="0.000001" name="conversions[${convRowIndex}][factor_to_base]" required placeholder="Mis. 25" class="w-full px-3 py-2 bg-white border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain pr-10 focus:outline-none focus:border-sidebar">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-textMuted">${baseSymbol}</span>
                </div>
            </div>

            <div class="sm:col-span-3">
                <label class="block text-[10px] font-bold text-textMuted uppercase mb-1">Barcode Kemasan</label>
                <input type="text" name="conversions[${convRowIndex}][barcode]" placeholder="Barcode khusus" class="w-full px-3 py-2 bg-white border border-[#E6E4F0] rounded-xl text-xs font-mono font-medium text-textMain focus:outline-none focus:border-sidebar">
            </div>

            <div class="sm:col-span-2 flex items-center justify-end gap-2 pt-4 sm:pt-0">
                <label class="flex items-center gap-1 text-[11px] font-bold text-textMain cursor-pointer">
                    <input type="checkbox" name="conversions[${convRowIndex}][is_sale]" value="1" checked class="rounded text-sidebar">
                    <span>Jual</span>
                </label>
                <button type="button" onclick="document.getElementById('conv-row-${convRowIndex}').remove()" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus Konversi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        `;

        conversionContainer.appendChild(row);
    });
</script>
@endpush
@endsection
