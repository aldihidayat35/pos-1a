@extends('layouts.app')

@section('title', 'Kirim Transfer Stok Baru — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Error Validation Alert -->
  @if($errors->any() || session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <span>{{ session('error') ?? 'Harap perbaiki kesalahan input pengiriman:' }}</span>
      </div>
      @if($errors->any())
        <ul class="list-disc pl-5 font-normal text-[11px] space-y-0.5">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      @endif
    </div>
  @endif

  <!-- Top Breadcrumb & Header -->
  <div class="flex items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('warehouse.index') }}" class="hover:underline">Operasional Gudang</a>
        <span>/</span>
        <a href="{{ route('warehouse.transfers.index') }}" class="hover:underline">Transfer Barang</a>
        <span>/</span>
        <span class="text-textMain font-bold">Kirim Transfer Baru</span>
      </div>
      <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
        Kirim Transfer Barang Antar Lokasi
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Pengiriman stok dari gudang menuju toko pasar dengan pemotongan stok asal dan pencatatan transit otomatis.
      </p>
    </div>

    <a href="{{ route('warehouse.transfers.index') }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
      Batal
    </a>
  </div>

  <!-- Transfer Form -->
  <form action="{{ route('warehouse.transfers.store') }}" method="POST" id="transferForm" class="space-y-6">
    @csrf

    <!-- Location Settings -->
    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
      <h3 class="font-bold text-sm text-textMain border-b border-[#ECEBF4] pb-3 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-brandOrange"></span>
        <span>1. Rute Pengiriman Barang</span>
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
        <div>
          <label class="block font-bold text-textMain mb-1.5">Lokasi Asal (Pengirim)</label>
          <input type="hidden" name="from_location_id" value="{{ $activeLocation->id }}">
          <div class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-bold text-sidebar flex items-center justify-between">
            <span>{{ $activeLocation->name }} ({{ $activeLocation->code }})</span>
            <span class="text-[10px] bg-white px-2 py-0.5 rounded text-textMuted border border-gray-200">Asal</span>
          </div>
        </div>

        <div>
          <label class="block font-bold text-textMain mb-1.5">Lokasi Tujuan (Penerima) <span class="text-rose-500">*</span></label>
          <select name="to_location_id" required class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar">
            <option value="">-- Pilih Toko / Cabang Tujuan --</option>
            @foreach($destinations as $dest)
              <option value="{{ $dest->id }}" {{ old('to_location_id') == $dest->id ? 'selected' : '' }}>
                {{ $dest->name }} ({{ $dest->code }}) • {{ $dest->type }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="sm:col-span-2">
          <label class="block font-bold text-textMain mb-1.5">Catatan Ekspedisi / Armada Pengantar</label>
          <input type="text" name="notes" value="{{ old('notes') }}"
                 placeholder="Contoh: Kirim via pick-up B-8821-XYZ, sopir Pak Rudi, target tiba pk 10:00 WIB."
                 class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">
        </div>
      </div>
    </div>

    <!-- Line Items Table -->
    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
        <div>
          <h3 class="font-bold text-sm text-textMain flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            <span>2. Barang yang Dikirim dari Saldo On-Hand</span>
          </h3>
          <p class="text-xs text-textMuted mt-0.5">Hanya produk yang memiliki saldo fisik di gudang asal yang dapat dikirim.</p>
        </div>

        <button type="button" onclick="addTransferRow()" class="px-3 py-1.5 bg-sidebar text-white font-bold text-xs rounded-xl hover:bg-sidebar/90 transition flex items-center gap-1.5 shadow-sm">
          <svg class="w-3.5 h-3.5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Tambah Barang</span>
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs" id="transferTable">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-2.5 px-3 min-w-[220px]">Pilih Produk</th>
              <th class="py-2.5 px-3 min-w-[140px]">Saldo Tersedia di Asal</th>
              <th class="py-2.5 px-3 min-w-[150px]">Satuan Kirim (UOM)</th>
              <th class="py-2.5 px-3 min-w-[130px]">Jumlah Kirim</th>
              <th class="py-2.5 px-3 min-w-[160px]">Catatan / Batch</th>
              <th class="py-2.5 px-3 w-12 text-center">Hapus</th>
            </tr>
          </thead>
          <tbody id="transferTableBody" class="divide-y divide-[#ECEBF4]">
            <!-- Dynamic rows will be inserted here -->
          </tbody>
        </table>
      </div>

      <div class="p-3.5 bg-blue-50/70 rounded-2xl border border-blue-200 text-xs text-blue-900 flex items-center gap-2.5">
        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>
          Saat Anda mengklik <strong>Dispatch Transfer</strong>, saldo gudang asal otomatis terpotong dan dicatat sebagai saldo virtual <strong>Transit</strong> sampai toko tujuan mengonfirmasi penerimaan fisik.
        </span>
      </div>
    </div>

    <!-- Submit Button Group -->
    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ route('warehouse.transfers.index') }}" class="px-4 py-2.5 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
        Batal
      </a>
      <button type="submit" class="px-6 py-2.5 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
        <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
        </svg>
        <span>Dispatch Transfer (Kirim ke Transit)</span>
      </button>
    </div>
  </form>
</main>

<script>
  const availableBalances = @json($availableBalances);
  let tRowIndex = 0;

  function addTransferRow() {
    const tbody = document.getElementById('transferTableBody');
    const tr = document.createElement('tr');
    tr.id = `trow-${tRowIndex}`;
    tr.className = 'hover:bg-panelBg/40 transition';

    let productOptions = '<option value="">-- Pilih Produk --</option>';
    availableBalances.forEach(bal => {
      productOptions += `<option value="${bal.product.id}" data-onhand="${bal.on_hand_qty}" data-baseuom="${bal.product.base_uom?.symbol ?? ''}">${bal.product.name} (${bal.product.sku})</option>`;
    });

    tr.innerHTML = `
      <td class="py-2.5 px-3">
        <select name="items[${tRowIndex}][product_id]" required onchange="onTransferProductChange(${tRowIndex})" id="tproduct-select-${tRowIndex}"
                class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar">
          ${productOptions}
        </select>
      </td>
      <td class="py-2.5 px-3 font-extrabold text-sidebar text-xs" id="tonhand-label-${tRowIndex}">
        -
      </td>
      <td class="py-2.5 px-3">
        <select name="items[${tRowIndex}][product_uom_id]" required id="tuom-select-${tRowIndex}"
                class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">
          <option value="">-- Satuan --</option>
        </select>
      </td>
      <td class="py-2.5 px-3">
        <input type="number" step="0.001" min="0.001" name="items[${tRowIndex}][quantity]" value="1" required
               id="tqty-input-${tRowIndex}"
               class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-bold text-sidebar text-right focus:outline-none focus:border-sidebar">
      </td>
      <td class="py-2.5 px-3">
        <input type="text" name="items[${tRowIndex}][notes]" placeholder="Keterangan item opsional"
               class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs text-textMain focus:outline-none focus:border-sidebar">
      </td>
      <td class="py-2.5 px-3 text-center">
        <button type="button" onclick="removeTransferRow(${tRowIndex})" class="p-1.5 text-textMuted hover:text-rose-600 transition rounded-lg hover:bg-rose-50" title="Hapus Baris">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </button>
      </td>
    `;

    tbody.appendChild(tr);

    if (availableBalances.length > 0) {
      document.getElementById(`tproduct-select-${tRowIndex}`).value = availableBalances[0].product.id;
      onTransferProductChange(tRowIndex);
    }

    tRowIndex++;
  }

  function onTransferProductChange(idx) {
    const pSelect = document.getElementById(`tproduct-select-${idx}`);
    const uSelect = document.getElementById(`tuom-select-${idx}`);
    const onhandLabel = document.getElementById(`tonhand-label-${idx}`);
    const prodId = pSelect.value;

    uSelect.innerHTML = '<option value="">-- Satuan --</option>';

    if (!prodId) {
      onhandLabel.textContent = '-';
      return;
    }

    const bal = availableBalances.find(b => b.product.id == prodId);
    if (bal) {
      onhandLabel.textContent = `${parseFloat(bal.on_hand_qty).toLocaleString('id-ID')} ${bal.product.base_uom?.symbol ?? ''}`;

      if (bal.product.product_uoms) {
        bal.product.product_uoms.forEach(pu => {
          const factorTxt = parseFloat(pu.factor_to_base) > 1 ? ` (Isi ${parseFloat(pu.factor_to_base)} ${bal.product.base_uom?.symbol ?? ''})` : ' (Satuan Dasar)';
          uSelect.innerHTML += `<option value="${pu.id}">${pu.uom?.name ?? 'UOM'} [${pu.uom?.symbol ?? ''}]${factorTxt}</option>`;
        });
        uSelect.selectedIndex = 0;
      }
    }
  }

  function removeTransferRow(idx) {
    const row = document.getElementById(`trow-${idx}`);
    if (row) row.remove();
  }

  document.addEventListener('DOMContentLoaded', () => {
    addTransferRow();
  });
</script>
@endsection

@section('right_panel')
  @include('partials.right-panel')
@endsection
