@extends('layouts.app')

@section('title', 'Valuasi Stok & Nilai Aset Persediaan — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Bar & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('reports.index') }}" class="hover:text-sidebar transition">Laporan Finansial</a>
        <span>/</span>
        <span class="text-sidebar font-bold">Valuasi Stok &amp; Aset</span>
      </div>
      <div class="flex items-center gap-3">
        <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
          Valuasi Stok &amp; Nilai Aset Persediaan
        </h1>
        @if($user->isOwner())
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-brandLavender text-sidebar border border-[#D5D2E8]">
            <span class="w-2 h-2 rounded-full bg-sidebar"></span>
            <span>Owner Read-Only</span>
          </span>
        @endif
      </div>
      <p class="text-xs text-textMuted mt-0.5">
        Nilai aset modal kerja riil berdasarkan metode Moving Average Cost pada seluruh gudang dan toko.
      </p>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-2.5">
      @if($user->hasPermission('reports.export') || $user->isOwner() || $user->isSuperAdmin())
        <a href="{{ route('reports.export.inventory', request()->query()) }}" class="px-4 py-2.5 bg-sidebar hover:bg-slate-800 text-white text-xs font-bold rounded-2xl transition flex items-center gap-2 shadow-sm">
          <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <span>Ekspor Valuasi (CSV)</span>
        </a>
      @endif

      <a href="{{ route('reports.index') }}" class="px-4 py-2.5 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Ringkasan Eksekutif</span>
      </a>
    </div>
  </div>

  @php
    $warehouseVal = $items->where('location_type', 'WAREHOUSE')->sum('total_asset_value');
    $storeVal = $items->where('location_type', 'STORE')->sum('total_asset_value');
  @endphp

  <!-- Summary KPI Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm">
      <div class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Total Nilai Aset Stok</div>
      <div class="text-2xl font-extrabold text-sidebar mt-1">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</div>
      <div class="text-[11px] text-textMuted mt-1">Modal terikat pada persediaan</div>
    </div>
    <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm">
      <div class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Aset Gudang Pusat</div>
      <div class="text-2xl font-extrabold text-blue-600 mt-1">Rp {{ number_format($warehouseVal, 0, ',', '.') }}</div>
      <div class="text-[11px] text-textMuted mt-1">{{ $totalAssetValue > 0 ? round(($warehouseVal / $totalAssetValue) * 100, 1) : 0 }}% dari total nilai aset</div>
    </div>
    <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm">
      <div class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Aset Display Toko</div>
      <div class="text-2xl font-extrabold text-purple-600 mt-1">Rp {{ number_format($storeVal, 0, ',', '.') }}</div>
      <div class="text-[11px] text-textMuted mt-1">{{ $totalAssetValue > 0 ? round(($storeVal / $totalAssetValue) * 100, 1) : 0 }}% dari total nilai aset</div>
    </div>
    <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm">
      <div class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Total SKU Terdata</div>
      <div class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($totalItemsCount, 0, ',', '.') }} SKU</div>
      <div class="text-[11px] text-textMuted mt-1">Posisi stok di seluruh lokasi</div>
    </div>
  </div>

  <!-- Filter & Search Bar -->
  <div class="bg-white rounded-3xl p-5 border border-[#ECEBF4] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
    <form method="GET" action="{{ route('reports.inventory') }}" class="flex flex-wrap items-center gap-3">
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
    </form>

    <div class="flex items-center gap-3 w-full md:w-auto">
      <div class="relative flex-1 md:w-72">
        <input type="text" id="inventorySearchInput" onkeyup="searchInventoryTable()" placeholder="Cari SKU atau nama produk..." class="w-full text-xs rounded-xl border border-[#ECEBF4] pl-9 pr-3 py-2 bg-panelBg focus:outline-none focus:border-sidebar focus:bg-white transition">
        <svg class="w-4 h-4 text-textMuted absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </div>

      <!-- Quick Type Filter Buttons -->
      <div class="inline-flex rounded-xl bg-panelBg p-1 border border-[#ECEBF4] text-xs font-bold">
        <button type="button" onclick="filterLocationType('all')" id="loc-tab-all" class="loctab px-3 py-1 rounded-lg bg-white text-sidebar shadow-sm transition">
          Semua
        </button>
        <button type="button" onclick="filterLocationType('WAREHOUSE')" id="loc-tab-wh" class="loctab px-3 py-1 rounded-lg text-textMuted hover:text-sidebar transition">
          Gudang
        </button>
        <button type="button" onclick="filterLocationType('STORE')" id="loc-tab-store" class="loctab px-3 py-1 rounded-lg text-textMuted hover:text-sidebar transition">
          Toko
        </button>
      </div>
    </div>
  </div>

  <!-- Detailed Inventory Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] shadow-sm overflow-hidden">
    <div class="p-5 border-b border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h3 class="font-bold text-sm text-sidebar">Rincian Saldo &amp; Valuasi Stok</h3>
        <p class="text-xs text-textMuted">Perhitungan nilai aset = Kuantitas Fisik On-Hand &times; Moving Average Unit Cost.</p>
      </div>
      <div class="text-xs font-bold text-sidebar bg-panelBg px-3 py-1.5 rounded-xl border border-[#ECEBF4]">
        Menampilkan: <span id="visibleRowCount">{{ $items->count() }}</span> Baris Stok
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs" id="inventoryTable">
        <thead class="bg-panelBg text-sidebar font-bold border-b border-[#ECEBF4]">
          <tr>
            <th class="py-3 px-4">SKU / Kode</th>
            <th class="py-3 px-4">Nama Produk</th>
            <th class="py-3 px-4">Kategori</th>
            <th class="py-3 px-4">Lokasi Simpan</th>
            <th class="py-3 px-4 text-center">Satuan</th>
            <th class="py-3 px-4 text-right">Fisik On-Hand</th>
            <th class="py-3 px-4 text-right">Reservasi B2B</th>
            <th class="py-3 px-4 text-right">Tersedia</th>
            <th class="py-3 px-4 text-right">HPP Rata-rata (Rp)</th>
            <th class="py-3 px-4 text-right">Nilai Aset (Rp)</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @forelse($items as $row)
            <tr class="inv-row hover:bg-slate-50 transition" data-loctype="{{ $row['location_type'] }}" data-sku="{{ strtolower($row['product_sku']) }}" data-name="{{ strtolower($row['product_name']) }}">
              <td class="py-3 px-4 font-mono font-bold text-sidebar whitespace-nowrap">
                {{ $row['product_sku'] }}
              </td>
              <td class="py-3 px-4 font-semibold text-textMain">
                {{ $row['product_name'] }}
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-panelBg text-sidebar border border-[#ECEBF4]">
                  {{ $row['category_name'] }}
                </span>
              </td>
              <td class="py-3 px-4 whitespace-nowrap">
                <div class="flex items-center gap-1.5">
                  <span class="font-medium text-sidebar">{{ $row['location_name'] }}</span>
                  @if($row['location_type'] === 'WAREHOUSE')
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Gudang</span>
                  @else
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200">Toko</span>
                  @endif
                </div>
              </td>
              <td class="py-3 px-4 text-center font-bold text-textMuted whitespace-nowrap">
                {{ $row['uom'] }}
              </td>
              <td class="py-3 px-4 text-right font-bold text-sidebar whitespace-nowrap">
                {{ number_format($row['on_hand_qty'], 2, ',', '.') }}
              </td>
              <td class="py-3 px-4 text-right text-amber-600 font-semibold whitespace-nowrap">
                {{ number_format($row['reserved_qty'], 2, ',', '.') }}
              </td>
              <td class="py-3 px-4 text-right font-bold text-emerald-700 whitespace-nowrap">
                {{ number_format($row['available_qty'], 2, ',', '.') }}
              </td>
              <td class="py-3 px-4 text-right text-textMuted whitespace-nowrap">
                Rp {{ number_format($row['avg_unit_cost'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-4 text-right font-bold text-sidebar whitespace-nowrap">
                Rp {{ number_format($row['total_asset_value'], 0, ',', '.') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="py-12 text-center text-textMuted">
                <div class="max-w-xs mx-auto">
                  <svg class="w-10 h-10 text-textMuted/40 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                  <p class="font-bold text-xs text-sidebar">Tidak Ada Data Saldo Persediaan</p>
                  <p class="text-[11px] text-textMuted mt-1">Belum ada saldo stok terdata pada lokasi yang dipilih.</p>
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
  let activeLocType = 'all';

  function filterLocationType(type) {
    activeLocType = type;
    document.querySelectorAll('.loctab').forEach(t => {
      t.classList.remove('bg-white', 'text-sidebar', 'shadow-sm');
      t.classList.add('text-textMuted');
    });

    if (type === 'all') {
      document.getElementById('loc-tab-all').classList.add('bg-white', 'text-sidebar', 'shadow-sm');
      document.getElementById('loc-tab-all').classList.remove('text-textMuted');
    } else if (type === 'WAREHOUSE') {
      document.getElementById('loc-tab-wh').classList.add('bg-white', 'text-sidebar', 'shadow-sm');
      document.getElementById('loc-tab-wh').classList.remove('text-textMuted');
    } else if (type === 'STORE') {
      document.getElementById('loc-tab-store').classList.add('bg-white', 'text-sidebar', 'shadow-sm');
      document.getElementById('loc-tab-store').classList.remove('text-textMuted');
    }

    searchInventoryTable();
  }

  function searchInventoryTable() {
    const query = document.getElementById('inventorySearchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.inv-row');
    let visibleCount = 0;

    rows.forEach(r => {
      const matchType = (activeLocType === 'all') || (r.dataset.loctype === activeLocType);
      const matchQuery = !query || r.dataset.sku.includes(query) || r.dataset.name.includes(query);

      if (matchType && matchQuery) {
        r.style.display = '';
        visibleCount++;
      } else {
        r.style.display = 'none';
      }
    });

    const badge = document.getElementById('visibleRowCount');
    if (badge) badge.innerText = visibleCount;
  }
</script>
@endsection
