@extends('layouts.app')

@section('title', "Terima Kiriman Transfer {$stockTransfer->transfer_number} — PasarHub")

@section('content')
<main class="flex-1 w-full space-y-6">


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
<div class="space-y-6">

  <!-- CARD 1: INFORMASI EKSPEDISI & RUTE (SESUAI GAMBAR) -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <div>
        <h2 class="font-display font-bold text-base text-textMain tracking-tight">Informasi Rute & Ekspedisi</h2>
        <p class="text-[11px] text-textMuted mt-0.5">Surat Jalan Antar-Cabang (Manifest)</p>
      </div>
      <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $stockTransfer->status_badge_class }}">
        {{ $stockTransfer->status_label }}
      </span>
    </div>

    <div class="space-y-3">
      <!-- 1. GUDANG PENGIRIM -->
      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] space-y-1 shadow-xs">
        <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">GUDANG PENGIRIM</span>
        <h3 class="font-bold text-sm text-textMain">{{ $stockTransfer->fromLocation->name }}</h3>
        <p class="text-[11px] text-textMuted font-medium">
          Waktu Dispatch: <span class="font-semibold text-textMain">{{ $stockTransfer->dispatched_at?->format('d/m/Y H:i') ?? '-' }}</span>
        </p>
        <p class="text-[10px] text-textMuted pt-0.5">
          Petugas Dispatch: <strong class="text-sidebar font-semibold">{{ $stockTransfer->creator?->name ?? 'Admin Gudang' }}</strong>
        </p>
      </div>

      <!-- 2. TOKO PENERIMA -->
      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] space-y-1 shadow-xs">
        <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">TOKO PENERIMA</span>
        <h3 class="font-bold text-sm text-textMain">{{ $stockTransfer->toLocation->name }}</h3>
        <p class="text-[11px] text-textMuted font-medium">
          Status Penerimaan: <span class="font-semibold text-sidebar">{{ $stockTransfer->status_label }}</span>
        </p>
        <p class="text-[10px] text-textMuted pt-0.5">
          Petugas Penerima: <strong class="text-sidebar font-semibold">{{ $stockTransfer->receiver?->name ?? 'Menunggu Verifikasi Staf' }}</strong>
          @if($stockTransfer->received_at)
            <span class="block text-[10px] text-emerald-700 font-medium">Waktu Terima: {{ $stockTransfer->received_at->format('d/m/Y H:i') }}</span>
          @endif
        </p>
      </div>

      <!-- 3. CATATAN EKSPEDISI -->
      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] space-y-1 shadow-xs">
        <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">CATATAN EKSPEDISI</span>
        <p class="text-xs text-textMain font-medium leading-relaxed pt-0.5">
          {{ $stockTransfer->notes ?: 'Tidak ada catatan khusus armada/pengiriman.' }}
        </p>
      </div>
    </div>
  </div>

  <!-- CARD 2: RINGKASAN MANIFEST & MUATAN BARANG (DATA TAMBAHAN) -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <h2 class="font-display font-bold text-base text-textMain tracking-tight">Ringkasan Muatan Stok</h2>
      <span class="text-[10px] font-bold text-sidebar bg-purple-50 px-2 py-0.5 rounded-full border border-purple-100 uppercase tracking-wider">
        {{ $stockTransfer->transfer_number }}
      </span>
    </div>

    @php
      $totalItems = $stockTransfer->items->count();
      $totalRequested = (float) $stockTransfer->items->sum('requested_qty_input');
      $totalReceived = (float) $stockTransfer->items->sum('received_qty_base');
      $completionPercent = $totalRequested > 0 ? min(100, round(($totalReceived / $totalRequested) * 100)) : 0;
    @endphp

    <div class="grid grid-cols-2 gap-2.5">
      <div class="bg-white p-3.5 rounded-2xl border border-[#ECEBF4] shadow-xs">
        <span class="text-[10px] uppercase font-bold text-textMuted block">Total SKU</span>
        <span class="font-mono font-extrabold text-lg text-sidebar block mt-0.5">{{ $totalItems }}</span>
        <span class="text-[10px] text-textMuted">Jenis Produk</span>
      </div>

      <div class="bg-white p-3.5 rounded-2xl border border-[#ECEBF4] shadow-xs">
        <span class="text-[10px] uppercase font-bold text-textMuted block">Total Kirim</span>
        <span class="font-mono font-extrabold text-lg text-sidebar block mt-0.5">{{ number_format($totalRequested, 0) }}</span>
        <span class="text-[10px] text-textMuted">Unit Fisik</span>
      </div>
    </div>

    <!-- Progress Verifikasi Penerimaan -->
    <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] space-y-2 shadow-xs">
      <div class="flex items-center justify-between text-xs">
        <span class="font-bold text-textMain">Progress Verifikasi</span>
        <span class="font-mono font-extrabold {{ $completionPercent >= 100 ? 'text-emerald-600' : 'text-blue-600' }}">
          {{ $completionPercent }}% Selesai
        </span>
      </div>

      <div class="w-full bg-[#ECEBF4] rounded-full h-2 overflow-hidden">
        <div class="h-2 rounded-full transition-all duration-300 {{ $completionPercent >= 100 ? 'bg-emerald-500' : 'bg-blue-600' }}" 
             style="width: {{ $completionPercent }}%"></div>
      </div>

      <div class="flex justify-between items-center text-[11px] text-textMuted pt-1">
        <span>Fisik Tiba: <strong class="text-emerald-700 font-mono">{{ number_format($totalReceived, 0) }}</strong></span>
        <span>Sisa: <strong class="text-amber-700 font-mono">{{ number_format(max(0, $totalRequested - $totalReceived), 0) }}</strong></span>
      </div>
    </div>

    <!-- Lokasi Transit Virtual -->
    <div class="bg-white p-3.5 rounded-2xl border border-[#ECEBF4] text-xs flex items-center justify-between shadow-xs">
      <div>
        <span class="text-[10px] text-textMuted uppercase font-bold block">Lokasi Transit</span>
        <span class="font-bold text-textMain">{{ $stockTransfer->transitLocation?->name ?? 'Virtual Transit Location' }}</span>
      </div>
      <span class="font-mono text-[10px] font-extrabold text-sidebar bg-panelBg px-2 py-1 rounded-lg border border-[#ECEBF4]">
        {{ $stockTransfer->transitLocation?->code ?? 'LOC-TRANSIT' }}
      </span>
    </div>
  </div>

  <!-- CARD 3: PETUNJUK OPERASIONAL TOKO -->
  <div class="bg-panelBg rounded-[28px] p-5 border border-[#ECEBF4] space-y-2 shadow-sm text-xs text-textMuted">
    <div class="flex items-center gap-1.5 font-bold text-sidebar">
      <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
      </svg>
      <span>Petunjuk Serah Terima Fisik</span>
    </div>
    <p class="text-[11px] leading-relaxed">
      Pastikan jumlah dan kemasan barang dari kurir telah dihitung sebelum menekan tombol konfirmasi. Konfirmasi akan langsung menambah stok rak toko Anda dan mengupdate harga pokok rata-rata (Moving Average HPP).
    </p>
  </div>

</div>
@endsection
