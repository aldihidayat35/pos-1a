@extends('layouts.app')

@section('title', 'Buat Purchase Order Baru — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Error Validation Alert -->
  @if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <span>Harap perbaiki kesalahan input berikut:</span>
      </div>
      <ul class="list-disc pl-5 font-normal text-[11px] space-y-0.5">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Top Breadcrumb & Header -->
  <div class="flex items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('warehouse.index') }}" class="hover:underline">Operasional Gudang</a>
        <span>/</span>
        <a href="{{ route('warehouse.po.index') }}" class="hover:underline">Purchase Orders</a>
        <span>/</span>
        <span class="text-textMain font-bold">Terbitkan PO Baru</span>
      </div>
      <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
        Terbitkan Purchase Order (PO) Suplayer
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Pengadaan barang dari suplayer terdaftar menuju dermaga penerimaan gudang sentral.
      </p>
    </div>

    <a href="{{ route('warehouse.po.index') }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
      Batal
    </a>
  </div>

  <!-- Form Container -->
  <form action="{{ route('warehouse.po.store') }}" method="POST" id="poForm" class="space-y-6">
    @csrf

    <!-- 1. PO Header Information -->
    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
      <h3 class="font-bold text-sm text-textMain border-b border-[#ECEBF4] pb-3 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-brandOrange"></span>
        <span>1. Rincian Pemasok & Lokasi Tujuan</span>
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
        <!-- Suplayer -->
        <div>
          <label class="block font-bold text-textMain mb-1.5">Pemasok / Suplayer <span class="text-rose-500">*</span></label>
          <select name="supplier_id" required class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">
            <option value="">-- Pilih Pemasok --</option>
            @foreach($suppliers as $s)
              <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>
                {{ $s->name }} ({{ $s->supplier_code }})
              </option>
            @endforeach
          </select>
        </div>

        <!-- Destination Location -->
        <div>
          <label class="block font-bold text-textMain mb-1.5">Gudang Tujuan <span class="text-rose-500">*</span></label>
          <select name="destination_location_id" required class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (old('destination_location_id') ?? $activeLocation?->id) == $loc->id ? 'selected' : '' }}>
                {{ $loc->name }} ({{ $loc->type }})
              </option>
            @endforeach
          </select>
        </div>

        <!-- Order Date -->
        <div>
          <label class="block font-bold text-textMain mb-1.5">Tanggal Pesanan</label>
          <input type="date" name="ordered_at" value="{{ old('ordered_at', date('Y-m-d')) }}"
                 class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">
        </div>

        <!-- Expected Delivery -->
        <div>
          <label class="block font-bold text-textMain mb-1.5">Estimasi Tiba (Lead Time)</label>
          <input type="date" name="expected_delivery_at" value="{{ old('expected_delivery_at', date('Y-m-d', strtotime('+2 days'))) }}"
                 class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">
        </div>

        <!-- Notes -->
        <div class="sm:col-span-2 lg:col-span-4">
          <label class="block font-bold text-textMain mb-1.5">Catatan Logistik / Instruksi Pengiriman</label>
          <input type="text" name="notes" value="{{ old('notes') }}"
                 placeholder="Contoh: Pengiriman via truk fuso, wajib segel utuh dan sertakan nota pabrik."
                 class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">
        </div>
      </div>
    </div>

    <!-- 2. Ordered Line Items -->
    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
        <h3 class="font-bold text-sm text-textMain flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span>2. Daftar Barang Pesanan (Multi-UOM)</span>
        </h3>
        <button type="button" onclick="addItemRow()" class="px-3 py-1.5 bg-sidebar text-white font-bold text-xs rounded-xl hover:bg-sidebar/90 transition flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Tambah Baris</span>
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs" id="itemsTable">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-2.5 px-3 min-w-[220px]">Produk & SKU</th>
              <th class="py-2.5 px-3 min-w-[160px]">Satuan Beli (UOM)</th>
              <th class="py-2.5 px-3 min-w-[120px]">Jumlah Pesanan</th>
              <th class="py-2.5 px-3 min-w-[140px]">Harga Beli Satuan (Rp)</th>
              <th class="py-2.5 px-3 min-w-[140px] text-right">Subtotal (Rp)</th>
              <th class="py-2.5 px-3 w-12 text-center">Hapus</th>
            </tr>
          </thead>
          <tbody id="itemsTableBody" class="divide-y divide-[#ECEBF4]">
            <!-- Dynamic rows will be inserted here -->
          </tbody>
        </table>
      </div>

      <!-- Financial Calculation Summary -->
      <div class="pt-4 border-t border-[#ECEBF4] flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-xs text-textMuted">
          <p class="font-medium">Konversi kuantitas ke satuan dasar (kg/liter/pcs) akan disnapshot secara otomatis saat penerimaan barang.</p>
        </div>

        <div class="flex items-center gap-6 bg-panelBg p-4 rounded-2xl border border-[#ECEBF4]">
          <div class="text-right">
            <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Estimasi Total PO</span>
            <span id="grandTotalLabel" class="text-xl font-black text-sidebar">Rp 0</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Submit Button Group -->
    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ route('warehouse.po.index') }}" class="px-4 py-2.5 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
        Batal
      </a>
      <button type="submit" class="px-6 py-2.5 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
        <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <span>Terbitkan Purchase Order</span>
      </button>
    </div>
  </form>
