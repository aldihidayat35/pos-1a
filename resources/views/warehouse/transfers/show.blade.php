@extends('layouts.app')

@section('title', "Transfer {$stockTransfer->transfer_number} — PasarHub")

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
        <a href="{{ route('warehouse.transfers.index') }}" class="hover:underline">Transfer Barang</a>
        <span>/</span>
        <span class="text-textMain font-mono font-bold">{{ $stockTransfer->transfer_number }}</span>
      </div>
      <div class="flex items-center gap-3">
        <h1 class="font-display text-xl sm:text-2xl font-extrabold text-sidebar tracking-tight font-mono">
          {{ $stockTransfer->transfer_number }}
        </h1>
        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $stockTransfer->status_badge_class }}">
          {{ $stockTransfer->status_label }}
        </span>
      </div>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('warehouse.transfers.index') }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
        ← Daftar Transfer
      </a>
      <a href="{{ route('warehouse.ledger.index') }}" class="px-4 py-2 bg-sidebar text-white font-bold text-xs rounded-xl hover:bg-sidebar/95 transition shadow-sm">
        Buku Kartu Stok
      </a>
    </div>
  </div>

  <!-- Routing Tracking Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm space-y-2">
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Lokasi Asal (Pengirim)</span>
      <h3 class="font-bold text-base text-textMain">{{ $stockTransfer->fromLocation->name }}</h3>
      <p class="text-xs text-textMuted">Tipe: {{ $stockTransfer->fromLocation->type }}</p>
      <div class="pt-2 border-t border-[#ECEBF4] text-xs">
        <p class="text-textMuted">Dispatch: <span class="font-semibold text-textMain">{{ $stockTransfer->dispatched_at?->format('d/m/Y H:i') }}</span></p>
        <p class="text-textMuted">Petugas: <span class="font-semibold text-textMain">{{ $stockTransfer->creator?->name }}</span></p>
      </div>
    </div>

    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm space-y-2">
      <span class="text-[10px] font-bold text-blue-700 uppercase tracking-wider block">Status Perjalanan</span>
      <h3 class="font-bold text-base text-blue-900">{{ $stockTransfer->transitLocation->name }}</h3>
      <p class="text-xs text-textMuted">Lokasi Virtual Penampung Stok Transit</p>
      <div class="pt-2 border-t border-[#ECEBF4] text-xs">
        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
          Stok Berada di Transit
        </span>
      </div>
    </div>

    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm space-y-2">
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Lokasi Tujuan (Penerima)</span>
      <h3 class="font-bold text-base text-textMain">{{ $stockTransfer->toLocation->name }}</h3>
      <p class="text-xs text-textMuted">Tipe: {{ $stockTransfer->toLocation->type }}</p>
      <div class="pt-2 border-t border-[#ECEBF4] text-xs">
        <p class="text-textMuted">Diterima: <span class="font-semibold text-textMain">{{ $stockTransfer->received_at?->format('d/m/Y H:i') ?? 'Menunggu Konfirmasi Toko' }}</span></p>
        <p class="text-textMuted">Penerima: <span class="font-semibold text-textMain">{{ $stockTransfer->receiver?->name ?? '-' }}</span></p>
      </div>
    </div>
  </div>

  <!-- Items Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <h3 class="font-bold text-sm text-textMain">Rincian Barang yang Ditransfer</h3>
      <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-bold border border-blue-200">
        Status Mutasi: BIRU (TRANSFER)
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
            <th class="py-3 px-3">Produk & SKU</th>
            <th class="py-3 px-3 text-right">Kuantitas Dikirim</th>
            <th class="py-3 px-3 text-right">Kuantitas Dasar (Base)</th>
            <th class="py-3 px-3 text-right">Kuantitas Diterima Toko</th>
            @if($canViewHpp)
              <th class="py-3 px-3 text-right">Snapshot HPP Asal</th>
            @endif
            <th class="py-3 px-3">Catatan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @foreach($stockTransfer->items as $item)
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
                {{ number_format($item->requested_qty_input, 1) }}
                <span class="text-[10px] font-normal text-textMuted">{{ $item->productUom?->uom?->symbol }}</span>
              </td>
              <td class="py-3 px-3 text-right font-bold text-blue-700">
                {{ number_format($item->dispatched_qty_base, 1) }}
                <span class="text-[10px] font-normal text-textMuted">{{ $item->product->baseUom?->symbol }}</span>
              </td>
              <td class="py-3 px-3 text-right font-bold {{ $item->received_qty_base > 0 ? 'text-emerald-700' : 'text-gray-400' }}">
                {{ number_format($item->received_qty_base, 1) }}
                <span class="text-[10px] font-normal text-textMuted">{{ $item->product->baseUom?->symbol }}</span>
              </td>
              @if($canViewHpp)
                <td class="py-3 px-3 text-right font-semibold text-textMain">
                  Rp {{ number_format($item->cost_snapshot, 0, ',', '.') }}
                </td>
              @endif
              <td class="py-3 px-3 text-textMuted">
                {{ $item->notes ?? '-' }}
              </td>
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
