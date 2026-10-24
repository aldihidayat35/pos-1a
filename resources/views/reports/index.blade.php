@extends('layouts.app')

@section('title', 'Laporan Finansial & Dashboard Owner — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Bar & Header -->
  <!-- Top Bar & Header (Clean UI with Mobile Tooltip) -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <span>Pengawasan &amp; Tata Kelola</span>
        <span>/</span>
        <span class="text-sidebar font-bold">Laporan Finansial</span>
      </div>
      <div class="flex items-center gap-2 flex-wrap">
        <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
          Dasbor Finansial & Owner
        </h1>
        @if($user->isOwner())
          <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brandLavender text-sidebar border border-[#D5D2E8]">
            <span class="w-1.5 h-1.5 rounded-full bg-sidebar"></span>
            <span>Owner Read-Only</span>
          </span>
        @endif

        <!-- Mobile-Only Info Tooltip Trigger (ℹ️) -->
        <div class="relative md:hidden inline-flex items-center">
          <button type="button" 
                  onclick="toggleHeaderTooltip('reportsHeaderPopover', event)" 
                  class="w-6 h-6 rounded-full bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white flex items-center justify-center transition p-1 shadow-xs"
                  title="Lihat Keterangan Halaman"
                  aria-label="Info Halaman">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
            </svg>
          </button>

          <!-- Mobile Popover Floating Bubble -->
          <div id="reportsHeaderPopover" class="header-popover hidden fixed inset-x-4 top-20 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs">
            <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
              <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
                <span>Informasi Dasbor Finansial</span>
              </div>
              <button type="button" onclick="closeHeaderTooltip('reportsHeaderPopover')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
            </div>
            <p class="text-[11px] text-textMuted leading-relaxed">
              Visibilitas penuh arus omzet terealisasi, laba kotor, valuasi aset persediaan, risiko piutang B2B, dan pengeluaran darurat.
            </p>
          </div>
        </div>
      </div>

      <!-- Desktop Subtitle (Hidden on Mobile) -->
      <p class="hidden md:block text-xs text-textMuted mt-0.5">
        Visibilitas penuh arus omzet terealisasi, laba kotor, valuasi aset persediaan, risiko piutang B2B, dan pengeluaran darurat.
      </p>
    </div>

    <!-- Export Action Dropdown / Buttons -->
    <div class="flex items-center gap-2 shrink-0 flex-wrap">
      @if($user->hasPermission('reports.export') || $user->isOwner() || $user->isSuperAdmin())
        <div class="relative inline-block text-left" id="exportDropdownContainer">
          <a href="{{ route('reports.export.financial', request()->query()) }}" class="px-3.5 py-2 bg-sidebar hover:bg-slate-800 text-white text-xs font-bold rounded-2xl transition inline-flex items-center gap-1.5 shadow-sm">
            <svg class="w-3.5 h-3.5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Ekspor CSV</span>
          </a>
        </div>
      @endif

      <a href="{{ route('audit.index') }}" class="px-3.5 py-2 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition inline-flex items-center gap-1.5 shadow-xs">
        <svg class="w-3.5 h-3.5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
        </svg>
        <span>Audit Board</span>
      </a>
    </div>
  </div>

  <!-- Filter & Date Range Bar -->
  <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm">
    <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
      <!-- Preset Buttons -->
      <div class="flex flex-wrap items-center gap-2">
        <span class="text-xs font-bold text-sidebar uppercase tracking-wider mr-1">Periode:</span>
        <button type="submit" name="preset" value="today" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $preset === 'today' ? 'bg-sidebar text-white shadow-sm' : 'bg-panelBg hover:bg-gray-200 text-textMuted' }}">
          Hari Ini
        </button>
        <button type="submit" name="preset" value="this_week" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $preset === 'this_week' ? 'bg-sidebar text-white shadow-sm' : 'bg-panelBg hover:bg-gray-200 text-textMuted' }}">
          Minggu Ini
        </button>
        <button type="submit" name="preset" value="this_month" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $preset === 'this_month' ? 'bg-sidebar text-white shadow-sm' : 'bg-panelBg hover:bg-gray-200 text-textMuted' }}">
          Bulan Ini
        </button>
        <button type="submit" name="preset" value="last_month" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $preset === 'last_month' ? 'bg-sidebar text-white shadow-sm' : 'bg-panelBg hover:bg-gray-200 text-textMuted' }}">
          Bulan Lalu
        </button>
      </div>

      <!-- Location & Date Inputs -->
      <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-1.5">
          <span class="text-xs font-bold text-textMuted">Lokasi:</span>
          <select name="location_id" class="text-xs rounded-xl border border-[#ECEBF4] px-3 py-1.5 bg-white focus:outline-none focus:border-sidebar" onchange="this.form.submit()">
            <option value="">Semua Lokasi Terpusat</option>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (string) request('location_id') === (string) $loc->id ? 'selected' : '' }}>
                {{ $loc->name }} ({{ $loc->type }})
              </option>
            @endforeach
          </select>
        </div>

        <div class="flex items-center gap-1.5">
          <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="text-xs rounded-xl border border-[#ECEBF4] px-2.5 py-1.5 bg-white">
          <span class="text-textMuted text-xs">s/d</span>
          <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="text-xs rounded-xl border border-[#ECEBF4] px-2.5 py-1.5 bg-white">
          <input type="hidden" name="preset" value="custom">
          <button type="submit" class="px-3 py-1.5 bg-sidebar text-white text-xs font-bold rounded-xl">
            Terapkan
          </button>
        </div>
      </div>
    </form>
  </div>

  <!-- 4 Primary Financial KPI Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1: Omzet Terealisasi -->
    <a href="{{ route('reports.sales', request()->query()) }}" class="bg-white rounded-3xl p-6 border border-[#ECEBF4] hover:border-sidebar/40 transition shadow-sm block group">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-textMuted">Omzet Terealisasi</span>
        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>
      <h3 class="text-2xl font-extrabold text-sidebar mt-2">
        Rp {{ number_format($summary['revenue']['total'], 0, ',', '.') }}
      </h3>
      <p class="text-[11px] text-textMuted mt-1">
        Ritel: Rp {{ number_format($summary['revenue']['pos'], 0, ',', '.') }} • B2B: Rp {{ number_format($summary['revenue']['b2b'], 0, ',', '.') }}
      </p>
      <div class="mt-3 pt-3 border-t border-[#ECEBF4] flex items-center justify-between text-[11px] text-purple-600 font-bold group-hover:underline">
        <span>Rincian Transaksi &rarr;</span>
        <span>{{ $summary['revenue']['pos_count'] + $summary['revenue']['b2b_count'] }} Order</span>
      </div>
    </a>

    <!-- Card 2: Laba Kotor -->
    <div class="bg-white rounded-3xl p-6 border border-[#ECEBF4] shadow-sm">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-textMuted">Estimasi Laba Kotor</span>
        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
      </div>
      <h3 class="text-2xl font-extrabold text-emerald-600 mt-2">
        Rp {{ number_format($summary['profit']['gross_profit'], 0, ',', '.') }}
      </h3>
      <p class="text-[11px] text-emerald-700 font-semibold mt-1">
        Margin Bersih: +{{ $summary['profit']['margin_percent'] }}% dari omzet
      </p>
      <div class="mt-3 pt-3 border-t border-[#ECEBF4] text-[11px] text-textMuted">
        Beban Pokok: Rp {{ number_format($summary['cogs']['total'], 0, ',', '.') }}
      </div>
    </div>

    <!-- Card 3: Valuasi Aset Stok -->
    <a href="{{ route('reports.inventory', request()->query()) }}" class="bg-white rounded-3xl p-6 border border-[#ECEBF4] hover:border-sidebar/40 transition shadow-sm block group">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-textMuted">Nilai Aset Persediaan</span>
        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
      </div>
      <h3 class="text-2xl font-extrabold text-sidebar mt-2">
        Rp {{ number_format($summary['inventory']['total_value'], 0, ',', '.') }}
      </h3>
      <p class="text-[11px] text-textMuted mt-1">
        Gudang {{ $summary['inventory']['warehouse_percent'] }}% • Toko {{ $summary['inventory']['store_percent'] }}%
      </p>
      <div class="mt-3 pt-3 border-t border-[#ECEBF4] flex items-center justify-between text-[11px] text-blue-600 font-bold group-hover:underline">
        <span>Valuasi Per SKU &rarr;</span>
        <span>Real-time On-Hand</span>
      </div>
    </a>

    <!-- Card 4: Piutang B2B -->
    <a href="{{ route('reports.receivables') }}" class="bg-white rounded-3xl p-6 border border-[#ECEBF4] hover:border-sidebar/40 transition shadow-sm block group">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-textMuted">Piutang B2B Outstanding</span>
        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>
      <h3 class="text-2xl font-extrabold text-sidebar mt-2">
        Rp {{ number_format($summary['receivables']['total'], 0, ',', '.') }}
      </h3>
      <p class="text-[11px] mt-1 font-semibold">
        @if($summary['receivables']['overdue_count'] > 0)
          <span class="text-rose-600 font-bold">{{ $summary['receivables']['overdue_count'] }} Overdue (Rp {{ number_format($summary['receivables']['overdue_total'], 0, ',', '.') }})</span>
        @else
          <span class="text-emerald-600">Plafon Aman (0 Overdue)</span>
        @endif
      </p>
      <div class="mt-3 pt-3 border-t border-[#ECEBF4] flex items-center justify-between text-[11px] text-amber-700 font-bold group-hover:underline">
        <span>Penuaan Piutang &rarr;</span>
        <span>Cek Jatuh Tempo</span>
      </div>
    </a>
  </div>

  <!-- Deep Financial Analysis Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Box 1: Revenue vs COGS Analysis -->
    <div class="bg-white rounded-3xl p-6 border border-[#ECEBF4] shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
        <div>
          <h3 class="font-bold text-sm text-sidebar">Realisasi Omzet &amp; Beban Pokok (COGS)</h3>
          <p class="text-[11px] text-textMuted mt-0.5">Hanya mencakup barang yang telah diserahkan fisik</p>
        </div>
        <a href="{{ route('reports.sales', request()->query()) }}" class="text-xs text-sidebar font-bold hover:underline">
          Lihat Semua &rarr;
        </a>
      </div>

      <div class="space-y-3">
        <!-- Retail POS -->
        <div class="p-4 rounded-2xl bg-panelBg/50 border border-[#ECEBF4] flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs">
              POS
            </div>
            <div>
              <h4 class="font-bold text-xs text-sidebar">Penjualan Kasir Ritel</h4>
              <p class="text-[11px] text-textMuted">{{ $summary['revenue']['pos_count'] }} Struk Pembayaran</p>
            </div>
          </div>
          <div class="text-right">
            <p class="font-bold text-sm text-sidebar">Rp {{ number_format($summary['revenue']['pos'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-textMuted">HPP: Rp {{ number_format($summary['cogs']['pos'], 0, ',', '.') }}</p>
          </div>
        </div>

        <!-- Wholesale B2B -->
        <div class="p-4 rounded-2xl bg-panelBg/50 border border-[#ECEBF4] flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-sidebar flex items-center justify-center font-bold text-xs">
              B2B
            </div>
            <div>
              <h4 class="font-bold text-xs text-sidebar">Penjualan Grosir Kemitraan</h4>
              <p class="text-[11px] text-textMuted">{{ $summary['revenue']['b2b_count'] }} Order Handed Over</p>
            </div>
          </div>
          <div class="text-right">
            <p class="font-bold text-sm text-sidebar">Rp {{ number_format($summary['revenue']['b2b'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-textMuted">HPP: Rp {{ number_format($summary['cogs']['b2b'], 0, ',', '.') }}</p>
          </div>
        </div>

        <!-- Profit Ratio Bar -->
        <div class="pt-2">
          <div class="flex items-center justify-between text-xs font-semibold text-textMuted mb-1.5">
            <span>Rasio Margin Kotor:</span>
            <span class="font-bold text-emerald-600">{{ $summary['profit']['margin_percent'] }}%</span>
          </div>
          <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden flex">
            <div class="bg-emerald-500 h-full" style="width: {{ min(100, max(0, $summary['profit']['margin_percent'])) }}%"></div>
            <div class="bg-sidebar/20 h-full" style="width: {{ min(100, max(0, 100 - $summary['profit']['margin_percent'])) }}%"></div>
          </div>
          <div class="flex items-center justify-between text-[10px] text-textMuted mt-1">
            <span>Laba Kotor: Rp {{ number_format($summary['profit']['gross_profit'], 0, ',', '.') }}</span>
            <span>HPP: Rp {{ number_format($summary['cogs']['total'], 0, ',', '.') }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Box 2: Receivables Aging & Abnormal Outflows -->
    <div class="bg-white rounded-3xl p-6 border border-[#ECEBF4] shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
        <div>
          <h3 class="font-bold text-sm text-sidebar">Struktur Penuaan Piutang &amp; Beban Anomali</h3>
          <p class="text-[11px] text-textMuted mt-0.5">Pemantauan risiko kredit dan kebocoran persediaan</p>
        </div>
        <a href="{{ route('reports.receivables') }}" class="text-xs text-sidebar font-bold hover:underline">
          Buku Piutang &rarr;
        </a>
      </div>

      <!-- Aging Buckets -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center">
        <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-100">
          <span class="text-[10px] font-bold uppercase text-emerald-800">Lancar (Current)</span>
          <p class="font-bold text-xs text-emerald-900 mt-1">Rp {{ number_format($summary['receivables']['aging']['current'], 0, ',', '.') }}</p>
        </div>
        <div class="p-3 rounded-2xl bg-amber-50 border border-amber-100">
          <span class="text-[10px] font-bold uppercase text-amber-800">Overdue 1-7 Hari</span>
          <p class="font-bold text-xs text-amber-900 mt-1">Rp {{ number_format($summary['receivables']['aging']['1_to_7'], 0, ',', '.') }}</p>
        </div>
        <div class="p-3 rounded-2xl bg-orange-50 border border-orange-100">
          <span class="text-[10px] font-bold uppercase text-orange-800">Overdue 8-14 Hari</span>
          <p class="font-bold text-xs text-orange-900 mt-1">Rp {{ number_format($summary['receivables']['aging']['8_to_14'], 0, ',', '.') }}</p>
        </div>
        <div class="p-3 rounded-2xl bg-rose-50 border border-rose-100">
          <span class="text-[10px] font-bold uppercase text-rose-800">Overdue &gt;14 Hari</span>
          <p class="font-bold text-xs text-rose-900 mt-1">Rp {{ number_format($summary['receivables']['aging']['over_14'], 0, ',', '.') }}</p>
        </div>
      </div>

      <!-- Abnormal Outflow Watchlist -->
      <div class="border-t border-[#ECEBF4] pt-3 space-y-2">
        <h4 class="text-xs font-bold text-sidebar uppercase tracking-wider">Pemantauan Pengeluaran Darurat &amp; Selisih</h4>
        
        <div class="flex items-center justify-between text-xs py-1.5 border-b border-gray-100">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            <span class="text-textMain">Susut &amp; Selisih Kurang Opname (Merah)</span>
          </div>
          <span class="font-mono font-bold text-rose-600">
            Rp {{ number_format($summary['abnormal_outflows']['opname_loss'], 0, ',', '.') }}
          </span>
        </div>

        <div class="flex items-center justify-between text-xs py-1.5 border-b border-gray-100">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            <span class="text-textMain">Belanja Darurat Toko (Oranye)</span>
          </div>
          <span class="font-mono font-bold text-amber-700">
            Rp {{ number_format($summary['abnormal_outflows']['emergency_purchases'], 0, ',', '.') }}
          </span>
        </div>

        <div class="flex items-center justify-between text-xs py-1.5">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
            <span class="text-textMain">Pembatalan Struk (Void Kasir)</span>
          </div>
          <span class="font-mono font-bold text-textMuted">
            Rp {{ number_format($summary['abnormal_outflows']['void_amount'], 0, ',', '.') }} ({{ $summary['abnormal_outflows']['void_count'] }} kali)
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Navigation Cards to Drilldowns -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
    <a href="{{ route('reports.sales', request()->query()) }}" class="p-5 bg-white rounded-2xl border border-[#ECEBF4] hover:border-sidebar/40 shadow-sm transition block group">
      <div class="flex items-center justify-between">
        <h4 class="font-bold text-sm text-sidebar group-hover:text-brandOrange">Buku Penjualan &amp; Laba</h4>
        <span class="text-sidebar group-hover:translate-x-1 transition">&rarr;</span>
      </div>
      <p class="text-xs text-textMuted mt-1">Daftar transaksi per struk/order dengan rincian omzet, COGS, dan persentase margin kotor.</p>
    </a>

    <a href="{{ route('reports.inventory', request()->query()) }}" class="p-5 bg-white rounded-2xl border border-[#ECEBF4] hover:border-sidebar/40 shadow-sm transition block group">
      <div class="flex items-center justify-between">
        <h4 class="font-bold text-sm text-sidebar group-hover:text-brandOrange">Laporan Valuasi Stok</h4>
        <span class="text-sidebar group-hover:translate-x-1 transition">&rarr;</span>
      </div>
      <p class="text-xs text-textMuted mt-1">Rincian nilai aset stok fisik per SKU, moving average HPP, dan alokasi gudang vs toko.</p>
    </a>

    <a href="{{ route('reports.receivables') }}" class="p-5 bg-white rounded-2xl border border-[#ECEBF4] hover:border-sidebar/40 shadow-sm transition block group">
      <div class="flex items-center justify-between">
        <h4 class="font-bold text-sm text-sidebar group-hover:text-brandOrange">Penuaan Piutang B2B</h4>
        <span class="text-sidebar group-hover:translate-x-1 transition">&rarr;</span>
      </div>
      <p class="text-xs text-textMuted mt-1">Daftar faktur belum lunas, batas plafon kredit mitra, dan status jatuh tempo (Overdue lock).</p>
    </a>
  </div>
</main>
@endsection
