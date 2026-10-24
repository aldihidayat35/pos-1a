<!-- =======================================================
     PROFIL DASHBOARD: KEPALA GUDANG
     Fokus: Manajemen Rantai Pasok, Penerimaan PO, Distribusi Transfer, Stok Opname & Analitik Throughput
     ======================================================= -->
<div class="space-y-7">

  <!-- 1. HERO BANNER KEPALA GUDANG (BERSIH TANPA TOMBOL) -->
  <div class="relative bg-brandOrange rounded-[26px] p-6 sm:p-8 text-white overflow-hidden shadow-sm">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-xs font-semibold mb-3">
        <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
        <span>Rantai Pasok & Logistik Sentral • Real-Time</span>
      </div>
      <h1 class="font-display text-2xl sm:text-[32px] font-bold tracking-tight leading-tight">
        Halo, {{ $user->name }}!
      </h1>
      <p class="text-white/95 text-xs sm:text-sm mt-1.5 font-medium leading-relaxed">
        Kepala Gudang • Fasilitas Pergudangan: <strong class="font-bold underline">{{ $stats['active_location_name'] }}</strong> ({{ $stats['active_location_code'] }})
      </p>

      <!-- Status Operasional Singkat -->
      <div class="mt-4 flex flex-wrap items-center gap-2">
        <div class="bg-white/20 px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <span>{{ $profileData['pending_pos_count'] }} PO Menunggu Terima</span>
        </div>
        <div class="bg-white/20 px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          <span>{{ $profileData['low_stock_count'] }} SKU Mendekati Batas Kritis</span>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. KARTU METRIK KEPALA GUDANG -->
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-lg text-textMain">Status Operasional Logistik Sentral</h2>
      <span class="text-xs text-textMuted font-medium">Diperbarui Real-Time</span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Card 1: PO Menunggu Terima -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">PO Menunggu Terima</span>
          <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-textMain mt-3">
          {{ $profileData['pending_pos_count'] }} Dokumen
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Barang suplayer siap verifikasi fisik
        </p>
      </div>

      <!-- Card 2: Transfer Keluar (Draft) -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Transfer Keluar (Draft)</span>
          <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-blue-600 mt-3">
          {{ $profileData['draft_transfers_count'] }} Permintaan
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Menunggu picking & dispatch ke toko
        </p>
      </div>

      <!-- Card 3: Stok Menipis -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Stok Menipis (<=10)</span>
          <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-rose-600 mt-3">
          {{ $profileData['low_stock_count'] }} SKU
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Perlu penerbitan PO restock segera
        </p>
      </div>

      <!-- Card 4: Sesi Stok Opname -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Sesi Stok Opname</span>
          <div class="w-7 h-7 rounded-lg bg-amber-50 text-brandOrange flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-textMain mt-3">
          {{ $profileData['active_opname'] ? $profileData['active_opname']->status : 'TIDAK AKTIF' }}
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          {{ $profileData['active_opname'] ? 'Sesi opname fisik sedang berjalan' : 'Semua saldo terverifikasi' }}
        </p>
      </div>
    </div>
  </div>

  <!-- 3. GRAFIK THROUGHPUT LOGISTIK GUDANG (CHART.JS) -->
  <div class="bg-white rounded-[24px] p-6 border border-[#ECEBF4] shadow-xs space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
      <div>
        <h3 class="font-display font-bold text-base text-textMain">Throughput Logistik 7 Hari Terakhir</h3>
        <p class="text-xs text-textMuted mt-0.5">Perbandingan volume barang masuk (PO Suplayer) vs barang keluar (Transfer Toko)</p>
      </div>
      <div class="flex items-center gap-3 text-xs font-semibold">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-moveGreen"></span> Masuk (PO)</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-500"></span> Keluar (Transfer)</span>
      </div>
    </div>

    <div class="relative w-full h-64 sm:h-72">
      <canvas id="warehouseThroughputChart"></canvas>
    </div>
  </div>

  <!-- 4. TABEL PERGERAKAN STOK GUDANG TERKINI -->
  <div class="bg-white rounded-[24px] p-6 border border-[#ECEBF4] shadow-xs space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-bold text-base text-textMain">Pergerakan Stok Gudang Terkini</h3>
        <p class="text-xs text-textMuted mt-0.5">5 aktivitas penerimaan dan distribusi gudang terakhir</p>
      </div>
      <a href="{{ route('warehouse.ledger.index') }}" class="text-xs font-bold text-brandOrange hover:underline">Lihat Buku Stok →</a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted uppercase text-[10px] tracking-wider">
            <th class="pb-3 font-bold">Waktu</th>
            <th class="pb-3 font-bold">Produk</th>
            <th class="pb-3 font-bold">Jenis Mutasi</th>
            <th class="pb-3 font-bold text-right">Kuantitas</th>
            <th class="pb-3 font-bold">Operator</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#F0EFF8]">
          @forelse($profileData['recent_movements'] as $movement)
            <tr class="hover:bg-panelBg/50 transition">
              <td class="py-3 text-textMuted whitespace-nowrap">{{ $movement->occurred_at->format('H:i') }} WIB</td>
              <td class="py-3 font-bold text-textMain whitespace-nowrap">{{ $movement->product?->name ?? 'Produk' }}</td>
              <td class="py-3 whitespace-nowrap">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-panelBg text-sidebar">
                  {{ $movement->movement_type }}
                </span>
              </td>
              <td class="py-3 text-right font-extrabold whitespace-nowrap {{ $movement->delta_qty_base >= 0 ? 'text-moveGreen' : 'text-rose-600' }}">
                {{ $movement->delta_qty_base > 0 ? '+'.(float)$movement->delta_qty_base : (float)$movement->delta_qty_base }}
              </td>
              <td class="py-3 text-textMuted whitespace-nowrap">{{ $movement->operation?->actor?->name ?? 'Operator Gudang' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-6 text-center text-textMuted">Belum ada mutasi tercatat di gudang ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- SCRIPT CHART.JS LOGISTIK GUDANG -->
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('warehouseThroughputChart');
    if (!ctx) return;

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: {!! json_encode($profileData['chart_labels'] ?? []) !!},
        datasets: [
          {
            label: 'Barang Masuk PO (Unit)',
            data: {!! json_encode($profileData['chart_inbound_qty'] ?? []) !!},
            backgroundColor: '#10B981',
            borderRadius: 6
          },
          {
            label: 'Barang Keluar Transfer (Unit)',
            data: {!! json_encode($profileData['chart_outbound_qty'] ?? []) !!},
            backgroundColor: '#3B82F6',
            borderRadius: 6
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function (ctx) {
                return ctx.dataset.label + ': ' + new Intl.NumberFormat('id-ID').format(ctx.raw) + ' Unit';
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              font: { family: 'Poppins', size: 10 },
              color: '#9490AD'
            },
            grid: { color: '#F0EFF8' }
          },
          x: {
            ticks: { font: { family: 'Poppins', size: 10 }, color: '#9490AD' },
            grid: { display: false }
          }
        }
      }
    });
  });
</script>
@endpush
