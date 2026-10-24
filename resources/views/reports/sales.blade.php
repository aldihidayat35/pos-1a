@extends('layouts.app')

@section('title', 'Buku Penjualan & Margin Laba Kotor — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Bar & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('reports.index') }}" class="hover:text-sidebar transition">Laporan Finansial</a>
        <span>/</span>
        <span class="text-sidebar font-bold">Buku Penjualan &amp; Margin</span>
      </div>
      <div class="flex items-center gap-3">
        <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
          Buku Penjualan &amp; Margin Laba Kotor
        </h1>
        @if($user->isOwner())
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brandLavender text-sidebar border border-[#D5D2E8]">
            <span class="w-2 h-2 rounded-full bg-sidebar"></span>
            <span>Owner Read-Only</span>
          </span>
        @endif
      </div>
      <p class="text-xs text-textMuted mt-0.5">
        Audit granular seluruh transaksi terealisasi (Ritel Kasir POS dan Pesanan Grosir B2B Serah Terima).
      </p>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-2.5">
      @if($user->hasPermission('reports.export') || $user->isOwner() || $user->isSuperAdmin())
        <a href="{{ route('reports.export.sales', request()->query()) }}" class="px-4 py-2.5 bg-sidebar hover:bg-slate-800 text-white text-xs font-bold rounded-2xl transition flex items-center gap-2 shadow-sm">
          <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <span>Ekspor Penjualan (CSV)</span>
        </a>
      @endif

      <a href="{{ route('reports.index') }}" class="px-4 py-2.5 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Ringkasan Eksekutif</span>
      </a>
    </div>
  </div>

  <!-- Filter & Date Range Bar -->
  <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm">
    <form method="GET" action="{{ route('reports.sales') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
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

  @php
    $totalRev = $sales->sum('revenue');
    $totalCogs = $sales->sum('cogs');
    $totalProfit = $sales->sum('gross_profit');
    $overallMargin = $totalRev > 0 ? round(($totalProfit / $totalRev) * 100, 1) : 0.0;
  @endphp

  <!-- Summary KPI Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm">
      <div class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Total Omzet Terealisasi</div>
      <div class="text-2xl font-extrabold text-sidebar mt-1">Rp {{ number_format($totalRev, 0, ',', '.') }}</div>
      <div class="text-[11px] text-textMuted mt-1">{{ $sales->count() }} transaksi terealisasi</div>
    </div>
    <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm">
      <div class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Total Beban Pokok (HPP)</div>
      <div class="text-2xl font-extrabold text-textMain mt-1">Rp {{ number_format($totalCogs, 0, ',', '.') }}</div>
      <div class="text-[11px] text-textMuted mt-1">Moving average inventory cost</div>
    </div>
    <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm">
      <div class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Laba Kotor Terealisasi</div>
      <div class="text-2xl font-extrabold text-emerald-600 mt-1">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
      <div class="text-[11px] text-emerald-700 font-semibold mt-1">Surplus kotor operasional</div>
    </div>
    <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm">
      <div class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Rata-rata Gross Margin</div>
      <div class="text-2xl font-extrabold text-brandPurple mt-1">+{{ $overallMargin }}%</div>
      <div class="text-[11px] text-textMuted mt-1">Margin laba kotor konsolidasi</div>
    </div>
  </div>

  <!-- Channel Filter Tabs -->
  <div class="flex items-center gap-2 border-b border-[#ECEBF4] pb-2 text-xs font-bold">
    <button type="button" onclick="filterSalesChannel('all')" id="tab-btn-all" class="channel-tab-btn px-4 py-2 rounded-xl bg-sidebar text-white shadow-sm transition">
      Semua Transaksi ({{ $sales->count() }})
    </button>
    <button type="button" onclick="filterSalesChannel('POS')" id="tab-btn-pos" class="channel-tab-btn px-4 py-2 rounded-xl bg-panelBg text-textMuted hover:text-sidebar transition">
      Ritel POS Toko ({{ $sales->where('type', 'POS')->count() }})
    </button>
    <button type="button" onclick="filterSalesChannel('B2B')" id="tab-btn-b2b" class="channel-tab-btn px-4 py-2 rounded-xl bg-panelBg text-textMuted hover:text-sidebar transition">
      Grosir B2B ({{ $sales->where('type', 'B2B')->count() }})
    </button>
  </div>

  <!-- Detailed Sales Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] shadow-sm overflow-hidden">
    <div class="p-5 border-b border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h3 class="font-bold text-sm text-sidebar">Daftar Transaksi Terealisasi</h3>
        <p class="text-xs text-textMuted">Setiap baris mencatat nomor struk/order, omzet riil, HPP, serta kontribusi margin kotor.</p>
      </div>
      <div class="text-xs font-bold text-sidebar bg-panelBg px-3 py-1.5 rounded-xl border border-[#ECEBF4]">
        Total: {{ $sales->count() }} Transaksi
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs" id="salesTable">
        <thead class="bg-panelBg text-sidebar font-bold border-b border-[#ECEBF4]">
          <tr>
            <th class="py-3 px-4">Waktu</th>
            <th class="py-3 px-4">No. Transaksi</th>
            <th class="py-3 px-4">Kanal</th>
            <th class="py-3 px-4">Lokasi</th>
            <th class="py-3 px-4">Pelanggan / Mitra</th>
            <th class="py-3 px-4">Pembayaran</th>
            <th class="py-3 px-4 text-right">Omzet (Rp)</th>
            <th class="py-3 px-4 text-right">HPP (Rp)</th>
            <th class="py-3 px-4 text-right">Laba Kotor (Rp)</th>
            <th class="py-3 px-4 text-right">Margin (%)</th>
            <th class="py-3 px-4">Petugas</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @forelse($sales as $row)
            <tr class="sales-row hover:bg-slate-50 transition" data-channel="{{ $row['type'] }}">
              <td class="py-3 px-4 text-textMuted whitespace-nowrap">
                {{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y H:i') }}
              </td>
              <td class="py-3 px-4 font-mono font-bold text-sidebar whitespace-nowrap">
                {{ $row['reference_number'] }}
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                @if($row['type'] === 'POS')
                  <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                    Ritel POS
                  </span>
                @else
                  <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    Grosir B2B
                  </span>
                @endif
              </td>
              <td class="py-3 px-4 text-sidebar font-semibold whitespace-nowrap">
                {{ $row['location_name'] }}
              </td>
              <td class="py-3 px-4 text-textMain font-medium whitespace-nowrap">
                {{ $row['customer'] }}
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-panelBg text-sidebar border border-[#ECEBF4]">
                  {{ $row['payment_method'] }}
                </span>
              </td>
              <td class="py-3 px-4 text-right font-bold text-sidebar whitespace-nowrap">
                Rp {{ number_format($row['revenue'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-4 text-right text-textMuted whitespace-nowrap">
                Rp {{ number_format($row['cogs'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-4 text-right font-bold text-emerald-600 whitespace-nowrap">
                Rp {{ number_format($row['gross_profit'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-4 text-right whitespace-nowrap">
                <span class="font-bold {{ $row['margin_percent'] >= 10 ? 'text-emerald-700' : 'text-amber-600' }}">
                  +{{ $row['margin_percent'] }}%
                </span>
              </td>
              <td class="py-3 px-4 text-textMuted whitespace-nowrap">
                {{ $row['actor'] }}
              </td>
            </tr>
          @empty
            <tr id="emptySalesRow">
              <td colspan="11" class="py-12 text-center text-textMuted">
                <div class="max-w-xs mx-auto">
                  <svg class="w-10 h-10 text-textMuted/40 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  <p class="font-bold text-xs text-sidebar">Tidak Ada Transaksi Penjualan</p>
                  <p class="text-[11px] text-textMuted mt-1">Tidak ditemukan transaksi kasir POS atau pesanan B2B pada rentang tanggal dan lokasi yang dipilih.</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</main>

<script>
  function filterSalesChannel(channel) {
    const rows = document.querySelectorAll('.sales-row');
    const tabs = document.querySelectorAll('.channel-tab-btn');
    
    tabs.forEach(tab => {
      tab.classList.remove('bg-sidebar', 'text-white', 'shadow-sm');
      tab.classList.add('bg-panelBg', 'text-textMuted');
    });

    if (channel === 'all') {
      document.getElementById('tab-btn-all').classList.add('bg-sidebar', 'text-white', 'shadow-sm');
      document.getElementById('tab-btn-all').classList.remove('bg-panelBg', 'text-textMuted');
      rows.forEach(r => r.style.display = '');
    } else if (channel === 'POS') {
      document.getElementById('tab-btn-pos').classList.add('bg-sidebar', 'text-white', 'shadow-sm');
      document.getElementById('tab-btn-pos').classList.remove('bg-panelBg', 'text-textMuted');
      rows.forEach(r => {
        r.style.display = r.dataset.channel === 'POS' ? '' : 'none';
      });
    } else if (channel === 'B2B') {
      document.getElementById('tab-btn-b2b').classList.add('bg-sidebar', 'text-white', 'shadow-sm');
      document.getElementById('tab-btn-b2b').classList.remove('bg-panelBg', 'text-textMuted');
      rows.forEach(r => {
        r.style.display = r.dataset.channel === 'B2B' ? '' : 'none';
      });
    }
  }
</script>
@endsection