</main>

<script>
  // Product Catalog Dataset for Dynamic Lines
  const productsData = @json($products);
  let rowIndex = 0;

  function addItemRow(selectedProductId = null, qty = 1, price = 0) {
    const tbody = document.getElementById('itemsTableBody');
    const tr = document.createElement('tr');
    tr.id = `row-${rowIndex}`;
    tr.className = 'hover:bg-panelBg/40 transition';

    let productOptions = '<option value="">-- Pilih Produk --</option>';
    productsData.forEach(p => {
      const isSelected = selectedProductId && selectedProductId == p.id ? 'selected' : '';
      productOptions += `<option value="${p.id}" ${isSelected}>${p.name} (${p.sku})</option>`;
    });

    tr.innerHTML = `
      <td class="py-2.5 px-3">
        <select name="items[${rowIndex}][product_id]" required onchange="onProductChange(${rowIndex})" id="product-select-${rowIndex}"
                class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar">
          ${productOptions}
        </select>
      </td>
      <td class="py-2.5 px-3">
        <select name="items[${rowIndex}][product_uom_id]" required id="uom-select-${rowIndex}" onchange="recalculateLine(${rowIndex})"
                class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">
          <option value="">-- Satuan --</option>
        </select>
      </td>
      <td class="py-2.5 px-3">
        <input type="number" step="0.001" min="0.001" name="items[${rowIndex}][quantity_ordered]" value="${qty}" required
               oninput="recalculateLine(${rowIndex})" id="qty-input-${rowIndex}"
               class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-bold text-textMain text-right focus:outline-none focus:border-sidebar">
      </td>
      <td class="py-2.5 px-3">
        <input type="number" step="1" min="0" name="items[${rowIndex}][unit_purchase_price]" value="${price}" required
               oninput="recalculateLine(${rowIndex})" id="price-input-${rowIndex}"
               class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-bold text-sidebar text-right focus:outline-none focus:border-sidebar">
      </td>
      <td class="py-2.5 px-3 text-right font-extrabold text-sidebar text-xs" id="subtotal-label-${rowIndex}">
        Rp 0
      </td>
      <td class="py-2.5 px-3 text-center">
        <button type="button" onclick="removeRow(${rowIndex})" class="p-1.5 text-textMuted hover:text-rose-600 transition rounded-lg hover:bg-rose-50" title="Hapus Baris">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </button>
      </td>
    `;

    tbody.appendChild(tr);

    if (selectedProductId) {
      onProductChange(rowIndex);
    }

    rowIndex++;
  }

  function onProductChange(idx) {
    const productSelect = document.getElementById(`product-select-${idx}`);
    const uomSelect = document.getElementById(`uom-select-${idx}`);
    const prodId = productSelect.value;

    uomSelect.innerHTML = '<option value="">-- Satuan --</option>';

    if (!prodId) {
      recalculateLine(idx);
      return;
    }

    const prod = productsData.find(p => p.id == prodId);
    if (prod && prod.product_uoms) {
      prod.product_uoms.forEach(pu => {
        const factorText = parseFloat(pu.factor_to_base) > 1 ? ` (Isi ${parseFloat(pu.factor_to_base)} ${prod.base_uom?.symbol ?? ''})` : ' (Satuan Dasar)';
        uomSelect.innerHTML += `<option value="${pu.id}" data-factor="${pu.factor_to_base}">${pu.uom?.name ?? 'UOM'} [${pu.uom?.symbol ?? ''}]${factorText}</option>`;
      });

      // Default select the first package UOM, or base
      if (prod.product_uoms.length > 0) {
        uomSelect.selectedIndex = 1;
      }
    }

    recalculateLine(idx);
  }

  function recalculateLine(idx) {
    const qtyInput = document.getElementById(`qty-input-${idx}`);
    const priceInput = document.getElementById(`price-input-${idx}`);
    const subtotalLabel = document.getElementById(`subtotal-label-${idx}`);

    if (!qtyInput || !priceInput || !subtotalLabel) return;

    const qty = parseFloat(qtyInput.value) || 0;
    const price = parseFloat(priceInput.value) || 0;
    const subtotal = Math.round(qty * price);

    subtotalLabel.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    updateGrandTotal();
  }

  function removeRow(idx) {
    const row = document.getElementById(`row-${idx}`);
    if (row) {
      row.remove();
      updateGrandTotal();
    }
  }

  function updateGrandTotal() {
    let grandTotal = 0;
    const qtyInputs = document.querySelectorAll('input[id^="qty-input-"]');
    qtyInputs.forEach(qi => {
      const idx = qi.id.replace('qty-input-', '');
      const pi = document.getElementById(`price-input-${idx}`);
      if (pi) {
        const q = parseFloat(qi.value) || 0;
        const p = parseFloat(pi.value) || 0;
        grandTotal += (q * p);
      }
    });

    document.getElementById('grandTotalLabel').textContent = 'Rp ' + Math.round(grandTotal).toLocaleString('id-ID');
  }

  // Initialize with 1 empty row on load
  document.addEventListener('DOMContentLoaded', () => {
    if (productsData.length > 0) {
      addItemRow(productsData[0].id, 10, 500000);
    } else {
      addItemRow();
    }
  });
</script>
@endsection

@section('right_panel')
  @include('partials.right-panel')
@endsection
