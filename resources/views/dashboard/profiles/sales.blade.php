<!-- =======================================================
     PROFIL DASHBOARD: SALES EXECUTIVE
     Fokus: Kinerja Penjualan B2B Personal, Pencapaian Target Omzet, Portofolio Mitra
     ======================================================= -->
<div class="space-y-7">

  <!-- 1. HERO BANNER SALES (BERSIH) -->
  <div class="relative bg-brandOrange rounded-[26px] p-6 sm:p-8 text-white overflow-hidden shadow-sm">
    <div class="relative z-10 max-w-2xl">
      <h1 class="font-display text-lg sm:text-2xl md:text-[30px] font-bold tracking-tight leading-tight">
        Selamat Beraktivitas, {{ $user->name }}!
      </h1>
      <p class="text-white/95 text-[11px] sm:text-xs md:text-sm mt-1.5 font-medium leading-relaxed">
        Sales Executive • Cakupan Pasar: <strong class="font-bold underline">{{ $stats['active_location_name'] }}</strong>
      </p>
    </div>
  </div>

  <!-- 2. KARTU KINERJA PERSONAL SALES -->
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-sm sm:text-base md:text-lg text-textMain">Performa Penjualan Grosir Anda (Bulan Ini)</h2>
      <span class="text-[11px] sm:text-xs text-textMuted font-medium">Bulan: {{ now()->translatedFormat('F Y') }}</span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Card 1: Omzet Penjualan B2B -->
      <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-textMuted">Realisasi Omzet B2B</span>
          <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>
        <p class="font-display text-base sm:text-xl md:text-2xl font-extrabold text-sidebar mt-2 sm:mt-3 truncate">
          Rp {{ number_format($profileData['monthly_sales_omzet'], 0, ',', '.') }}
        </p>
        <p class="text-[10px] sm:text-[11px] text-textMuted mt-1 truncate">
          Target: Rp {{ number_format($profileData['target_sales_omzet'], 0, ',', '.') }}
        </p>
      </div>

      <!-- Card 2: Jumlah Pesanan B2B -->
      <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-textMuted">Pesanan Dibuat</span>
          <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          </div>
        </div>
        <p class="font-display text-base sm:text-xl md:text-2xl font-extrabold text-textMain mt-2 sm:mt-3 truncate">
          {{ $profileData['monthly_orders_count'] }} Order
        </p>
        <p class="text-[10px] sm:text-[11px] text-textMuted mt-1 truncate">
          Faktur penjualan grosir yang Anda buat
        </p>
      </div>

      <!-- Card 3: Mitra Terdaftar -->
      <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-textMuted">Mitra Grosir B2B</span>
          <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </div>
        </div>
        <p class="font-display text-base sm:text-xl md:text-2xl font-extrabold text-textMain mt-2 sm:mt-3 truncate">
          {{ $profileData['total_partners_count'] }} Mitra
        </p>
        <p class="text-[10px] sm:text-[11px] text-textMuted mt-1 truncate">
          Akun kemitraan grosir aktif
        </p>
      </div>

      <!-- Card 4: Progress Target -->
      <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-textMuted">Pencapaian Target</span>
          <div class="w-7 h-7 rounded-lg bg-amber-50 text-brandOrange flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          </div>
        </div>
        <p class="font-display text-base sm:text-xl md:text-2xl font-extrabold text-brandOrange mt-2 sm:mt-3 truncate">
          {{ $profileData['omzet_progress'] }}%
        </p>
        <p class="text-[10px] sm:text-[11px] text-textMuted mt-1 truncate">
          Status: {{ $profileData['omzet_progress'] >= 100 ? 'Target Terlampaui!' : 'Dalam Progres' }}
        </p>
      </div>
    </div>
  </div>

  <!-- 3. INDIKATOR TARGET OMZET PENJUALAN B2B (PROGRESS BAR) -->
  <div class="bg-white rounded-[24px] p-5 sm:p-6 border border-[#ECEBF4] shadow-xs space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-bold text-sm sm:text-base text-textMain">Target Omzet Penjualan B2B Anda</h3>
        <p class="text-[11px] sm:text-xs text-textMuted mt-0.5">Realisasi nominal pesanan grosir bulan berjalan</p>
      </div>
      <span class="text-[10px] sm:text-xs font-bold text-sidebar bg-brandLavender px-2.5 sm:px-3 py-1 rounded-full">
        Rp {{ number_format($profileData['monthly_sales_omzet'], 0, ',', '.') }} / Rp {{ number_format($profileData['target_sales_omzet'], 0, ',', '.') }}
      </span>
    </div>

    <div>
      <div class="w-full h-3 sm:h-3.5 bg-panelBg rounded-full overflow-hidden border border-[#E6E4F0]">
        <div class="h-full bg-gradient-to-r from-brandOrange to-sidebar rounded-full transition-all duration-500" style="width: {{ $profileData['omzet_progress'] }}%"></div>
      </div>
    </div>
  </div>

  <!-- 4. TABEL 5 PESANAN B2B TERAKHIR DIBUAT SALES INI -->
  <div class="bg-white rounded-[24px] p-5 sm:p-6 border border-[#ECEBF4] shadow-xs space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-bold text-sm sm:text-base text-textMain">Pesanan Grosir yang Anda Buat Terkini</h3>
        <p class="text-[11px] sm:text-xs text-textMuted mt-0.5">5 order grosir B2B terbaru yang Anda tangani</p>
      </div>
      <a href="{{ route('b2b.orders.index') }}" class="text-[11px] sm:text-xs font-bold text-brandOrange hover:underline">Semua Pesanan Grosir →</a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-[11px] sm:text-xs">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted uppercase text-[9px] sm:text-[10px] tracking-wider">
            <th class="pb-3 font-bold">No. Order</th>
            <th class="pb-3 font-bold">Mitra</th>
            <th class="pb-3 font-bold">Waktu</th>
            <th class="pb-3 font-bold text-center">Status Order</th>
            <th class="pb-3 font-bold text-right">Nilai Pesanan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#F0EFF8]">
          @forelse($profileData['recent_orders'] as $order)
            <tr class="hover:bg-panelBg/50 transition">
              <td class="py-3 font-mono font-bold text-sidebar whitespace-nowrap">{{ $order->order_number }}</td>
              <td class="py-3 font-semibold text-textMain whitespace-nowrap">{{ $order->member?->name ?? 'Mitra B2B' }}</td>
              <td class="py-3 text-textMuted whitespace-nowrap">{{ $order->created_at->format('d M, H:i') }} WIB</td>
              <td class="py-3 text-center whitespace-nowrap">
                <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-amber-50 text-brandOrange">
                  {{ $order->status }}
                </span>
              </td>
              <td class="py-3 text-right font-extrabold text-sidebar whitespace-nowrap">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-6 text-center text-textMuted">Belum ada pesanan grosir yang Anda buat bulan ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
