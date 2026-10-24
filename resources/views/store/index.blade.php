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

  <!-- Header Banner (Clean UI with Mobile Tooltip) -->
  <div class="bg-panelBg p-5 sm:p-7 rounded-[26px] border border-[#ECEBF4] relative">
    <div class="flex items-center gap-2 flex-wrap">
      <h1 class="font-display text-lg sm:text-2xl font-bold text-textMain tracking-tight">Operasional Toko & Kasir</h1>
      <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase shrink-0 bg-sidebar text-white">
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

@section('right_panel')
  @include('store.partials.right-panel')
@endsection
