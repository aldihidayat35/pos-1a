@extends('layouts.app')

@section('title', 'Operasional Toko & Kasir POS — PasarHub')

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

  <!-- Header Banner (Clean) -->
  <div class="bg-panelBg p-5 sm:p-7 rounded-[26px] border border-[#ECEBF4]">
    <h1 class="font-display text-lg sm:text-2xl font-bold text-textMain">Operasional Toko & Kasir</h1>
    <p class="text-[11px] sm:text-xs text-textMuted mt-1 max-w-2xl">
      {{ $activeLocation?->name ?? 'Toko Pasar Raya' }} ({{ $activeLocation?->code ?? 'STR-PASAR-1' }}) • Pusat penerimaan stok transit dari gudang utama, pencatatan belanja darurat instan berfoto nota, dan monitoring rak etalase toko.
    </p>
  </div>

  <!-- Incoming Transfers Alert / Table -->
  <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECEBF4] shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-4">
      <div>
        <h3 class="font-bold text-sm sm:text-base text-textMain flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
          <span>Barang Masuk dari Gudang (Menunggu Diterima di Toko)</span>
        </h3>
        <p class="text-[11px] sm:text-xs text-textMuted mt-0.5">
          Stok saat ini berada di lokasi virtual <code>LOC-TRANSIT</code>. Klik tombol Terima untuk membukukan stok ke rak toko dan mengupdate Moving Average HPP.
        </p>
      </div>
      <a href="{{ route('store.transfers.index') }}" class="text-[11px] sm:text-xs font-bold text-sidebar hover:underline">
        Lihat Semua Transfer Masuk →
      </a>
    </div>

    @if($incomingTransfers->isEmpty())
      <div class="py-8 text-center text-xs text-textMuted bg-panelBg/40 rounded-2xl border border-dashed border-[#ECEBF4]">
        Tidak ada transfer barang transit yang sedang menuju ke toko saat ini.
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-2.5 px-3">No. Transfer</th>
              <th class="py-2.5 px-3">Asal Pengirim</th>
              <th class="py-2.5 px-3">Rincian Barang</th>
              <th class="py-2.5 px-3">Waktu Dispatch</th>
              <th class="py-2.5 px-3 text-center">Status</th>
              <th class="py-2.5 px-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @foreach($incomingTransfers as $trf)
              <tr class="hover:bg-panelBg/40 transition">
                <td class="py-3 px-3 font-mono font-extrabold text-sidebar">
                  {{ $trf->transfer_number }}
                </td>
                <td class="py-3 px-3">
                  <span class="font-bold text-textMain">{{ $trf->fromLocation->name }}</span>
                  <span class="text-[10px] text-textMuted block font-mono">{{ $trf->fromLocation->code }}</span>
                </td>
                <td class="py-3 px-3">
                  @foreach($trf->items as $item)
                    <div class="text-[11px]">
                      <span class="font-bold text-sidebar">{{ $item->product->name }}</span>:
                      <span class="font-extrabold text-blue-700">{{ number_format($item->qty_dispatched, 1) }}</span>
                    </div>
                  @endforeach
                </td>
                <td class="py-3 px-3 text-textMuted text-[11px]">
                  {{ $trf->dispatched_at?->format('d/m/Y H:i') }}
                  <span class="text-[10px] text-textMain block">Oleh: {{ $trf->creator?->name }}</span>
                </td>
                <td class="py-3 px-3 text-center">
                  <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-blue-50 text-blue-700 border border-blue-200">
                    Dalam Transit
                  </span>
                </td>
                <td class="py-3 px-3 text-center">
                  @if($user->hasPermission('transfer.receive') || $user->isSuperAdmin())
                    <a href="{{ route('store.transfers.show', $trf->id) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition inline-flex items-center gap-1.5">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                      </svg>
                      <span>Terima di Toko</span>
                    </a>
                  @else
                    <span class="text-[11px] text-textMuted">Hanya Kasir / PIC</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  <!-- 2 Columns: Shelf Balances & Recent Emergency -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Left: Shelf Balances -->
    <div class="bg-white rounded-3xl p-6 border border-[#ECEBF4] shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
        <div>
          <h3 class="font-bold text-sm text-textMain flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-sidebar"></span>
            <span>Saldo Fisik di Rak Toko</span>
          </h3>
          <p class="text-xs text-textMuted mt-0.5">Stok on-hand siap jual untuk pelanggan.</p>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-2.5 px-3">Nama Produk</th>
              <th class="py-2.5 px-3 text-right">Stok Rak</th>
              @if($canViewHpp)
                <th class="py-2.5 px-3 text-right">HPP Toko</th>
              @endif
              <th class="py-2.5 px-3 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @forelse($balances as $bal)
              <tr class="hover:bg-panelBg/40 transition">
                <td class="py-3 px-3">
                  <span class="font-bold text-textMain block">{{ $bal->product->name }}</span>
                  <span class="text-[10px] text-textMuted font-mono">{{ $bal->product->sku }}</span>
                </td>
                <td class="py-3 px-3 text-right font-black text-sidebar text-xs">
                  {{ number_format($bal->on_hand_qty, 2) }}
                  <span class="text-[10px] font-normal text-textMuted">{{ $bal->product->baseUom?->symbol }}</span>
                </td>
                @if($canViewHpp)
                  <td class="py-3 px-3 text-right font-bold text-textMain text-xs">
                    Rp {{ number_format($bal->avg_unit_cost, 0, ',', '.') }}
                  </td>
                @endif
                <td class="py-3 px-3 text-center">
                  @if($bal->on_hand_qty > 10)
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold text-[10px]">Tersedia</span>
                  @elseif($bal->on_hand_qty > 0)
                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-full font-bold text-[10px]">Menipis</span>
                  @else
                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-full font-bold text-[10px]">Kosong</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="py-6 text-center text-textMuted text-xs">Belum ada saldo barang di toko ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Right: Recent Emergency Purchases -->
    <div class="bg-white rounded-3xl p-6 border border-[#ECEBF4] shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
        <div>
          <h3 class="font-bold text-sm text-textMain flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-brandOrange"></span>
            <span>Pembelian Emergency Terbaru</span>
          </h3>
          <p class="text-xs text-textMuted mt-0.5">Pengadaan darurat pasar dengan foto nota.</p>
        </div>
        <a href="{{ route('store.emergency.index') }}" class="text-xs font-bold text-sidebar hover:underline">
          Lihat Semua →
        </a>
      </div>

      <div class="space-y-3">
        @forelse($recentEmergencies as $emg)
          <div class="p-3.5 bg-panelBg/70 rounded-2xl border border-[#ECEBF4] flex items-center justify-between hover:border-brandOrange transition">
            <div class="space-y-0.5">
              <div class="flex items-center gap-2">
                <span class="font-mono font-extrabold text-brandOrange text-xs">{{ $emg->purchase_number }}</span>
                <span class="text-[10px] text-textMuted">• {{ $emg->merchant_name }}</span>
              </div>
              <div class="text-xs font-bold text-textMain">
                {{ $emg->product->name }} (+{{ number_format($emg->qty_input, 1) }} {{ $emg->productUom?->uom?->symbol }})
              </div>
              <div class="text-[10px] text-textMuted">
                {{ $emg->posted_at?->format('d/m H:i') }} • Oleh {{ $emg->creator?->name }}
              </div>
            </div>

            <div class="text-right">
              <span class="text-xs font-extrabold text-amber-700 block font-mono">
                Rp {{ number_format($emg->total_amount, 0, ',', '.') }}
              </span>
              <a href="{{ route('store.emergency.show', $emg->id) }}" class="text-[11px] font-bold text-sidebar hover:underline">
                Lihat Nota →
              </a>
            </div>
          </div>
        @empty
          <div class="py-8 text-center text-xs text-textMuted bg-panelBg/40 rounded-2xl border border-dashed border-[#ECEBF4]">
            Belum ada pembelian emergency di toko ini.
          </div>
        @endforelse
      </div>
    </div>
  </div>
</main>
@endsection

@section('right_panel')
  @include('store.partials.right-panel')
@endsection
