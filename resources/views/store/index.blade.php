@extends('layouts.app')

@section('title', 'Operasional Toko & Kasir POS — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">


  <!-- Header Banner (Clean UI with Mobile Tooltip & Quick Actions) -->
  <div class="bg-panelBg p-5 sm:p-7 rounded-[26px] border border-[#ECEBF4] relative">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 flex-wrap">
          <h1 class="font-display text-lg sm:text-2xl font-bold text-textMain tracking-tight">Operasional Toko & Kasir</h1>
          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase shrink-0 bg-sidebar text-white">
            {{ $activeLocation?->code ?? 'STR-PASAR-1' }}
          </span>

          <!-- Mobile-Only Info Tooltip Trigger (ℹ️) -->
          <div class="relative md:hidden inline-flex items-center">
            <button type="button" 
                    onclick="toggleHeaderTooltip('storeHeaderPopover', event)" 
                    class="w-6 h-6 rounded-full bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white flex items-center justify-center transition p-1 shadow-xs"
                    title="Lihat Keterangan Halaman"
                    aria-label="Info Halaman">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
              </svg>
            </button>

            <!-- Mobile Popover Floating Bubble -->
            <div id="storeHeaderPopover" class="header-popover hidden fixed inset-x-4 top-20 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs">
              <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
                <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                  <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                  </svg>
                  <span>Informasi Toko</span>
                </div>
                <button type="button" onclick="closeHeaderTooltip('storeHeaderPopover')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
              </div>
              <p class="text-[11px] text-textMuted leading-relaxed">
                {{ $activeLocation?->name ?? 'Toko Pasar Raya' }} ({{ $activeLocation?->code ?? 'STR-PASAR-1' }}) • Pusat penerimaan stok transit dari gudang utama, pencatatan belanja darurat instan berfoto nota, dan monitoring rak etalase toko.
              </p>
            </div>
          </div>
        </div>

        <!-- Desktop Subtitle (Hidden on Mobile) -->
        <p class="hidden md:block text-[11px] sm:text-xs text-textMuted mt-1 max-w-2xl leading-normal">
          {{ $activeLocation?->name ?? 'Toko Pasar Raya' }} ({{ $activeLocation?->code ?? 'STR-PASAR-1' }}) • Pusat penerimaan stok transit dari gudang utama, pencatatan belanja darurat instan berfoto nota, dan monitoring rak etalase toko.
        </p>
      </div>

      <!-- Quick Action Buttons -->
      <div class="flex items-center gap-2 flex-wrap shrink-0">
        @if($user?->hasPermission('pos.access') || $user?->isSuperAdmin())
          <a href="{{ route('store.pos.terminal') }}" 
             class="px-3.5 py-2 rounded-xl bg-sidebar hover:bg-sidebar/90 text-white font-bold text-xs shadow-sm transition inline-flex items-center gap-2">
            <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Kasir POS (F2)</span>
          </a>
        @endif

        @if($user?->hasPermission('emergency.input') || $user?->isSuperAdmin())
          <a href="{{ route('store.emergency.create') }}" 
             class="px-3 py-2 rounded-xl bg-white hover:bg-amber-50 text-amber-700 font-bold text-xs border border-amber-200 shadow-xs transition inline-flex items-center gap-1.5">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span>+ Belanja Darurat</span>
          </a>
        @endif

        @if($user?->hasPermission('pos.cash_shift') || $user?->isSuperAdmin())
          <a href="{{ route('store.shifts.index') }}" 
             class="px-3 py-2 rounded-xl bg-white hover:bg-indigo-50 text-indigo-700 font-bold text-xs border border-indigo-200 shadow-xs transition inline-flex items-center gap-1.5">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>Shift Kasir</span>
          </a>
        @endif

        <a href="{{ route('store.transfers.index') }}" 
           class="px-3 py-2 rounded-xl bg-white hover:bg-blue-50 text-blue-700 font-bold text-xs border border-blue-200 shadow-xs transition inline-flex items-center gap-1.5">
          <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
          </svg>
          <span>Transfer Masuk</span>
        </a>
      </div>
    </div>
  </div>

  <!-- STATUS OPERASIONAL & STATISTIK TOKO (DI BAWAH JUDUL) -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <!-- 1. STATUS OPERASIONAL -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECEBF4] shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
          <h2 class="font-display font-bold text-base text-textMain tracking-tight">Status Operasional</h2>
        </div>
        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100 uppercase tracking-wider">Live Toko</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <!-- Status 1: Kasir Buka Shift -->
        <div class="bg-panelBg p-3.5 rounded-2xl border border-[#ECEBF4] space-y-1">
          <div class="flex items-center justify-between">
            <span class="text-[10px] uppercase font-bold text-textMuted tracking-wider">Kasir Aktif</span>
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          </div>
          <p class="font-mono font-extrabold text-lg text-emerald-700">
            {{ $stats['active_cashiers_count'] ?? ($stats['active_shift'] ? 1 : 0) }} Kasir
          </p>
          <span class="text-[10px] text-textMuted block truncate">Shift kasir berjalan</span>
        </div>

        <!-- Status 2: SKU Perlu Restock -->
        <div class="bg-panelBg p-3.5 rounded-2xl border border-[#ECEBF4] space-y-1">
          <div class="flex items-center justify-between">
            <span class="text-[10px] uppercase font-bold text-textMuted tracking-wider">Restock</span>
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
          </div>
          <p class="font-mono font-extrabold text-lg text-amber-700">
            {{ $stats['low_stock_count'] ?? 0 }} SKU
          </p>
          <span class="text-[10px] text-textMuted block truncate">Stok menipis (≤10)</span>
        </div>

        <!-- Status 3: Transfer Transit -->
        <div class="bg-panelBg p-3.5 rounded-2xl border border-[#ECEBF4] space-y-1">
          <div class="flex items-center justify-between">
            <span class="text-[10px] uppercase font-bold text-textMuted tracking-wider">Transit</span>
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
          </div>
          <p class="font-mono font-extrabold text-lg text-blue-700">
            {{ $stats['incoming_transfers_count'] ?? 0 }} Kiriman
          </p>
          <span class="text-[10px] text-textMuted block truncate">Menuju toko ini</span>
        </div>
      </div>

      <!-- Info Sesi Kasir Terkini -->
      @php $currentShift = $stats['active_shift'] ?? null; @endphp
      <div class="p-3.5 rounded-2xl bg-panelBg/80 border border-[#ECEBF4] text-xs flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          </div>
          <div>
            <span class="text-[10px] text-textMuted uppercase font-bold block">Sesi Shift Kasir Terkini</span>
            <span class="font-bold text-textMain">{{ $currentShift ? $currentShift->cashier?->name . ' (Modal: Rp ' . number_format($currentShift->starting_cash ?? $currentShift->opening_cash ?? 0, 0, ',', '.') . ')' : 'Belum ada sesi kasir aktif hari ini' }}</span>
          </div>
        </div>
        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold shrink-0 {{ $currentShift ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
          {{ $currentShift ? 'Laci Terbuka' : 'Laci Tertutup' }}
        </span>
      </div>
    </div>

    <!-- 2. STATISTIK TOKO -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECEBF4] shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-sidebar" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          <h2 class="font-display font-bold text-base text-textMain tracking-tight">Statistik Toko</h2>
        </div>
        <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider">Bulan Berjalan</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Metrik 1: Belanja Emergency Bulan Ini -->
        <div class="bg-panelBg p-4 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
          <div>
            <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Belanja Emergency</span>
            <div class="flex items-baseline gap-1.5 mt-0.5">
              <span class="text-xl font-black text-amber-700 font-mono">{{ $stats['emergency_purchases_count'] ?? 0 }}</span>
              <span class="text-[10px] font-bold text-textMuted">Transaksi</span>
            </div>
            <p class="text-[11px] text-textMuted mt-1">
              Total: <strong class="text-sidebar font-mono font-bold">Rp {{ number_format($stats['total_emergency_spent'] ?? 0, 0, ',', '.') }}</strong>
            </p>
          </div>
          <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
            </svg>
          </div>
        </div>

        <!-- Metrik 2: Total SKU Terdisplay di Rak -->
        <div class="bg-panelBg p-4 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
          <div>
            <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Display Rak Toko</span>
            <div class="flex items-baseline gap-1.5 mt-0.5">
              <span class="text-xl font-black text-sidebar font-mono">{{ $stats['total_sku_shelf'] ?? 0 }}</span>
              <span class="text-[10px] font-bold text-textMuted">SKU Aktif</span>
            </div>
            <p class="text-[11px] text-textMuted mt-1">
              Katalog: <strong class="text-sidebar font-mono font-bold">{{ $stats['total_products'] ?? 0 }} SKU</strong>
            </p>
          </div>
          <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Incoming Transfers Alert / Table -->
  <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECEBF4] shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-4">
      <div>
        <div class="flex items-center gap-2 flex-wrap">
          <h3 class="font-bold text-sm sm:text-base text-textMain flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
            <span>Barang Masuk dari Gudang</span>
          </h3>

          <!-- Mobile-Only Info Tooltip Trigger (ℹ️) -->
          <div class="relative md:hidden inline-flex items-center">
            <button type="button" 
                    onclick="toggleHeaderTooltip('storeIncomingCardPopover', event)" 
                    class="w-5 h-5 rounded-full bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white flex items-center justify-center transition p-0.5 shadow-xs"
                    title="Lihat Keterangan"
                    aria-label="Info Card">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
              </svg>
            </button>

            <!-- Mobile Popover Floating Bubble -->
            <div id="storeIncomingCardPopover" class="header-popover hidden fixed inset-x-4 top-24 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs text-textMain">
              <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
                <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                  <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                  </svg>
                  <span>Keterangan Transfer Masuk</span>
                </div>
                <button type="button" onclick="closeHeaderTooltip('storeIncomingCardPopover')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
              </div>
              <p class="text-[11px] text-textMuted leading-relaxed">
                Stok saat ini berada di lokasi transit virtual. Klik Terima untuk membukukan stok ke rak toko dan mengupdate HPP.
              </p>
            </div>
          </div>
        </div>

        <!-- Desktop Subtitle (Hidden on Mobile) -->
        <p class="hidden md:block text-[11px] sm:text-xs text-textMuted mt-0.5">
          Stok saat ini berada di lokasi virtual <code>LOC-TRANSIT</code>. Klik tombol Terima untuk membukukan stok ke rak toko dan mengupdate Moving Average HPP.
        </p>
      </div>
      <a href="{{ route('store.transfers.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sidebar/5 hover:bg-sidebar text-sidebar hover:text-white text-xs font-semibold transition shrink-0">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
        </svg>
        <span>Transfer Masuk</span>
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
                    <a href="{{ route('store.transfers.show', $trf->id) }}" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded-lg shadow-xs transition inline-flex items-center gap-1">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                      </svg>
                      <span>Terima</span>
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
          <div class="flex items-center gap-2 flex-wrap">
            <h3 class="font-bold text-sm text-textMain flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-sidebar"></span>
              <span>Saldo Fisik di Rak Toko</span>
            </h3>

            <!-- Mobile-Only Info Tooltip Trigger (ℹ️) -->
            <div class="relative md:hidden inline-flex items-center">
              <button type="button" 
                      onclick="toggleHeaderTooltip('storeShelfCardPopover', event)" 
                      class="w-5 h-5 rounded-full bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white flex items-center justify-center transition p-0.5 shadow-xs"
                      title="Lihat Keterangan"
                      aria-label="Info Card">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
              </button>

              <!-- Mobile Popover Floating Bubble -->
              <div id="storeShelfCardPopover" class="header-popover hidden fixed inset-x-4 top-24 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs text-textMain">
                <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
                  <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                    <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                    <span>Keterangan Saldo Rak</span>
                  </div>
                  <button type="button" onclick="closeHeaderTooltip('storeShelfCardPopover')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
                </div>
                <p class="text-[11px] text-textMuted leading-relaxed">
                  Stok on-hand siap jual untuk pelanggan.
                </p>
              </div>
            </div>
          </div>

          <!-- Desktop Subtitle (Hidden on Mobile) -->
          <p class="hidden md:block text-xs text-textMuted mt-0.5">Stok on-hand siap jual untuk pelanggan.</p>
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
          <div class="flex items-center gap-2 flex-wrap">
            <h3 class="font-bold text-sm text-textMain flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-brandOrange"></span>
              <span>Pembelian Emergency Terbaru</span>
            </h3>

            <!-- Mobile-Only Info Tooltip Trigger (ℹ️) -->
            <div class="relative md:hidden inline-flex items-center">
              <button type="button" 
                      onclick="toggleHeaderTooltip('storeEmergencyCardPopover', event)" 
                      class="w-5 h-5 rounded-full bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white flex items-center justify-center transition p-0.5 shadow-xs"
                      title="Lihat Keterangan"
                      aria-label="Info Card">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
              </button>

              <!-- Mobile Popover Floating Bubble -->
              <div id="storeEmergencyCardPopover" class="header-popover hidden fixed inset-x-4 top-24 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs text-textMain">
                <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
                  <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                    <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                    <span>Keterangan Pembelian Emergency</span>
                  </div>
                  <button type="button" onclick="closeHeaderTooltip('storeEmergencyCardPopover')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
                </div>
                <p class="text-[11px] text-textMuted leading-relaxed">
                  Pengadaan darurat pasar dengan foto nota.
                </p>
              </div>
            </div>
          </div>

          <!-- Desktop Subtitle (Hidden on Mobile) -->
          <p class="hidden md:block text-xs text-textMuted mt-0.5">Pengadaan darurat pasar dengan foto nota.</p>
        </div>
        <a href="{{ route('store.emergency.index') }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-sidebar/5 hover:bg-sidebar text-sidebar hover:text-white text-[11px] font-semibold transition shrink-0">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>Riwayat</span>
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
              <a href="{{ route('store.emergency.show', $emg->id) }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-sidebar hover:underline mt-0.5">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                </svg>
                <span>Nota</span>
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
