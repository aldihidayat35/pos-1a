@extends('layouts.app')

@section('title', "Bukti Penerimaan {$goodsReceipt->receipt_number} — PasarHub")

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Flash Notifications -->
  @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
      <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
      </svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  <!-- Top Breadcrumb & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('warehouse.index') }}" class="hover:underline">Operasional Gudang</a>
        <span>/</span>
        <a href="{{ route('warehouse.po.index') }}" class="hover:underline">Purchase Orders</a>
        <span>/</span>
        <span class="text-textMain font-mono font-bold">{{ $goodsReceipt->receipt_number }}</span>
      </div>
      <div class="flex items-center gap-3">
        <h1 class="font-display text-xl sm:text-2xl font-extrabold text-sidebar tracking-tight font-mono">
          {{ $goodsReceipt->receipt_number }}
        </h1>
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
          Tercatat & Diposting
        </span>
      </div>
    </div>

    <div class="flex items-center gap-2.5">
      @if($goodsReceipt->purchase_order_id)
        <a href="{{ route('warehouse.po.show', $goodsReceipt->purchase_order_id) }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
          ← Kembali ke PO
        </a>
      @endif
      <a href="{{ route('warehouse.ledger.index') }}" class="px-4 py-2 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <span>Lihat Kartu Stok</span>
      </a>
    </div>
  </div>

  <!-- Document Meta Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm space-y-2">
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Surat Jalan Suplayer</span>
      <h3 class="font-bold text-base text-textMain font-mono">{{ $goodsReceipt->supplier_do_number ?? '-' }}</h3>
      <p class="text-xs text-textMuted">Suplayer: <span class="font-semibold text-textMain">{{ $goodsReceipt->supplier?->name ?? 'Suplayer' }}</span></p>
      @if($goodsReceipt->notes)
        <p class="text-[11px] text-textMuted italic pt-2 border-t border-[#ECEBF4]">"{{ $goodsReceipt->notes }}"</p>
      @endif
    </div>

    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm space-y-2">
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Penerimaan & Lokasi</span>
      <h3 class="font-bold text-base text-textMain">{{ $goodsReceipt->location->name }}</h3>
      <p class="text-xs text-textMuted">Waktu: <span class="font-semibold text-textMain">{{ $goodsReceipt->posted_at?->format('d/m/Y H:i') }}</span></p>
      <p class="text-xs text-textMuted">Petugas Penerima: <span class="font-bold text-sidebar">{{ $goodsReceipt->creator?->name }}</span></p>
    </div>

    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm flex flex-col justify-between">
      <div>
        <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Surat Pesanan (PO)</span>
        <h3 class="font-bold text-base text-sidebar font-mono mt-0.5">
          @if($goodsReceipt->purchaseOrder)
            <a href="{{ route('warehouse.po.show', $goodsReceipt->purchase_order_id) }}" class="text-brandOrange hover:underline">
              {{ $goodsReceipt->purchaseOrder->po_number }}
            </a>
          @else
            -
          @endif
        </h3>
        <p class="text-xs text-textMuted mt-1">Status PO saat ini: <span class="font-bold">{{ $goodsReceipt->purchaseOrder?->status_label ?? '-' }}</span></p>
      </div>

      <div class="pt-2 border-t border-[#ECEBF4] flex items-center justify-between text-xs">
        <span class="text-textMuted font-medium">Total Item Fisik:</span>
        <span class="font-extrabold text-sidebar">{{ $goodsReceipt->items->count() }} Produk</span>
      </div>
    </div>
  </div>

  <!-- Items Received Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <h3 class="font-bold text-sm text-textMain">Daftar Barang Fisik yang Masuk ke Gudang</h3>
      <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
        Status Mutasi: HIJAU (INBOUND_PO)
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
            <th class="py-3 px-3">Produk & SKU</th>
            <th class="py-3 px-3 text-right">Kuantitas Masuk</th>
            <th class="py-3 px-3 text-right">Kuantitas Satuan Dasar</th>
            <th class="py-3 px-3">Batch / Lot</th>
            <th class="py-3 px-3">Tgl Kadaluarsa</th>
            @if($canViewHpp)
              <th class="py-3 px-3 text-right">Harga Pokok Masuk (HPP)</th>
              <th class="py-3 px-3 text-right">Total Nilai</th>
            @endif
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @foreach($goodsReceipt->items as $item)
            <tr class="hover:bg-panelBg/40 transition">
              <td class="py-3 px-3">
                <div class="flex items-center gap-2.5">
                  <x-product-thumbnail :product="$item->product" size="sm" />
                  <div>
                    <span class="font-bold text-textMain block">{{ $item->product->name }}</span>
                    <span class="text-[10px] text-brandOrange font-mono">{{ $item->product->sku }}</span>
                  </div>
                </div>
              </td>
              <td class="py-3 px-3 text-right font-extrabold text-sidebar">
                {{ number_format($item->qty_input, 1) }}
                <span class="text-[10px] font-normal text-textMuted">{{ $item->productUom?->uom?->symbol }}</span>
              </td>
              <td class="py-3 px-3 text-right font-bold text-emerald-700">
                {{ number_format($item->qty_base, 1) }}
                <span class="text-[10px] font-normal text-textMuted">{{ $item->product->baseUom?->symbol }}</span>
              </td>
              <td class="py-3 px-3 font-mono text-textMain">
                {{ $item->batch_number ?? '-' }}
              </td>
              <td class="py-3 px-3 text-textMuted">
                {{ $item->expiry_date?->format('d/m/Y') ?? 'Tidak Kadaluarsa' }}
              </td>
              @if($canViewHpp)
                <td class="py-3 px-3 text-right font-semibold text-textMain">
                  Rp {{ number_format($item->unit_cost_base, 0, ',', '.') }} /{{ $item->product->baseUom?->symbol }}
                </td>
                <td class="py-3 px-3 text-right font-extrabold text-sidebar">
                  Rp {{ number_format($item->line_total, 0, ',', '.') }}
                </td>
              @endif
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</main>
@endsection

@section('right_panel')
  @include('partials.right-panel')
@endsection
