@extends('layouts.app')

@section('title', "Terima Kiriman Transfer {$stockTransfer->transfer_number} — PasarHub")

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

  @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
      <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
      <span>{{ session('error') }}</span>
    </div>
  @endif

  <!-- Top Breadcrumb & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('store.index') }}" class="hover:underline">Operasional Toko</a>
        <span>/</span>
        <a href="{{ route('store.transfers.index') }}" class="hover:underline">Transfer Masuk</a>
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

    <a href="{{ route('store.transfers.index') }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
      ← Kembali ke Daftar Kiriman
    </a>
  </div>

  <!-- Routing Info Card -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm space-y-1">
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Gudang Pengirim</span>
      <h3 class="font-bold text-base text-textMain">{{ $stockTransfer->fromLocation->name }}</h3>
      <p class="text-xs text-textMuted">Waktu Dispatch: {{ $stockTransfer->dispatched_at?->format('d/m/Y H:i') }}</p>
    </div>

    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm space-y-1">
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Toko Penerima</span>
      <h3 class="font-bold text-base text-textMain">{{ $stockTransfer->toLocation->name }}</h3>
      <p class="text-xs text-textMuted">Status Penerimaan: {{ $stockTransfer->status_label }}</p>
    </div>

    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm space-y-1">
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Catatan Ekspedisi</span>
      <p class="text-xs text-textMain font-medium pt-1">{{ $stockTransfer->notes ?? 'Tidak ada catatan khusus.' }}</p>
    </div>
  </div>

  <!-- Receiving Form / Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <div>
        <h3 class="font-bold text-sm text-textMain">Konfirmasi Kuantitas Fisik Barang Tiba</h3>
        <p class="text-xs text-textMuted mt-0.5">Periksa kondisi fisik barang dari mobil pengantar sebelum mengonfirmasi.</p>
      </div>
    </div>

    @if($canReceive)
      <form action="{{ route('store.transfers.receive', $stockTransfer->id) }}" method="POST" class="space-y-6">
        @csrf

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
                <th class="py-3 px-3">Produk & SKU</th>
                <th class="py-3 px-3 text-right">Dikirim Asal</th>
                <th class="py-3 px-3 text-right">Sudah Diterima</th>
                <th class="py-3 px-3 min-w-[160px]">Jumlah Diterima Toko</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#ECEBF4]">
              @foreach($stockTransfer->items as $idx => $item)
                <tr class="hover:bg-panelBg/40 transition">
                  <input type="hidden" name="items[{{ $idx }}][item_id]" value="{{ $item->id }}">

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

                  <td class="py-3 px-3 text-right font-bold text-emerald-700">
                    {{ number_format($item->received_qty_base, 1) }}
                    <span class="text-[10px] font-normal text-textMuted">{{ $item->product->baseUom?->symbol }}</span>
                  </td>

                  <td class="py-3 px-3">
                    <input type="number" step="0.001" min="0" max="{{ $item->requested_qty_input }}"
                           name="items[{{ $idx }}][qty_received]"
                           value="{{ old("items.{$idx}.qty_received", $item->requested_qty_input - ($item->received_qty_base / ($item->factor_snapshot ?: 1))) }}"
                           class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-bold text-sidebar text-right focus:outline-none focus:border-sidebar">
                  </td>
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
            <span>Dampak Konfirmasi Penerimaan Toko:</span>
          </p>
          <p class="text-[11px] text-emerald-800">
            Stok transit akan dikurangi, saldo fisik toko Anda bertambah secara atomik, dan Moving Average HPP toko diperbarui secara otomatis menggunakan nilai harga pokok bawaan dari gudang pengirim.
          </p>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
          <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Konfirmasi Penerimaan Fisik di Toko</span>
          </button>
        </div>
      </form>
    @else
      <div class="py-6 text-center text-xs text-emerald-700 font-bold bg-emerald-50 rounded-2xl border border-emerald-200">
        Kiriman transfer ini telah selesai diterima oleh {{ $stockTransfer->receiver?->name ?? 'Staf Toko' }} pada {{ $stockTransfer->received_at?->format('d/m/Y H:i') }}.
      </div>
    @endif
  </div>
</main>
@endsection

@section('right_panel')
  @include('partials.right-panel')
@endsection
