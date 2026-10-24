<!-- =======================================================
     PROFIL DASHBOARD: B2B & KEMITRAAN GROSIR (SALES & REKANAN MITRA)
     Fokus: Pesanan Grosir, Plafon Kredit, Tiket Pick-up QR, Overdue
     ======================================================= -->
<div class="space-y-7">

  <!-- 1. HERO BANNER B2B & KEMITRAAN -->
  <div class="relative bg-brandOrange rounded-[26px] p-6 sm:p-8 text-white overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-sm">
    <div class="relative z-10 max-w-xl">
      <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-xs font-semibold mb-3">
        <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
        <span>Portal Grosir &amp; Kemitraan B2B • Real-Time</span>
      </div>
      <h1 class="font-display text-2xl sm:text-[32px] font-bold tracking-tight leading-tight">
        Halo, {{ $user->name }}!
      </h1>
      <p class="text-white/95 text-xs sm:text-sm mt-1.5 font-medium leading-relaxed">
        {{ $stats['user_role_label'] }} • Penjualan Grosir, Alokasi Stok Otomatis, &amp; Tiket Pick-up QR
      </p>

      <div class="mt-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('b2b.orders.index') }}" class="bg-white/20 hover:bg-white/30 transition px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
          <span>{{ $profileData['active_orders_count'] }} Pesanan Aktif</span>
        </a>
        <a href="{{ route('b2b.index') }}" class="bg-white/20 hover:bg-white/30 transition px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          <span>{{ $profileData['total_partners_count'] }} Mitra Terdaftar</span>
        </a>
      </div>
    </div>

    <!-- Quick Action Touch Targets -->
    <div class="relative z-10 flex flex-col sm:flex-row md:flex-col gap-2.5 w-full md:w-auto shrink-0">
      <a href="{{ route('b2b.orders.create') }}" class="min-h-[48px] w-full px-5 py-3 rounded-2xl bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm active:scale-95">
        <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        <span>Buat Order Grosir Baru</span>
      </a>

      <div class="flex gap-2 w-full">
        <button onclick="simulateScanQR()" class="min-h-[48px] flex-1 px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-95">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
          <span>Scan Tiket QR</span>
        </button>

        <a href="{{ route('b2b.index') }}" class="min-h-[48px] flex-1 px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-95">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
          <span>Plafon Kredit</span>
        </a>
      </div>
    </div>
  </div>

  <!-- 2. KARTU METRIK OPERASIONAL B2B -->
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-lg text-textMain">Ringkasan Grosir & Kemitraan</h2>
      <span class="text-xs text-textMuted font-medium">B2B Channel Monitoring</span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Card 1: Pesanan Grosir Aktif (Reserved) -->
      <a href="{{ route('b2b.orders.index') }}" class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200 block group shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Pesanan Grosir Aktif</span>
          <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
          </div>
        </div>
        <p class="font-display text-lg sm:text-2xl font-extrabold text-sidebar mt-3">
          {{ $profileData['active_orders_count'] }} Pesanan
        </p>
        <p class="text-[11px] font-semibold text-purple-700 mt-1">
          Stok Fisik Telah Direservasi
        </p>
      </a>

      <!-- Card 2: Tiket Pick-up Siap Ambil -->
      <a href="{{ route('b2b.orders.index') }}" class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200 block group shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Tiket QR Siap Ambil</span>
          <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
          </div>
        </div>
        <p class="font-display text-lg sm:text-2xl font-extrabold text-emerald-700 mt-3">
          {{ $profileData['pending_tickets_count'] }} Tiket
        </p>
        <p class="text-[11px] font-semibold mt-1 {{ $profileData['pending_tickets_count'] > 0 ? 'text-emerald-700' : 'text-textMuted' }}">
          {{ $profileData['pending_tickets_count'] > 0 ? 'Menunggu Penyerahan Fisik' : 'Semua Tiket Telah Ditebus' }}
        </p>
      </a>

      <!-- Card 3: Total Rekanan B2B -->
      <a href="{{ route('b2b.index') }}" class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200 block group shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Mitra Grosir Terdaftar</span>
          <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </div>
        </div>
        <p class="font-display text-lg sm:text-2xl font-extrabold text-blue-800 mt-3">
          {{ $profileData['total_partners_count'] }} Rekanan
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Tier Bertingkat &amp; Plafon Kredit
        </p>
      </a>

      <!-- Card 4: Peringatan Tagihan Overdue -->
      <a href="{{ route('reports.receivables') }}" class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200 block group shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Status Overdue Piutang</span>
          <div class="w-7 h-7 rounded-lg {{ $profileData['overdue_invoices_count'] > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          </div>
        </div>
        <p class="font-display text-lg sm:text-2xl font-extrabold {{ $profileData['overdue_invoices_count'] > 0 ? 'text-rose-700' : 'text-emerald-700' }} mt-3">
          {{ $profileData['overdue_invoices_count'] }} Faktur
        </p>
        <p class="text-[11px] font-semibold mt-1 {{ $profileData['overdue_invoices_count'] > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
          {{ $profileData['overdue_invoices_count'] > 0 ? 'Plafon Tempo Diblokir Server' : 'Semua Pembayaran Lancar' }}
        </p>
      </a>
    </div>
  </div>

  <!-- 3. TABEL 5 PESANAN GROSIR B2B TERAKHIR -->
  <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-4 shadow-xs">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-bold text-base text-textMain">Pesanan Grosir B2B Terkini</h3>
        <p class="text-xs text-textMuted mt-0.5">Pantauan reservasi stok, tiket pick-up QR, dan ketentuan pembayaran</p>
      </div>
      <a href="{{ route('b2b.orders.index') }}" class="text-xs font-bold text-sidebar hover:underline inline-flex items-center gap-1">
        <span>Semua Pesanan Grosir</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted">
            <th class="py-2.5 px-3 font-bold uppercase text-[10px]">No. Order</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Waktu</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Mitra Grosir</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Ketentuan Bayar</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px] text-right">Nilai Pesanan</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px] text-center">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @forelse($profileData['recent_orders'] as $order)
            <tr class="hover:bg-panelBg/50 transition">
              <td class="py-3 px-3 font-mono font-bold text-sidebar">
                <a href="{{ route('b2b.orders.show', $order->id) }}" class="hover:underline">
                  #{{ $order->order_number }}
                </a>
              </td>
              <td class="py-3 px-3 text-textMuted">
                {{ $order->created_at?->format('d/m/Y H:i') ?? '-' }}
              </td>
              <td class="py-3 px-3 font-medium text-textMain">
                {{ $order->member?->name ?? 'Mitra' }}
                <span class="text-[11px] text-textMuted font-normal font-mono block">({{ $order->member?->member_code ?? '-' }})</span>
              </td>
              <td class="py-3 px-3 font-semibold text-textMain">
                <span class="px-2 py-0.5 rounded-lg bg-gray-100 text-gray-700 text-[10px]">
                  {{ $order->payment_term }}
                </span>
              </td>
              <td class="py-3 px-3 font-mono font-bold text-textMain text-right">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
              </td>
              <td class="py-3 px-3 text-center">
                @php
                  $badgeCls = match($order->status) {
                    'RESERVED' => 'bg-purple-50 text-purple-700 border-purple-200',
                    'READY_FOR_PICKUP' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'HANDED_OVER' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'CANCELLED' => 'bg-gray-100 text-gray-500 border-gray-200',
                    default => 'bg-amber-50 text-amber-700 border-amber-200',
                  };
                @endphp
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeCls }}">
                  {{ $order->status }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-6 text-center text-textMuted italic">
                Belum ada pesanan grosir B2B yang tercatat.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
