<!-- =======================================================
     PROFIL DASHBOARD: EKSEKUTIF & PENGAWAS (SUPER ADMIN & OWNER)
     Fokus: Kesehatan Finansial, Valuasi Aset, Grafik Tren Omzet & Laba
     ======================================================= -->
<div class="space-y-7">

  <!-- 1. HERO BANNER EKSEKUTIF (Clean UI with Mobile Tooltip) -->
  <div class="relative bg-brandOrange rounded-[26px] p-6 sm:p-8 text-white overflow-hidden shadow-sm">
    <div class="relative z-10 max-w-2xl">
      <div class="flex items-center gap-2 flex-wrap">
        <h1 class="font-display text-lg sm:text-2xl md:text-[30px] font-bold tracking-tight leading-tight">
          Halo, {{ $user->name }}!
        </h1>
        @if($user->isOwner())
          <span class="bg-white text-sidebar text-[10px] font-extrabold px-2 py-0.5 rounded-full">
            Owner (Read-Only)
          </span>
        @endif

        <!-- Mobile-Only Info Tooltip Trigger (ℹ️) -->
        <div class="relative md:hidden inline-flex items-center">
          <button type="button" 
                  onclick="toggleHeaderTooltip('executiveHeroPopover', event)" 
                  class="w-6 h-6 rounded-full bg-white/20 hover:bg-white text-white hover:text-brandOrange flex items-center justify-center transition p-1 shadow-xs"
                  title="Lihat Keterangan Halaman"
                  aria-label="Info Halaman">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
            </svg>
          </button>

          <!-- Mobile Popover Floating Bubble -->
          <div id="executiveHeroPopover" class="header-popover hidden fixed inset-x-4 top-20 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs text-textMain">
            <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
              <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
                <span>Informasi Pengawasan</span>
              </div>
              <button type="button" onclick="closeHeaderTooltip('executiveHeroPopover')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
            </div>
            <p class="text-[11px] text-textMuted leading-relaxed">
              {{ $stats['user_role_label'] }} • Cakupan Pemantauan: {{ $stats['active_location_name'] }} ({{ $stats['active_location_code'] }})
            </p>
          </div>
        </div>
      </div>

      <!-- Desktop Subtitle (Hidden on Mobile) -->
      <p class="hidden md:block text-white/95 text-[11px] sm:text-xs md:text-sm mt-1.5 font-medium leading-relaxed">
        {{ $stats['user_role_label'] }} @if($user->isOwner()) <span class="bg-white text-sidebar text-[10px] font-extrabold px-2 py-0.5 rounded-full ml-1">Owner (Read-Only)</span> @endif • Cakupan Pemantauan: <strong class="font-bold underline">{{ $stats['active_location_name'] }}</strong> ({{ $stats['active_location_code'] }})
      </p>
    </div>
  </div>

  <!-- 2. KARTU KESEHATAN FINANSIAL & ASET PASAR -->
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <h2 class="font-display font-bold text-sm sm:text-base md:text-lg text-textMain">Kesehatan Finansial & Aset Pasar</h2>
        @if($user->isOwner())
          <span class="text-[10px] font-bold text-sidebar bg-brandLavender px-2 py-0.5 rounded-full border border-[#D5D2E8]">
            Owner (Read-Only)
          </span>
        @endif
      </div>
      <span class="text-[11px] sm:text-xs text-textMuted font-medium">Diperbarui: Hari Ini (Real-Time)</span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Card 1: Omzet Hari Ini -->
      <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-textMuted">Omzet Realisasi Hari Ini</span>
          <div class="w-7 h-7 rounded-lg bg-movePurpleBg text-movePurple flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>
        <p class="font-display text-base sm:text-xl md:text-2xl font-extrabold text-textMain mt-2 sm:mt-3 truncate">
          Rp {{ number_format($financialSummary['revenue']['total'] ?? 0, 0, ',', '.') }}
        </p>
        <p class="text-[10px] sm:text-[11px] text-textMuted mt-1 truncate">
          Toko: Rp {{ number_format($financialSummary['revenue']['pos'] ?? 0, 0, ',', '.') }} • B2B: Rp {{ number_format($financialSummary['revenue']['b2b'] ?? 0, 0, ',', '.') }}
        </p>
      </div>

      <!-- Card 2: Estimasi Laba Kotor -->
      <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-textMuted">Laba Kotor Real-Time</span>
          <div class="w-7 h-7 rounded-lg bg-moveGreenBg text-moveGreen flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          </div>
        </div>
        <p class="font-display text-base sm:text-xl md:text-2xl font-extrabold text-moveGreen mt-2 sm:mt-3 truncate">
          Rp {{ number_format($financialSummary['profit']['gross_profit'] ?? 0, 0, ',', '.') }}
        </p>
        <p class="text-[10px] sm:text-[11px] text-textMuted mt-1 truncate">
          Margin: +{{ $financialSummary['profit']['margin_percent'] ?? 0 }}% dari omzet
        </p>
      </div>

      <!-- Card 3: Nilai Aset Stok -->
      <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-textMuted">Nilai Aset Persediaan</span>
          <div class="w-7 h-7 rounded-lg bg-amber-50 text-brandOrange flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
          </div>
        </div>
        <p class="font-display text-base sm:text-xl md:text-2xl font-extrabold text-textMain mt-2 sm:mt-3 truncate">
          Rp {{ number_format($financialSummary['inventory']['total_valuation'] ?? 0, 0, ',', '.') }}
        </p>
        <p class="text-[10px] sm:text-[11px] text-textMuted mt-1 truncate">
          Total: {{ $financialSummary['inventory']['total_sku'] ?? 0 }} SKU (Valuasi HPP)
        </p>
      </div>

      <!-- Card 4: Piutang B2B -->
      <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-textMuted">Piutang B2B Berjalan</span>
          <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </div>
        </div>
        <p class="font-display text-base sm:text-xl md:text-2xl font-extrabold text-textMain mt-2 sm:mt-3 truncate">
          Rp {{ number_format($financialSummary['receivables']['total_ar'] ?? 0, 0, ',', '.') }}
        </p>
        <p class="text-[10px] sm:text-[11px] text-rose-600 font-semibold mt-1 truncate">
          Overdue: Rp {{ number_format($financialSummary['receivables']['overdue_ar'] ?? 0, 0, ',', '.') }}
        </p>
      </div>
    </div>
  </div>

  <!-- 3. GRAFIK TREN FINANSIAL REAL-TIME (CHART.JS) -->
  <div class="bg-white rounded-[24px] p-6 border border-[#ECEBF4] shadow-xs space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
      <div>
        <div class="flex items-center gap-2 flex-wrap">
          <h3 class="font-display font-bold text-base text-textMain">Tren Omzet & Laba Kotor 7 Hari Terakhir</h3>

          <!-- Mobile-Only Info Tooltip Trigger (ℹ️) -->
          <div class="relative md:hidden inline-flex items-center">
            <button type="button" 
                    onclick="toggleHeaderTooltip('execChartCardPopover', event)" 
                    class="w-5 h-5 rounded-full bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white flex items-center justify-center transition p-0.5 shadow-xs"
                    title="Lihat Keterangan Grafik"
                    aria-label="Info Grafik">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
              </svg>
            </button>

            <div id="execChartCardPopover" class="header-popover hidden fixed inset-x-4 top-24 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs text-textMain">
              <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
                <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                  <svg class="w-3.5 h-3.5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                  </svg>
                  <span>Tren Finansial Terintegrasi</span>
                </div>
                <button type="button" onclick="closeHeaderTooltip('execChartCardPopover')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
              </div>
              <p class="text-[11px] text-textMuted leading-relaxed">
                Analisis performa pendapatan pasar terintegrasi (POS Ritel + Grosir B2B).
              </p>
            </div>
          </div>
        </div>
        <p class="hidden md:block text-xs text-textMuted mt-0.5">Analisis performa pendapatan pasar terintegrasi (POS Ritel + Grosir B2B)</p>
      </div>
      <div class="flex items-center gap-3 text-xs font-semibold">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-brandOrange"></span> Omzet</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-moveGreen"></span> Laba Kotor</span>
      </div>
    </div>

    <div class="relative w-full h-64 sm:h-72">
      <canvas id="executiveFinancialChart"></canvas>
    </div>
  </div>

  <!-- 4. AKTIVITAS MUTASI STOK TERAKHIR -->
  <div class="bg-white rounded-[24px] p-6 border border-[#ECEBF4] shadow-xs space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <div class="flex items-center gap-2 flex-wrap">
          <h3 class="font-display font-bold text-base text-textMain">Aktivitas Transaksi & Mutasi Pasar Terkini</h3>

          <!-- Mobile-Only Info Tooltip Trigger (ℹ️) -->
          <div class="relative md:hidden inline-flex items-center">
            <button type="button" 
                    onclick="toggleHeaderTooltip('execAuditCardPopover', event)" 
                    class="w-5 h-5 rounded-full bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white flex items-center justify-center transition p-0.5 shadow-xs"
                    title="Lihat Keterangan Mutasi"
                    aria-label="Info Mutasi">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
              </svg>
            </button>

            <div id="execAuditCardPopover" class="header-popover hidden fixed inset-x-4 top-24 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs text-textMain">
              <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
                <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                  <svg class="w-3.5 h-3.5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  <span>Aktivitas Transaksi Terkini</span>
                </div>
                <button type="button" onclick="closeHeaderTooltip('execAuditCardPopover')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
              </div>
              <p class="text-[11px] text-textMuted leading-relaxed">
                Catatan live audit seluruh pergerakan persediaan.
              </p>
            </div>
          </div>
        </div>
        <p class="hidden md:block text-xs text-textMuted mt-0.5">Catatan live audit seluruh pergerakan persediaan</p>
      </div>
      <a href="{{ route('audit.index') }}" class="text-xs font-bold text-brandOrange hover:underline inline-flex items-center gap-1">
        <span>Semua Audit</span>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted uppercase text-[10px] tracking-wider">
            <th class="pb-3 font-bold">Waktu</th>
            <th class="pb-3 font-bold">Produk</th>
            <th class="pb-3 font-bold">Tipe Mutasi</th>
            <th class="pb-3 font-bold text-right">Kuantitas</th>
            <th class="pb-3 font-bold">Operator</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#F0EFF8]">
          @forelse($profileData['recent_movements'] ?? [] as $m)
            <tr class="hover:bg-panelBg/50 transition">
              <td class="py-3 text-textMuted whitespace-nowrap">{{ $m->occurred_at->format('H:i') }} WIB</td>
              <td class="py-3 font-bold text-textMain whitespace-nowrap">{{ $m->product?->name ?? 'Produk' }}</td>
              <td class="py-3 whitespace-nowrap">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-panelBg text-sidebar">
                  {{ $m->movement_type }}
                </span>
              </td>
              <td class="py-3 text-right font-extrabold whitespace-nowrap {{ $m->delta_qty_base >= 0 ? 'text-moveGreen' : 'text-rose-600' }}">
                {{ $m->delta_qty_base > 0 ? '+'.(float)$m->delta_qty_base : (float)$m->delta_qty_base }}
              </td>
              <td class="py-3 text-textMuted whitespace-nowrap">{{ $m->operation?->actor?->name ?? 'Sistem' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-6 text-center text-textMuted">Belum ada pergerakan stok tercatat hari ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- SCRIPT INISIALISASI CHART.JS EKSEKUTIF -->
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('executiveFinancialChart');
    if (!ctx) return;

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: {!! json_encode($profileData['chart_labels'] ?? []) !!},
        datasets: [
          {
            label: 'Omzet (Rp)',
            data: {!! json_encode($profileData['chart_revenue'] ?? []) !!},
            borderColor: '#F29F3D',
            backgroundColor: 'rgba(242, 159, 61, 0.1)',
            fill: true,
            tension: 0.35,
            borderWidth: 3,
            pointBackgroundColor: '#F29F3D',
            pointRadius: 4
          },
          {
            label: 'Laba Kotor (Rp)',
            data: {!! json_encode($profileData['chart_profit'] ?? []) !!},
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.05)',
            fill: true,
            tension: 0.35,
            borderWidth: 2,
            pointBackgroundColor: '#10B981',
            pointRadius: 4
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
                return ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
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
