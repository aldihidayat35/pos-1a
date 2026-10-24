<!-- =======================================================
     PROFIL DASHBOARD: STAF TOKO / KASIR
     Fokus: Kinerja Personal Kasir, Kehadiran Shift Laci, Pencapaian Target Penjualan
     ======================================================= -->
<div class="space-y-7">

  <!-- 1. HERO BANNER KASIR (Clean UI with Mobile Tooltip) -->
  <div class="relative bg-brandOrange rounded-[26px] p-6 sm:p-8 text-white overflow-hidden shadow-sm">
    <div class="relative z-10 max-w-xl">
      <div class="flex items-center gap-2 flex-wrap">
        <h1 class="font-display text-lg sm:text-2xl md:text-[30px] font-bold tracking-tight leading-tight">
          Semangat Bekerja, {{ $user->name }}!
        </h1>

        <!-- Mobile-Only Info Tooltip Trigger (ℹ️) -->
        <div class="relative md:hidden inline-flex items-center">
          <button type="button" 
                  onclick="toggleHeaderTooltip('storeStaffHeroPopover', event)" 
                  class="w-6 h-6 rounded-full bg-white/20 hover:bg-white text-white hover:text-brandOrange flex items-center justify-center transition p-1 shadow-xs"
                  title="Lihat Keterangan Halaman"
                  aria-label="Info Halaman">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
            </svg>
          </button>

          <!-- Mobile Popover Floating Bubble -->
          <div id="storeStaffHeroPopover" class="header-popover hidden fixed inset-x-4 top-20 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs text-textMain">
            <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
              <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
                <span>Informasi Kasir POS</span>
              </div>
              <button type="button" onclick="closeHeaderTooltip('storeStaffHeroPopover')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
            </div>
            <p class="text-[11px] text-textMuted leading-relaxed">
              Operator Kasir POS • Toko: {{ $stats['active_location_name'] }} ({{ $stats['active_location_code'] }})
            </p>
          </div>
        </div>
      </div>

      <!-- Desktop Subtitle (Hidden on Mobile) -->
      <p class="hidden md:block text-white/95 text-[11px] sm:text-xs md:text-sm mt-1.5 font-medium leading-relaxed">
        Operator Kasir POS • Toko: <strong class="font-bold underline">{{ $stats['active_location_name'] }}</strong> ({{ $stats['active_location_code'] }})
      </p>
    </div>
  </div>

  <!-- 2. KARTU KINERJA PERSONAL KASIR -->
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-sm sm:text-base md:text-lg text-textMain">Kinerja & Kehadiran Kasir (Bulan Ini)</h2>
      <span class="text-[11px] sm:text-xs text-textMuted font-medium">Bulan: {{ now()->translatedFormat('F Y') }}</span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Card 1: Kehadiran Shift Laci -->
      <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-textMuted">Kehadiran (Shift Buka)</span>
          <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
        </div>
        <p class="font-display text-base sm:text-xl md:text-2xl font-extrabold text-emerald-600 mt-2 sm:mt-3 truncate">
          {{ $profileData['shifts_attended_count'] }} Hari
        </p>
        <p class="text-[10px] sm:text-[11px] text-textMuted mt-1 truncate">
          Target: {{ $profileData['target_work_days'] }} Hari Kerja ({{ $profileData['attendance_progress'] }}%)
        </p>
      </div>

      <!-- Card 2: Total Struk Pribadi -->
      <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-textMuted">Total Transaksi Selesai</span>
          <div class="w-7 h-7 rounded-lg bg-movePurpleBg text-movePurple flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          </div>
        </div>
        <p class="font-display text-base sm:text-xl md:text-2xl font-extrabold text-textMain mt-2 sm:mt-3 truncate">
          {{ $profileData['monthly_sales_count'] }} Struk
        </p>
        <p class="text-[10px] sm:text-[11px] text-textMuted mt-1 truncate">
          Pelanggan dilayani oleh Anda
        </p>
      </div>

      <!-- Card 3: Total Nominal Penjualan Pribadi -->
      <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-textMuted">Akumulasi Omzet Kasir</span>
          <div class="w-7 h-7 rounded-lg bg-amber-50 text-brandOrange flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>
        <p class="font-display text-base sm:text-xl md:text-2xl font-extrabold text-sidebar mt-2 sm:mt-3 truncate">
          Rp {{ number_format($profileData['monthly_sales_total'], 0, ',', '.') }}
        </p>
        <p class="text-[10px] sm:text-[11px] text-textMuted mt-1 truncate">
          Progress: {{ $profileData['sales_progress'] }}% dari target
        </p>
      </div>

      <!-- Card 4: Rata-Rata Nilai Struk -->
      <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-textMuted">Rata-Rata Struk (Basket)</span>
          <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          </div>
        </div>
        <p class="font-display text-base sm:text-xl md:text-2xl font-extrabold text-textMain mt-2 sm:mt-3 truncate">
          Rp {{ number_format($profileData['avg_basket_size'], 0, ',', '.') }}
        </p>
        <p class="text-[10px] sm:text-[11px] text-textMuted mt-1 truncate">
          Rata-rata belanja per pelanggan
        </p>
      </div>
    </div>
  </div>

  <!-- 3. INDIKATOR TARGET KINERJA KASIR (PROGRESS BARS) -->
  <div class="bg-white rounded-[24px] p-5 sm:p-6 border border-[#ECEBF4] shadow-xs space-y-4 sm:space-y-5">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-bold text-sm sm:text-base text-textMain">Pencapaian Target Bulanan Kasir</h3>
        <p class="text-[11px] sm:text-xs text-textMuted mt-0.5">Pantau target penjualan dan kedisiplinan shift kerja Anda</p>
      </div>
      <span class="text-[10px] sm:text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 sm:px-3 py-1 rounded-full">
        {{ $profileData['sales_progress'] >= 80 ? 'Kinerja Sangat Baik' : 'Terus Semangat' }}
      </span>
    </div>

    <div class="space-y-4">
      <!-- Target Omzet -->
      <div>
        <div class="flex items-center justify-between text-[11px] sm:text-xs mb-1.5 font-bold">
          <span class="text-textMain">Target Penjualan Ritel Kasir</span>
          <span class="text-sidebar">Rp {{ number_format($profileData['monthly_sales_total'], 0, ',', '.') }} / Rp {{ number_format($profileData['target_sales'], 0, ',', '.') }} ({{ $profileData['sales_progress'] }}%)</span>
        </div>
        <div class="w-full h-3 bg-panelBg rounded-full overflow-hidden border border-[#E6E4F0]">
          <div class="h-full bg-gradient-to-r from-brandOrange to-sidebar rounded-full transition-all duration-500" style="width: {{ $profileData['sales_progress'] }}%"></div>
        </div>
      </div>

      <!-- Target Kehadiran Shift -->
      <div>
        <div class="flex items-center justify-between text-[11px] sm:text-xs mb-1.5 font-bold">
          <span class="text-textMain">Target Kehadiran Kerja (Shift Buka)</span>
          <span class="text-emerald-600">{{ $profileData['shifts_attended_count'] }} / {{ $profileData['target_work_days'] }} Hari ({{ $profileData['attendance_progress'] }}%)</span>
        </div>
        <div class="w-full h-3 bg-panelBg rounded-full overflow-hidden border border-[#E6E4F0]">
          <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: {{ $profileData['attendance_progress'] }}%"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- 4. TABEL 5 TRANSAKSI TERAKHIR YANG DILAYANI OLEH KASIR INI -->
  <div class="bg-white rounded-[24px] p-5 sm:p-6 border border-[#ECEBF4] shadow-xs space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-bold text-sm sm:text-base text-textMain">Transaksi Terakhir yang Anda Layani</h3>
        <p class="text-[11px] sm:text-xs text-textMuted mt-0.5">5 struk penjualan terakhir pada sesi kasir Anda</p>
      </div>
      <a href="{{ route('store.pos.history') }}" class="text-[11px] sm:text-xs font-bold text-brandOrange hover:underline">Riwayat Penjualan →</a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-[11px] sm:text-xs">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted uppercase text-[9px] sm:text-[10px] tracking-wider">
            <th class="pb-3 font-bold">No. Invoice</th>
            <th class="pb-3 font-bold">Waktu</th>
            <th class="pb-3 font-bold">Metode Bayar</th>
            <th class="pb-3 font-bold text-right">Total Transaksi</th>
            <th class="pb-3 font-bold text-center">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#F0EFF8]">
          @forelse($profileData['recent_sales'] as $sale)
            <tr class="hover:bg-panelBg/50 transition">
              <td class="py-3 font-mono font-bold text-sidebar whitespace-nowrap">{{ $sale->invoice_number }}</td>
              <td class="py-3 text-textMuted whitespace-nowrap">{{ $sale->posted_at?->format('d M, H:i') ?? '-' }} WIB</td>
              <td class="py-3 whitespace-nowrap">
                <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-panelBg text-sidebar">
                  {{ $sale->payment_method ?? 'TUNAI' }}
                </span>
              </td>
              <td class="py-3 text-right font-extrabold text-sidebar whitespace-nowrap">
                Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
              </td>
              <td class="py-3 text-center whitespace-nowrap">
                <span class="px-2.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-emerald-50 text-emerald-600">
                  SELESAI
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-6 text-center text-textMuted">Anda belum melayani transaksi penjualan bulan ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
