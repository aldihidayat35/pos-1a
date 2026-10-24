<!-- =======================================================
     PROFIL DASHBOARD: OPERASIONAL TOKO & KASIR (KEPALA TOKO & STAF KASIR)
     Fokus: POS Terminal, Shift Laci Kasir, Belanja Emergency, Transfer Masuk
     ======================================================= -->
<div class="space-y-7">

  <!-- 1. HERO BANNER OPERASIONAL TOKO & KASIR -->
  <div class="relative bg-brandOrange rounded-[26px] p-6 sm:p-8 text-white overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-sm">
    <div class="relative z-10 max-w-xl">
      <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-xs font-semibold mb-3">
        @if(!empty($profileData['active_shift']))
          <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
          <span>Shift Kasir Aktif • Jam Buka: {{ $profileData['active_shift']->opened_at?->format('H:i') ?? '-' }}</span>
        @else
          <span class="w-2 h-2 rounded-full bg-amber-300"></span>
          <span>Sesi Kasir Belum Buka • Buka Shift Sebelum Checkout</span>
        @endif
      </div>
      <h1 class="font-display text-2xl sm:text-[32px] font-bold tracking-tight leading-tight">
        Halo, {{ $user->name }}!
      </h1>
      <p class="text-white/95 text-xs sm:text-sm mt-1.5 font-medium leading-relaxed">
        {{ $stats['user_role_label'] }} • Lokasi Toko: <strong class="font-bold underline">{{ $stats['active_location_name'] }}</strong> ({{ $stats['active_location_code'] }})
      </p>

      <div class="mt-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('store.index') }}" class="bg-white/20 hover:bg-white/30 transition px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span>Operasional Toko</span>
        </a>
        <a href="{{ route('store.transfers.index') }}" class="bg-white/20 hover:bg-white/30 transition px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
          <span>{{ $profileData['inbound_transfers_count'] }} Transfer Dalam Transit</span>
        </a>
      </div>
    </div>

    <!-- Quick Action Touch Targets -->
    <div class="relative z-10 flex flex-col sm:flex-row md:flex-col gap-2.5 w-full md:w-auto shrink-0">
      <a href="{{ route('store.pos.terminal') }}" class="min-h-[48px] w-full px-5 py-3 rounded-2xl bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm active:scale-95 group">
        <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        <span>Buka Terminal Kasir POS</span>
        <span class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded font-mono">F2</span>
      </a>

      <div class="flex gap-2 w-full">
        <a href="{{ route('store.shifts.index') }}" class="min-h-[48px] flex-1 px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-95">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span>Shift Laci</span>
        </a>

        @if($user->hasPermission('emergency.input'))
          <a href="{{ route('store.emergency.create') }}" class="min-h-[48px] flex-1 px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-95">
            <span class="w-2 h-2 rounded-full bg-moveOrange"></span>
            <span>+ Emergency</span>
          </a>
        @endif
      </div>
    </div>
  </div>

  <!-- 2. KARTU METRIK OPERASIONAL TOKO -->
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-lg text-textMain">Ringkasan Operasional & Laci Kasir</h2>
      <span class="text-xs text-textMuted font-medium">Toko: {{ $stats['active_location_name'] }}</span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Card 1: Status Sesi Laci Kasir -->
      <a href="{{ route('store.shifts.index') }}" class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200 block group shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Status Shift Laci</span>
          <div class="w-7 h-7 rounded-lg {{ !empty($profileData['active_shift']) ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <p class="font-display text-lg sm:text-xl font-extrabold {{ !empty($profileData['active_shift']) ? 'text-emerald-700' : 'text-gray-500' }} mt-3">
          {{ !empty($profileData['active_shift']) ? 'Sesi Aktif Terbuka' : 'Laci Tertutup' }}
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Modal: Rp {{ number_format($profileData['active_shift']?->starting_cash ?? 0, 0, ',', '.') }}
        </p>
      </a>

      <!-- Card 2: Penjualan POS Hari Ini -->
      <a href="{{ route('store.pos.history') }}" class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200 block group shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Penjualan Kasir Hari Ini</span>
          <div class="w-7 h-7 rounded-lg bg-movePurpleBg text-movePurple flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
          </div>
        </div>
        <p class="font-display text-lg sm:text-2xl font-extrabold text-sidebar mt-3">
          Rp {{ number_format($profileData['today_sales_total'], 0, ',', '.') }}
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          {{ $profileData['today_sales_count'] }} Struk Transaksi Selesai
        </p>
      </a>

      <!-- Card 3: Belanja Emergency Bulan Ini -->
      <a href="{{ route('store.emergency.index') }}" class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200 block group shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Belanja Emergency Bulan Ini</span>
          <div class="w-7 h-7 rounded-lg bg-moveOrangeBg text-moveOrange flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
          </div>
        </div>
        <p class="font-display text-lg sm:text-2xl font-extrabold text-brandOrange mt-3">
          Rp {{ number_format($profileData['emergency_total_spent'], 0, ',', '.') }}
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          {{ $profileData['emergency_count'] }} Pengeluaran Darurat Toko
        </p>
      </a>

      <!-- Card 4: Transfer Masuk Dalam Transit -->
      <a href="{{ route('store.transfers.index') }}" class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200 block group shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Transfer Masuk (Transit)</span>
          <div class="w-7 h-7 rounded-lg bg-moveBlueBg text-moveBlue flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
          </div>
        </div>
        <p class="font-display text-lg sm:text-2xl font-extrabold text-textMain mt-3">
          {{ $profileData['inbound_transfers_count'] }} Pengiriman
        </p>
        <p class="text-[11px] font-semibold mt-1 {{ $profileData['inbound_transfers_count'] > 0 ? 'text-blue-700' : 'text-textMuted' }}">
          {{ $profileData['inbound_transfers_count'] > 0 ? 'Perlu Diterima Fisik di Toko' : 'Tidak Ada Pengiriman' }}
        </p>
      </a>
    </div>
  </div>

  <!-- 3. TABEL 5 TRANSAKSI PENJUALAN KASIR POS TERAKHIR -->
  <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-4 shadow-xs">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-bold text-base text-textMain">Transaksi Kasir POS Terbaru di Toko Ini</h3>
        <p class="text-xs text-textMuted mt-0.5">Pantauan struk belanja dan status pembayaran kasir ritel</p>
      </div>
      <a href="{{ route('store.pos.history') }}" class="text-xs font-bold text-sidebar hover:underline inline-flex items-center gap-1">
        <span>Semua Riwayat Kasir</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted">
            <th class="py-2.5 px-3 font-bold uppercase text-[10px]">No. Struk</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Waktu</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Kasir</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Metode Bayar</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px] text-right">Total Belanja</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px] text-center">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @forelse($profileData['recent_sales'] as $sale)
            <tr class="hover:bg-panelBg/50 transition">
              <td class="py-3 px-3 font-mono font-bold text-sidebar">
                <a href="{{ route('store.pos.receipt', $sale->id) }}" class="hover:underline">
                  #{{ $sale->sale_number }}
                </a>
              </td>
              <td class="py-3 px-3 text-textMuted">
                {{ $sale->posted_at?->format('d/m/Y H:i') ?? '-' }}
              </td>
              <td class="py-3 px-3 font-medium text-textMain">
                {{ $sale->cashier?->name ?? 'Kasir' }}
              </td>
              <td class="py-3 px-3 font-semibold text-textMain">
                <span class="px-2 py-0.5 rounded-lg bg-gray-100 text-gray-700 text-[10px]">
                  {{ $sale->payment_method ?? 'TUNAI' }}
                </span>
              </td>
              <td class="py-3 px-3 font-mono font-bold text-textMain text-right">
                Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
              </td>
              <td class="py-3 px-3 text-center">
                @if($sale->status === 'COMPLETED')
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Selesai
                  </span>
                @else
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                    Void
                  </span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-6 text-center text-textMuted italic">
                Belum ada transaksi penjualan kasir yang tercatat hari ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
