@extends('layouts.app')

@section('title', 'Tambah Produk Baru — PasarHub')
@section('page_title', 'Tambah Master Produk')
@section('page_subtitle', 'Daftarkan SKU baru, tentukan satuan dasar (Base UOM), dan aturan konversi multi-satuan.')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Sub-Nav Tabs -->
    @include('master.partials.nav')

    <!-- Form Container -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-[#ECEBF4] shadow-sm">
        <div class="border-b border-[#ECEBF4] pb-4 mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-textMain">Formulir Pendaftaran Produk</h2>
                <p class="text-xs text-textMuted mt-0.5">Semua data transaksi di masa depan akan berpatokan pada satuan dasar produk ini.</p>
            </div>
            <a href="{{ route('master.products.index') }}" class="text-xs font-semibold text-textMuted hover:text-sidebar px-3 py-2 rounded-xl hover:bg-panelBg transition">
                ← Kembali ke Katalog
            </a>
        </div>

        <form method="POST" action="{{ route('master.products.store') }}" id="productForm" class="space-y-6">
            @csrf

            <!-- Fuzzy Duplicate Matching Warning Box (PRD 4.2 >= 75%) -->
            <div id="duplicateWarningBox" class="hidden p-4 bg-amber-50 border border-amber-300 text-amber-900 rounded-2xl animate-fadeIn">
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-amber-500 text-white flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-bold text-amber-950 uppercase tracking-wide">Peringatan Kemiripan Nama Produk (>= 75%)</h4>
                        <p class="text-xs text-amber-800 mt-0.5">
                            Sistem mendeteksi produk dengan nama serupa di katalog. Pastikan produk ini bukan duplikasi barang yang sudah ada:
                        </p>
                        <div id="duplicateList" class="mt-2.5 space-y-1.5"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- SKU -->
                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        SKU Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="sku" value="{{ old('sku') }}" required placeholder="Mis. SKU-BRS-005"
                           class="w-full px-4 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-mono font-bold text-sidebar uppercase focus:bg-white focus:outline-none focus:border-sidebar transition">
                    <span class="block text-[11px] text-textMuted mt-1">Kode unik identifikasi stok di seluruh gudang & toko.</span>
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Kategori Produk <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" required
                            class="w-full px-4 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                        <option value="">Pilih Kategori Pasar</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                <input type="text" name="name" id="productNameInput" value="{{ old('name') }}" required 
                       placeholder="Mis. Beras Rojolele Super 50kg, Minyak Goreng Bimoli 1 Liter..."
                       class="w-full px-4 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                <span class="block text-[11px] text-textMuted mt-1">Nama akan diuji kemiripannya (fuzzy match) secara otomatis untuk mencegah duplikasi.</span>
            </div>

            <!-- Base UOM & Base Barcode -->
            <div class="p-5 bg-panelBg rounded-2xl border border-[#ECEBF4] space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-sidebar text-white flex items-center justify-center text-xs font-bold">1</div>
                    <h3 class="text-xs font-bold text-sidebar uppercase tracking-wider">Satuan Dasar (Base UOM) & Barcode Ritel</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-textMuted mb-1.5">
                            Satuan Dasar Buku Stok <span class="text-red-500">*</span>
                        </label>
                        <select name="base_uom_id" id="baseUomSelect" required
                                class="w-full px-3.5 py-2.5 bg-white border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar transition">
                            <option value="">Pilih Satuan Dasar (Mis. KG, LTR, PCS)</option>
                            @foreach($uoms as $u)
                                <option value="{{ $u->id }}" data-dimension="{{ $u->dimension }}" data-symbol="{{ $u->symbol }}"
                                    {{ old('base_uom_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->symbol }}) — Dimensi: {{ $u->dimension }}
                                </option>
                            @endforeach
                        </select>
                        <span class="block text-[10px] text-textMuted mt-1">Satuan terkecil tempat seluruh kuantitas dan HPP dibukukan.</span>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-textMuted mb-1.5">
                            Barcode Satuan Dasar (Opsional)
                        </label>
                        <input type="text" name="base_barcode" value="{{ old('base_barcode') }}" placeholder="Mis. 8991001001001"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#E6E4F0] rounded-xl text-xs font-mono font-medium text-textMain focus:outline-none focus:border-sidebar transition">
                        <span class="block text-[10px] text-textMuted mt-1">Dapat di-scan langsung di kasir POS atau penerimaan.</span>
                    </div>
                </div>
            </div>

            <!-- Dynamic Multi-UOM Conversions -->
            <div class="p-5 bg-white rounded-2xl border border-[#ECEBF4] space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-brandOrange text-white flex items-center justify-center text-xs font-bold">2</div>
                        <div>
                            <h3 class="text-xs font-bold text-textMain uppercase tracking-wider">Konversi Multi-Satuan Bertingkat (Opsional)</h3>
                            <p class="text-[11px] text-textMuted">Contoh: 1 Karung 25kg = 25 kg, 1 Dus = 12 pcs. Dimensi harus sama dengan satuan dasar.</p>
                        </div>
                    </div>

                    <button type="button" id="btnAddConversion" class="px-3 py-1.5 bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Tambah Konversi</span>
                    </button>
                </div>

                <!-- Conversions Table / Container -->
                <div id="conversionRowsContainer" class="space-y-3">
                    <p id="noConversionText" class="text-xs text-textMuted italic py-3 text-center bg-panelBg rounded-xl border border-dashed border-[#ECEBF4]">
                        Belum ada konversi tambahan. Produk ini hanya akan ditransaksikan dalam satuan dasar.
                    </p>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                    Deskripsi / Catatan Produk
                </label>
                <textarea name="description" rows="3" placeholder="Informasi merek, varian, atau spesifikasi khusus..."
                          class="w-full px-4 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">{{ old('description') }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-[#ECEBF4] flex items-center justify-end gap-3">
                <a href="{{ route('master.products.index') }}" class="px-5 py-2.5 bg-panelBg hover:bg-gray-200 text-textMuted text-xs font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition shadow-sm">
                    Simpan Master Produk
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // 1. Fuzzy Duplicate Matching (Debounced)
    const nameInput = document.getElementById('productNameInput');
    const warningBox = document.getElementById('duplicateWarningBox');
    const duplicateList = document.getElementById('duplicateList');
    let debounceTimer = null;

    nameInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const name = this.value.trim();

        if (name.length < 3) {
            warningBox.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`{{ route('master.products.check_duplicate') }}?name=${encodeURIComponent(name)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.has_warning) {
                        duplicateList.innerHTML = '';
                        data.duplicates.forEach(dup => {
                            const item = document.createElement('div');
                            item.className = 'flex items-center justify-between p-2 bg-white rounded-xl border border-amber-200 text-xs';
                            item.innerHTML = `
                                <div>
                                    <span class="font-bold text-textMain">${dup.name}</span>
                                    <span class="font-mono text-[10px] text-textMuted ml-1.5">[SKU: ${dup.sku}]</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                    Kemiripan: ${dup.similarity}%
                                </span>
                            `;
                            duplicateList.appendChild(item);
                        });
                        warningBox.classList.remove('hidden');
                    } else {
                        warningBox.classList.add('hidden');
                    }
                })
                .catch(() => warningBox.classList.add('hidden'));
        }, 350);
    });

    // 2. Dynamic Conversion Rows Filtered by Base UOM Dimension
    const baseUomSelect = document.getElementById('baseUomSelect');
    const btnAddConversion = document.getElementById('btnAddConversion');
    const conversionContainer = document.getElementById('conversionRowsContainer');
    const noConversionText = document.getElementById('noConversionText');
    let convRowIndex = 0;

    const allUoms = @json($uoms);

    btnAddConversion.addEventListener('click', function() {
        const selectedBaseOption = baseUomSelect.options[baseUomSelect.selectedIndex];
        if (!baseUomSelect.value) {
            alert('Silakan pilih Satuan Dasar (Base UOM) terlebih dahulu.');
            baseUomSelect.focus();
            return;
        }

        const baseDimension = selectedBaseOption.getAttribute('data-dimension');
        const baseSymbol = selectedBaseOption.getAttribute('data-symbol');

        // Filter UOMs that match the same dimension and are not the base UOM
        const validUoms = allUoms.filter(u => u.dimension === baseDimension && u.id != baseUomSelect.value);

        if (validUoms.length === 0) {
            alert(`Tidak ada satuan konversi yang tersedia untuk dimensi ${baseDimension}. Silakan daftarkan satuan baru di menu Satuan UOM.`);
            return;
        }

        if (noConversionText) {
            noConversionText.classList.add('hidden');
        }

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
