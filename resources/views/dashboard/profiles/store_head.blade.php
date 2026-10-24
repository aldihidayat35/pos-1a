<!-- =======================================================
     PROFIL DASHBOARD: KEPALA TOKO
     Fokus: Performa Penjualan Ritel Toko, Kasir On-Duty, Belanja Emergency, Analitik Grafik
     ======================================================= -->
<div class="space-y-7">

  <!-- 1. HERO BANNER KEPALA TOKO (BERSIH) -->
  <div class="relative bg-brandOrange rounded-[26px] p-6 sm:p-8 text-white overflow-hidden shadow-sm">
    <div class="relative z-10 max-w-2xl">
      <h1 class="font-display text-2xl sm:text-[32px] font-bold tracking-tight leading-tight">
        Halo, {{ $user->name }}!
      </h1>
      <p class="text-white/95 text-xs sm:text-sm mt-1.5 font-medium leading-relaxed">
        Kepala Toko • Unit Kerja: <strong class="font-bold underline">{{ $stats['active_location_name'] }}</strong> ({{ $stats['active_location_code'] }})
      </p>
    </div>
  </div>

  <!-- 2. KARTU METRIK KEPALA TOKO -->
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-lg text-textMain">Performa Ritel Toko Hari Ini</h2>
      <span class="text-xs text-textMuted font-medium">Diperbarui Real-Time</span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Card 1: Omzet Toko -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Penjualan Kasir Hari Ini</span>
          <div class="w-7 h-7 rounded-lg bg-movePurpleBg text-movePurple flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-textMain mt-3">
          Rp {{ number_format($profileData['today_sales_total'], 0, ',', '.') }}
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Total: {{ $profileData['today_sales_count'] }} Struk Terbit Hari Ini
        </p>
      </div>

      <!-- Card 2: Kasir Aktif Buka Shift -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Kasir Aktif (Shift Laci)</span>
          <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-emerald-600 mt-3">
          {{ $profileData['active_cashiers_count'] }} Kasir
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Laci kasir beroperasi melayani ritel
        </p>
      </div>

      <!-- Card 3: Belanja Emergency Bulan Ini -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Belanja Emergency Bulan Ini</span>
          <div class="w-7 h-7 rounded-lg bg-amber-50 text-brandOrange flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-textMain mt-3">
          Rp {{ number_format($profileData['emergency_month_total'], 0, ',', '.') }}
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Barang darurat dibeli langsung di pasar
        </p>
      </div>

      <!-- Card 4: Transfer Masuk (Transit) -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Transfer Masuk (Transit)</span>
          <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-blue-600 mt-3">
          {{ $profileData['inbound_transfers_count'] }} Pengiriman
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Sedang dalam perjalanan dari gudang
        </p>
      </div>
    </div>
  </div>

  <!-- 3. GRAFIK ANALITIK PENJUALAN TOKO 7 HARI TERAKHIR (CHART.JS) -->
  <div class="bg-white rounded-[24px] p-6 border border-[#ECEBF4] shadow-xs space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
      <div>
        <h3 class="font-display font-bold text-base text-textMain">Tren Penjualan Toko 7 Hari Terakhir</h3>
        <p class="text-xs text-textMuted mt-0.5">Statistik volume rupiah transaksi kasir di lokasi {{ $stats['active_location_name'] }}</p>
      </div>
      <span class="text-xs font-bold text-brandOrange bg-amber-50 px-3 py-1 rounded-full">Grafik Batang Harian</span>
    </div>

    <div class="relative w-full h-64 sm:h-72">
      <canvas id="storeSalesChart"></canvas>
    </div>
  </div>

  <!-- 4. TABEL PENJUALAN KASIR TOKO TERAKHIR -->
  <div class="bg-white rounded-[24px] p-6 border border-[#ECEBF4] shadow-xs space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-bold text-base text-textMain">Penjualan Kasir Terkini</h3>
        <p class="text-xs text-textMuted mt-0.5">5 transaksi ritel terbaru di kasir toko</p>
      </div>
      <a href="{{ route('store.pos.history') }}" class="text-xs font-bold text-brandOrange hover:underline">Riwayat Kasir Lengkap →</a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted uppercase text-[10px] tracking-wider">
            <th class="pb-3 font-bold">No. Invoice</th>
            <th class="pb-3 font-bold">Waktu</th>
            <th class="pb-3 font-bold">Kasir</th>
            <th class="pb-3 font-bold">Metode</th>
            <th class="pb-3 font-bold text-right">Total Tagihan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#F0EFF8]">
          @forelse($profileData['recent_sales'] as $sale)
            <tr class="hover:bg-panelBg/50 transition">
              <td class="py-3 font-mono font-bold text-sidebar whitespace-nowrap">{{ $sale->invoice_number }}</td>
              <td class="py-3 text-textMuted whitespace-nowrap">{{ $sale->posted_at?->format('H:i') ?? '-' }} WIB</td>
              <td class="py-3 font-semibold text-textMain whitespace-nowrap">{{ $sale->cashier?->name ?? 'Kasir' }}</td>
              <td class="py-3 whitespace-nowrap">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-panelBg text-sidebar">
                  {{ $sale->payment_method ?? 'TUNAI' }}
                </span>
              </td>
              <td class="py-3 text-right font-extrabold text-sidebar whitespace-nowrap">
                Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-6 text-center text-textMuted">Belum ada transaksi kasir hari ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- SCRIPT CHART.JS PENJUALAN TOKO -->
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('storeSalesChart');
    if (!ctx) return;

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: {!! json_encode($profileData['chart_labels'] ?? []) !!},
        datasets: [{
          label: 'Penjualan Toko (Rp)',
          data: {!! json_encode($profileData['chart_store_sales'] ?? []) !!},
          backgroundColor: '#8B5CF6',
          borderRadius: 8,
          borderSkipped: false,
          hoverBackgroundColor: '#342A6E'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function (ctx) {
                return 'Omzet: Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function (val) {
                if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'K';
                return 'Rp ' + val;
              },
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
