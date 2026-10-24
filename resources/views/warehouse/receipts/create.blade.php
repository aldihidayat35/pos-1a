@extends('layouts.app')

@section('title', "Penerimaan Barang PO {$purchaseOrder->po_number} — PasarHub")

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Error Validation Alert -->
  @if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <span>Harap perbaiki kesalahan input penerimaan:</span>
      </div>
      <ul class="list-disc pl-5 font-normal text-[11px] space-y-0.5">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
      <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
      <span>{{ session('error') }}</span>
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
        <a href="{{ route('warehouse.po.show', $purchaseOrder->id) }}" class="hover:underline font-mono">{{ $purchaseOrder->po_number }}</a>
        <span>/</span>
        <span class="text-textMain font-bold">Penerimaan Fisik (Inbound)</span>
      </div>
      <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
        Catat Penerimaan Barang Masuk (Goods Receipt)
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Verifikasi Surat Jalan suplayer, penimbangan fisik, batch lot, dan pembaruan Moving Average HPP gudang.
      </p>
    </div>

    <a href="{{ route('warehouse.po.show', $purchaseOrder->id) }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
      Batal
    </a>
  </div>

  <!-- PO Reference Summary Card -->
  <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
    <div>
      <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Surat Pesanan Rujukan</span>
      <h4 class="font-extrabold text-sm text-emerald-950 font-mono mt-0.5">
        {{ $purchaseOrder->po_number }} • {{ $purchaseOrder->supplier->name }}
      </h4>
      <p class="text-[11px] text-emerald-800 mt-0.5">
        Gudang Tujuan: <span class="font-bold">{{ $purchaseOrder->destinationLocation->name }}</span>
      </p>
    </div>
    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $purchaseOrder->status_badge_class }} self-start sm:self-auto">
      Status PO: {{ $purchaseOrder->status_label }}
    </span>
  </div>

  <!-- Receiving Form -->
  <form action="{{ route('warehouse.receipts.store', $purchaseOrder->id) }}" method="POST" class="space-y-6">
    @csrf

    <!-- Document Info Card -->
    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
      <h3 class="font-bold text-sm text-textMain border-b border-[#ECEBF4] pb-3 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-brandOrange"></span>
        <span>1. Verifikasi Dokumen Pengiriman Suplayer</span>
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
        <div>
          <label class="block font-bold text-textMain mb-1.5">
            Nomor Surat Jalan Vendor (DO / Delivery Order) <span class="text-rose-500">*</span>
          </label>
          <input type="text" name="supplier_do_number" value="{{ old('supplier_do_number') }}" required
                 placeholder="Contoh: SJ-2026/10/8812 atau DO-PABRIK-991"
                 class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar">
          <p class="text-[10px] text-textMuted mt-1">Nomor fisik surat jalan yang dibawa supir / ekspedisi pengantar.</p>
        </div>

        <div>
          <label class="block font-bold text-textMain mb-1.5">
            Catatan Pemeriksaan Fisik & QC Dermaga
          </label>
          <input type="text" name="notes" value="{{ old('notes') }}"
                 placeholder="Contoh: Kondisi karung kering & bersih, segel utuh, timbangan pas."
                 class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">
        </div>
      </div>
    </div>

    <!-- Items Receiving Table -->
    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
        <div>
          <h3 class="font-bold text-sm text-textMain flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>2. Verifikasi Fisik Jumlah Barang Masuk</span>
          </h3>
          <p class="text-xs text-textMuted mt-0.5">Isi kuantitas yang benar-benar diterima di dermaga. Dukungan penerimaan parsial tersedia.</p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3 px-3 min-w-[200px]">Barang & Satuan</th>
              <th class="py-3 px-3 text-right min-w-[100px]">Dipesan</th>
              <th class="py-3 px-3 text-right min-w-[100px]">Sisa Pesanan</th>
              <th class="py-3 px-3 min-w-[140px]">Jumlah Diterima</th>
              <th class="py-3 px-3 min-w-[150px]">No. Batch / Lot</th>
              <th class="py-3 px-3 min-w-[140px]">Tgl Kadaluarsa</th>
              @if($canViewHpp)
                <th class="py-3 px-3 min-w-[130px] text-right">Harga Satuan (Rp)</th>
              @endif
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @foreach($purchaseOrder->items as $idx => $item)
              <tr class="hover:bg-panelBg/40 transition">
                <input type="hidden" name="items[{{ $idx }}][purchase_order_item_id]" value="{{ $item->id }}">

                <td class="py-3.5 px-3">
                  <div class="flex items-center gap-2.5">
                    <x-product-thumbnail :product="$item->product" size="sm" />
                    <div>
                      <span class="font-bold text-textMain block">{{ $item->product->name }}</span>
                      <div class="flex items-center gap-2 text-[10px] mt-0.5">
                        <span class="text-brandOrange font-mono">{{ $item->product->sku }}</span>
                        <span class="text-textMuted">• Satuan: {{ $item->productUom?->uom?->name }} [{{ $item->productUom?->uom?->symbol }}]</span>
                      </div>
                    </div>
                  </div>
                </td>

                <td class="py-3.5 px-3 text-right font-extrabold text-sidebar">
                  {{ number_format($item->quantity_ordered, 1) }}
                  <span class="text-[10px] font-normal text-textMuted">{{ $item->productUom?->uom?->symbol }}</span>
                </td>

                <td class="py-3.5 px-3 text-right font-bold {{ $item->remaining_base_qty > 0 ? 'text-amber-700' : 'text-gray-400' }}">
                  {{ number_format($item->remaining_input_qty, 1) }}
                  <span class="text-[10px] font-normal text-textMuted">{{ $item->productUom?->uom?->symbol }}</span>
                </td>

                <!-- Input Qty Received -->
                <td class="py-3.5 px-3">
                  <input type="number" step="0.001" min="0" max="{{ $item->remaining_input_qty }}"
                         name="items[{{ $idx }}][qty_input]"
                         value="{{ old("items.{$idx}.qty_input", $item->remaining_input_qty) }}"
                         class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-bold text-sidebar text-right focus:outline-none focus:border-sidebar">
                  <span class="text-[10px] text-textMuted block text-right mt-0.5">
                    = <span id="base-calc-{{ $idx }}">{{ number_format($item->remaining_base_qty, 1) }}</span> {{ $item->product->baseUom?->symbol }}
                  </span>
                </td>

                <!-- Input Batch Number -->
                <td class="py-3.5 px-3">
                  <input type="text" name="items[{{ $idx }}][batch_number]"
                         value="{{ old("items.{$idx}.batch_number", 'LOT-' . date('Ymd') . '-' . $item->product_id) }}"
                         placeholder="Nomor Lot/Batch"
                         class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-mono text-textMain focus:outline-none focus:border-sidebar">
                </td>

                <!-- Input Expiry Date -->
                <td class="py-3.5 px-3">
                  <input type="date" name="items[{{ $idx }}][expiry_date]"
                         value="{{ old("items.{$idx}.expiry_date", date('Y-m-d', strtotime('+1 year'))) }}"
                         class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs text-textMain focus:outline-none focus:border-sidebar">
                </td>

                <!-- Unit Cost Input (Only if user has HPP view permission) -->
                @if($canViewHpp)
                  <td class="py-3.5 px-3 text-right">
                    <input type="number" step="1" min="0"
                           name="items[{{ $idx }}][unit_cost_input]"
                           value="{{ old("items.{$idx}.unit_cost_input", $item->unit_purchase_price) }}"
                           class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-bold text-sidebar text-right focus:outline-none focus:border-sidebar">
                  </td>
                @else
                  <input type="hidden" name="items[{{ $idx }}][unit_cost_input]" value="{{ $item->unit_purchase_price }}">
                @endif
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200 text-xs text-emerald-900 space-y-1">
        <p class="font-bold flex items-center gap-1.5">
          <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>Pembaruan Otomatis Kartu Stok & HPP (Moving Average):</span>
        </p>
        <p class="text-[11px] text-emerald-800">
          Saat formulir ini disimpan, stok fisik gudang akan bertambah secara atomik, ledger mutasi Hijau (Inbound PO) akan dicatat secara permanen, dan HPP rata-rata berjalan akan dihitung ulang secara otomatis.
        </p>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ route('warehouse.po.show', $purchaseOrder->id) }}" class="px-4 py-2.5 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
        Batal
      </a>
      <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <span>Konfirmasi Penerimaan & Posting ke Stok</span>
      </button>
    </div>
  </form>
</main>
@endsection

@section('right_panel')
  @include('partials.right-panel')
@endsection
