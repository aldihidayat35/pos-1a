@extends('layouts.app')

@section('title', 'Daftar Pembelian Emergency Toko — PasarHub')

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

  <!-- Top Breadcrumb & Header (Clean UI with Mobile Tooltip) -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('store.index') }}" class="hover:underline">Operasional Toko</a>
        <span>/</span>
        <span class="text-textMain font-bold">Pembelian Emergency</span>
      </div>
      <div class="flex items-center gap-2 flex-wrap">
        <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
          Pengadaan Darurat (+Foto Nota)
        </h1>

        <!-- Mobile-Only Info Tooltip Trigger (ℹ️) -->
        <div class="relative md:hidden inline-flex items-center">
          <button type="button" 
                  onclick="toggleHeaderTooltip('storeEmergencyHeaderPopover', event)" 
                  class="w-6 h-6 rounded-full bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white flex items-center justify-center transition p-1 shadow-xs"
                  title="Lihat Keterangan Halaman"
                  aria-label="Info Halaman">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
            </svg>
          </button>

          <!-- Mobile Popover Floating Bubble -->
          <div id="storeEmergencyHeaderPopover" class="header-popover hidden fixed inset-x-4 top-20 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs">
            <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
              <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
                <span>Informasi Pembelian Emergency</span>
              </div>
              <button type="button" onclick="closeHeaderTooltip('storeEmergencyHeaderPopover')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
            </div>
            <p class="text-[11px] text-textMuted leading-relaxed">
              Pembelian mendesak saat stok toko habis mendadak tanpa bottleneck persetujuan Owner.
            </p>
          </div>
        </div>
      </div>

      <!-- Desktop Subtitle (Hidden on Mobile) -->
      <p class="hidden md:block text-xs text-textMuted mt-0.5">
        Pembelian mendesak saat stok toko habis mendadak tanpa bottleneck persetujuan Owner.
      </p>
    </div>

    <div class="flex items-center gap-2 shrink-0 flex-wrap">
      <a href="{{ route('store.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        <span>Kembali</span>
      </a>
      @if($user->hasPermission('emergency.input') || $user->isSuperAdmin())
        <a href="{{ route('store.emergency.create') }}" class="px-3.5 py-2 bg-brandOrange hover:bg-brandOrange/90 text-white font-bold text-xs rounded-xl transition inline-flex items-center gap-1.5 shadow-sm">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Belanja Baru</span>
        </a>
      @endif
    </div>
  </div>

  <!-- Search & Filter Bar -->
  <div class="bg-white rounded-3xl p-4 border border-[#ECEBF4] shadow-sm">
    <form action="{{ route('store.emergency.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 text-xs">
      <div class="flex items-center gap-1.5 bg-panelBg px-3 py-2 rounded-xl border border-[#ECEBF4] shrink-0">
        <span class="text-[11px] font-semibold text-textMuted whitespace-nowrap">Baris:</span>
        <select data-table-perpage="emergency-table" class="bg-transparent text-xs font-bold text-textMain focus:outline-none cursor-pointer">
          <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
          <option value="15" {{ request('per_page', 10) == 15 ? 'selected' : '' }}>15</option>
          <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
        </select>
      </div>

      <div class="relative flex-1 w-full">
        <input type="text" name="search" value="{{ request('search') }}"
               data-table-search="emergency-table"
               placeholder="Cari nomor dokumen, kios, atau nama produk..."
               class="w-full px-3.5 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none">
      </div>

      <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-sidebar text-white font-bold text-xs rounded-xl hover:bg-sidebar/90 transition shadow-sm">
        Cari
      </button>

      @if(request()->anyFilled(['search', 'per_page']))
        <a href="{{ route('store.emergency.index') }}" class="px-2.5 py-2 text-xs font-bold text-brandCoral hover:underline text-center">
          Reset
        </a>
      @endif
    </form>
  </div>

  <!-- Purchases Table Container -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 sm:p-6 shadow-sm" data-async-table="emergency-table">
    @if($purchases->isEmpty())
      <div class="py-12 text-center text-textMuted text-xs">
        Belum ada riwayat pembelian emergency di lokasi toko ini.
      </div>
    @else
      <!-- Desktop View: Table -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3 px-3">No. Dokumen</th>
              <th class="py-3 px-3">Barang Dibeli</th>
              <th class="py-3 px-3">Pedagang / Kios</th>
              <th class="py-3 px-3 text-right">Kuantitas</th>
              <th class="py-3 px-3 text-right">Total Biaya</th>
              <th class="py-3 px-3">Waktu & Petugas</th>
              <th class="py-3 px-3 text-center">Bukti Nota</th>
              <th class="py-3 px-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @foreach($purchases as $emg)
              <tr class="hover:bg-panelBg/60 transition">
                <td class="py-3.5 px-3 font-extrabold text-sidebar font-mono">
                  <a href="{{ route('store.emergency.show', $emg->id) }}" class="text-brandOrange hover:underline">
                    {{ $emg->purchase_number }}
                  </a>
                </td>
                <td class="py-3.5 px-3">
                  <span class="font-bold text-textMain block">{{ $emg->product->name }}</span>
                  <span class="text-[10px] text-textMuted font-mono">{{ $emg->product->sku }}</span>
                </td>
                <td class="py-3.5 px-3 font-medium text-textMain">
                  {{ $emg->merchant_name }}
                </td>
                <td class="py-3.5 px-3 text-right font-extrabold text-sidebar">
                  {{ number_format($emg->qty_input, 1) }}
                  <span class="text-[10px] font-normal text-textMuted">{{ $emg->productUom?->uom?->symbol }}</span>
                </td>
                <td class="py-3.5 px-3 text-right font-extrabold text-amber-700">
                  Rp {{ number_format($emg->total_amount, 0, ',', '.') }}
                </td>
                <td class="py-3.5 px-3 text-textMuted text-[11px]">
                  <span>{{ $emg->posted_at?->format('d/m/Y H:i') }}</span>
                  <span class="block text-[10px] text-textMain font-semibold">Oleh: {{ $emg->creator?->name }}</span>
                </td>
                <td class="py-3.5 px-3 text-center">
                  @if($emg->receipt_photo_path)
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                      Ada Foto
                    </span>
                  @else
                    <span class="text-rose-500 font-bold">Tanpa Foto</span>
                  @endif
                </td>
                <td class="py-3.5 px-3 text-center">
                  <a href="{{ route('store.emergency.show', $emg->id) }}" class="px-2.5 py-1 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-[11px] rounded-lg transition border border-[#ECEBF4]">
                    Detail
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Mobile View: Card Stack -->
      <div class="block md:hidden divide-y divide-[#ECEBF4]">
        @foreach($purchases as $emg)
          <div class="py-4 space-y-3">
            <div class="flex items-start justify-between gap-2">
              <div>
                <a href="{{ route('store.emergency.show', $emg->id) }}" class="font-extrabold text-brandOrange hover:underline text-sm font-mono block">
                  {{ $emg->purchase_number }}
                </a>
                <span class="text-[10px] text-textMuted">{{ $emg->posted_at?->format('d/m/Y H:i') }} • Oleh: {{ $emg->creator?->name }}</span>
              </div>
              <div>
                @if($emg->receipt_photo_path)
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    Ada Foto
                  </span>
                @else
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                    Tanpa Foto
                  </span>
                @endif
              </div>
            </div>

            <div>
              <h4 class="font-bold text-sm text-textMain">{{ $emg->product->name }}</h4>
              <span class="text-[10px] text-textMuted font-mono">{{ $emg->product->sku }} • Kios: {{ $emg->merchant_name }}</span>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs bg-panelBg/60 p-2.5 rounded-xl border border-[#ECEBF4]">
              <div>
                <span class="text-[10px] uppercase font-bold text-textMuted block">Kuantitas</span>
                <span class="font-mono font-bold text-sidebar text-xs">
                  {{ number_format($emg->qty_input, 1) }} {{ $emg->productUom?->uom?->symbol }}
                </span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-textMuted block">Total Biaya</span>
                <span class="font-mono font-black text-amber-700 text-xs">
                  Rp {{ number_format($emg->total_amount, 0, ',', '.') }}
                </span>
              </div>
            </div>

            <div class="flex items-center justify-end pt-1">
              <a href="{{ route('store.emergency.show', $emg->id) }}" class="px-3 py-1.5 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
                Lihat Detail Nota
              </a>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4 pt-4 border-t border-[#ECEBF4]">
        {{ $purchases->links() }}
      </div>
    @endif
  </div>
</main>
@endsection

@section('right_panel')
  @include('store.partials.right-panel')
@endsection
