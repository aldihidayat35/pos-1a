@extends('layouts.app')

@section('title', 'Penuaan Piutang & Risiko Kredit Mitra B2B — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Bar & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('reports.index') }}" class="hover:text-sidebar transition">Laporan Finansial</a>
        <span>/</span>
        <span class="text-sidebar font-bold">Penuaan Piutang B2B</span>
      </div>
      <div class="flex items-center gap-3">
        <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
          Penuaan Piutang B2B (AR Aging Schedule)
        </h1>
        @if($user->isOwner())
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brandLavender text-sidebar border border-[#D5D2E8]">
            <span class="w-2 h-2 rounded-full bg-sidebar"></span>
            <span>Owner Read-Only</span>
          </span>
        @endif
      </div>
      <p class="text-xs text-textMuted mt-0.5">
        Pemantauan eksposur likuiditas dan jatuh tempo piutang kemitraan grosir B2B berbasis bucket penuaan.
      </p>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-2.5">
      <a href="{{ route('b2b.invoices.index') }}" class="px-4 py-2.5 bg-sidebar hover:bg-slate-800 text-white text-xs font-bold rounded-2xl transition flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>Kelola Faktur B2B</span>
      </a>

      <a href="{{ route('reports.index') }}" class="px-4 py-2.5 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Ringkasan Eksekutif</span>
      </a>
    </div>
  </div>

  @php
    $currentAmount = $receivables->where('aging_bucket', 'CURRENT')->sum('outstanding_amount');
    $bucket1to7 = $receivables->where('aging_bucket', '1_TO_7')->sum('outstanding_amount');
    $bucket8to14 = $receivables->where('aging_bucket', '8_TO_14')->sum('outstanding_amount');
    $bucketOver14 = $receivables->where('aging_bucket', 'OVER_14')->sum('outstanding_amount');
  @endphp

  <!-- 4 Aging Bucket Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Bucket 1: Current -->
    <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Lancar (Belum Jatuh Tempo)</span>
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
      </div>
      <div class="text-2xl font-extrabold text-sidebar mt-1">
        Rp {{ number_format($currentAmount, 0, ',', '.') }}
      </div>
      <div class="text-[11px] text-textMuted mt-1">
        {{ $receivables->where('aging_bucket', 'CURRENT')->count() }} faktur belum jatuh tempo
      </div>
    </div>

    <!-- Bucket 2: 1-7 Hari -->
    <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Terlambat 1 - 7 Hari</span>
        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
      </div>
      <div class="text-2xl font-extrabold text-amber-600 mt-1">
        Rp {{ number_format($bucket1to7, 0, ',', '.') }}
      </div>
      <div class="text-[11px] text-textMuted mt-1">
        {{ $receivables->where('aging_bucket', '1_TO_7')->count() }} faktur masa tenggang awal
      </div>
    </div>

    <!-- Bucket 3: 8-14 Hari -->
    <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Terlambat 8 - 14 Hari</span>
        <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
      </div>
      <div class="text-2xl font-extrabold text-orange-600 mt-1">
        Rp {{ number_format($bucket8to14, 0, ',', '.') }}
      </div>
      <div class="text-[11px] text-textMuted mt-1">
        {{ $receivables->where('aging_bucket', '8_TO_14')->count() }} faktur peringatan kedua
      </div>
    </div>

    <!-- Bucket 4: Over 14 Hari -->
    <div class="bg-white rounded-3xl p-5 border border-red-200 bg-red-50/20 shadow-sm">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-red-600 uppercase tracking-wider">Macet Kritis (&gt; 14 Hari)</span>
        <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
      </div>
      <div class="text-2xl font-extrabold text-red-600 mt-1">
        Rp {{ number_format($bucketOver14, 0, ',', '.') }}
      </div>
      <div class="text-[11px] text-red-700 font-semibold mt-1">
        {{ $receivables->where('aging_bucket', 'OVER_14')->count() }} faktur kunci plafon otomatis
      </div>
    </div>
  </div>

  <!-- Filter & Search Bar -->
  <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
    <!-- Filter Tabs -->
    <div class="flex flex-wrap items-center gap-1.5 text-xs font-bold">
      <button type="button" onclick="filterAgingBucket('all')" id="aging-tab-all" class="aging-tab px-3.5 py-1.5 rounded-xl bg-sidebar text-white shadow-sm transition">
        Semua ({{ $receivables->count() }})
      </button>
      <button type="button" onclick="filterAgingBucket('CURRENT')" id="aging-tab-current" class="aging-tab px-3.5 py-1.5 rounded-xl bg-panelBg text-textMuted hover:text-sidebar transition">
        Lancar ({{ $receivables->where('aging_bucket', 'CURRENT')->count() }})
      </button>
      <button type="button" onclick="filterAgingBucket('1_TO_7')" id="aging-tab-1to7" class="aging-tab px-3.5 py-1.5 rounded-xl bg-panelBg text-textMuted hover:text-sidebar transition">
        1-7 Hari ({{ $receivables->where('aging_bucket', '1_TO_7')->count() }})
      </button>
      <button type="button" onclick="filterAgingBucket('8_TO_14')" id="aging-tab-8to14" class="aging-tab px-3.5 py-1.5 rounded-xl bg-panelBg text-textMuted hover:text-sidebar transition">
        8-14 Hari ({{ $receivables->where('aging_bucket', '8_TO_14')->count() }})
      </button>
      <button type="button" onclick="filterAgingBucket('OVER_14')" id="aging-tab-over14" class="aging-tab px-3.5 py-1.5 rounded-xl bg-panelBg text-red-600 hover:text-red-800 transition">
        &gt;14 Hari Kritis ({{ $receivables->where('aging_bucket', 'OVER_14')->count() }})
      </button>
    </div>

    <!-- Search Input -->
    <div class="relative w-full md:w-72">
      <input type="text" id="receivableSearchInput" onkeyup="searchReceivablesTable()" placeholder="Cari nomor faktur / nama mitra..." class="w-full text-xs rounded-xl border border-[#ECEBF4] pl-9 pr-3 py-2 bg-panelBg focus:outline-none focus:border-sidebar focus:bg-white transition">
      <svg class="w-4 h-4 text-textMuted absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </div>
  </div>

  <!-- Detailed Receivables Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] shadow-sm overflow-hidden">
    <div class="p-5 border-b border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h3 class="font-bold text-sm text-sidebar">Daftar Tagihan Faktur Belum Lunas</h3>
        <p class="text-xs text-textMuted">Rincian faktur penjualan tempo yang masih memiliki saldo terutang (outstanding).</p>
      </div>
      <div class="text-xs font-bold text-sidebar bg-panelBg px-3 py-1.5 rounded-xl border border-[#ECEBF4]">
        Total Tagihan: Rp {{ number_format($totalOutstanding, 0, ',', '.') }}
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs" id="receivablesTable">
        <thead class="bg-panelBg text-sidebar font-bold border-b border-[#ECEBF4]">
          <tr>
            <th class="py-3 px-4">No. Faktur</th>
            <th class="py-3 px-4">No. Order</th>
            <th class="py-3 px-4">Mitra B2B</th>
            <th class="py-3 px-4">Tier / Plafon</th>
            <th class="py-3 px-4">Tgl Jatuh Tempo</th>
            <th class="py-3 px-4 text-right">Nilai Faktur (Rp)</th>
            <th class="py-3 px-4 text-right">Terbayar (Rp)</th>
            <th class="py-3 px-4 text-right">Sisa Piutang (Rp)</th>
            <th class="py-3 px-4 text-center">Status / Umur</th>
            <th class="py-3 px-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @forelse($receivables as $row)
            <tr class="rec-row hover:bg-slate-50 transition" data-bucket="{{ $row['aging_bucket'] }}" data-inv="{{ strtolower($row['invoice_number']) }}" data-order="{{ strtolower($row['order_number']) }}" data-member="{{ strtolower($row['member_name']) }}">
              <td class="py-3 px-4 font-mono font-bold text-sidebar whitespace-nowrap">
                {{ $row['invoice_number'] }}
              </td>
              <td class="py-3 px-4 font-mono text-textMuted whitespace-nowrap">
                {{ $row['order_number'] }}
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                <div class="font-semibold text-textMain">{{ $row['member_name'] }}</div>
                <div class="text-[10px] text-textMuted">{{ $row['member_code'] }} • {{ $row['phone'] }}</div>
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-panelBg text-sidebar border border-[#ECEBF4]">
                  {{ $row['tier'] }}
                </span>
                <div class="text-[10px] text-textMuted mt-0.5">Plafon: Rp {{ number_format($row['credit_limit'], 0, ',', '.') }}</div>
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                <div class="font-medium {{ $row['is_overdue'] ? 'text-red-600 font-bold' : 'text-textMain' }}">
                  {{ \Carbon\Carbon::parse($row['due_date'])->format('d/m/Y') }}
                </div>
                <div class="text-[10px] text-textMuted">Terbit: {{ \Carbon\Carbon::parse($row['invoice_date'])->format('d/m/Y') }}</div>
              </td>
              <td class="py-3 px-4 text-right font-medium text-textMain whitespace-nowrap">
                Rp {{ number_format($row['total_amount'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-4 text-right text-emerald-600 font-medium whitespace-nowrap">
                Rp {{ number_format($row['paid_amount'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-4 text-right font-bold text-sidebar whitespace-nowrap">
                Rp {{ number_format($row['outstanding_amount'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-4 text-center whitespace-nowrap">
                @if($row['aging_bucket'] === 'CURRENT')
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Lancar
                  </span>
                @elseif($row['aging_bucket'] === '1_TO_7')
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    +{{ $row['overdue_days'] }} Hari
                  </span>
                @elseif($row['aging_bucket'] === '8_TO_14')
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
                    +{{ $row['overdue_days'] }} Hari
                  </span>
                @else
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                    Kritis (+{{ $row['overdue_days'] }} Hari)
                  </span>
                @endif
              </td>
              <td class="py-3 px-4 text-center whitespace-nowrap">
                <a href="{{ route('b2b.invoices.index') }}" class="px-2.5 py-1 rounded-lg bg-panelBg hover:bg-sidebar hover:text-white text-sidebar font-bold text-[11px] border border-[#ECEBF4] transition inline-flex items-center gap-1">
                  <span>Lihat Faktur</span>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="py-12 text-center text-textMuted">
                <div class="max-w-xs mx-auto">
                  <svg class="w-10 h-10 text-emerald-500/50 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  <p class="font-bold text-xs text-sidebar">Nihil Piutang Tertunggak</p>
                  <p class="text-[11px] text-textMuted mt-1">Seluruh tagihan tempo B2B lunas atau belum ada faktur tempo terbuka.</p>
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
  let activeBucket = 'all';

  function filterAgingBucket(bucket) {
    activeBucket = bucket;
    document.querySelectorAll('.aging-tab').forEach(t => {
      t.classList.remove('bg-sidebar', 'text-white', 'shadow-sm');
      t.classList.add('bg-panelBg', 'text-textMuted');
    });

    const activeBtnMap = {
      'all': 'aging-tab-all',
      'CURRENT': 'aging-tab-current',
      '1_TO_7': 'aging-tab-1to7',
      '8_TO_14': 'aging-tab-8to14',
      'OVER_14': 'aging-tab-over14'
    };

    const targetId = activeBtnMap[bucket];
    if (targetId && document.getElementById(targetId)) {
      document.getElementById(targetId).classList.add('bg-sidebar', 'text-white', 'shadow-sm');
      document.getElementById(targetId).classList.remove('bg-panelBg', 'text-textMuted');
    }

    searchReceivablesTable();
  }

  function searchReceivablesTable() {
    const query = document.getElementById('receivableSearchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.rec-row');

    rows.forEach(r => {
      const matchBucket = (activeBucket === 'all') || (r.dataset.bucket === activeBucket);
      const matchQuery = !query || r.dataset.inv.includes(query) || r.dataset.order.includes(query) || r.dataset.member.includes(query);

      r.style.display = (matchBucket && matchQuery) ? '' : 'none';
    });
  }
</script>
@endsection
