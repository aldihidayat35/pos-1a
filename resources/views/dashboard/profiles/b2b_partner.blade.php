<!-- =======================================================
     PROFIL DASHBOARD: MITRA GROSIR B2B (CUSTOMER PORTAL)
     Fokus: Portal Mandiri Mitra, Tiket QR Siap Ambil, Sisa Plafon Kredit & Riwayat Pembelian
     ======================================================= -->
<div class="space-y-7">

  <!-- 1. HERO BANNER MITRA GROSIR (BERSIH) -->
  <div class="relative bg-brandOrange rounded-[26px] p-6 sm:p-8 text-white overflow-hidden shadow-sm">
    <div class="relative z-10 max-w-2xl">
      <h1 class="font-display text-2xl sm:text-[32px] font-bold tracking-tight leading-tight">
        Selamat Datang, {{ $profileData['member']?->name ?? $user->name }}!
      </h1>
      <p class="text-white/95 text-xs sm:text-sm mt-1.5 font-medium leading-relaxed">
        Kode Mitra: <strong class="font-bold underline">{{ $profileData['member']?->member_code ?? 'B2B-PARTNER' }}</strong> • Status: Terverifikasi Aktif
      </p>
    </div>
  </div>

  <!-- 2. KARTU INFORMASI KREDIT & BELANJA GROSIR -->
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-lg text-textMain">Status Plafon Kredit & Belanja Anda</h2>
      <span class="text-xs text-textMuted font-medium">Bulan: {{ now()->translatedFormat('F Y') }}</span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Card 1: Sisa Plafon Kredit -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Sisa Plafon Kredit</span>
          <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-emerald-600 mt-3">
          Rp {{ number_format($profileData['remaining_credit'], 0, ',', '.') }}
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Total Limit: Rp {{ number_format($profileData['credit_limit'], 0, ',', '.') }}
        </p>
      </div>

      <!-- Card 2: Tiket QR Siap Ambil -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Tiket QR Siap Ambil</span>
          <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-sidebar mt-3">
          {{ $profileData['active_tickets']->count() }} Tiket
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Barang siap diambil di lokasi
        </p>
      </div>

      <!-- Card 3: Tagihan Invoice Berjalan -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Tagihan Tempo Berjalan</span>
          <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-rose-600 mt-3">
          Rp {{ number_format($profileData['used_credit'], 0, ',', '.') }}
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Faktur belum jatuh tempo
        </p>
      </div>

      <!-- Card 4: Total Belanja Bulan Ini -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Total Pembelian Bulan Ini</span>
          <div class="w-7 h-7 rounded-lg bg-amber-50 text-brandOrange flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-textMain mt-3">
          Rp {{ number_format($profileData['monthly_spend'], 0, ',', '.') }}
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Volume transaksi grosir Anda
        </p>
      </div>
    </div>
  </div>

  <!-- 3. WIDGET TIKET QR PENGAMBILAN AKTIF -->
  @if($profileData['active_tickets']->count() > 0)
    <div class="bg-gradient-to-r from-purple-50 via-panelBg to-amber-50 rounded-[24px] p-6 border border-[#E6E4F0] shadow-xs space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-display font-bold text-base text-textMain">Tiket QR Siap Ambil (Pickup)</h3>
          <p class="text-xs text-textMuted mt-0.5">Tunjukkan kode QR / token ini kepada petugas gudang saat pengambilan barang</p>
        </div>
        <span class="text-xs font-bold text-purple-700 bg-purple-100 px-3 py-1 rounded-full animate-pulse">Siap Ambil</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($profileData['active_tickets'] as $ticket)
          <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] shadow-sm flex items-center justify-between gap-4">
            <div>
              <span class="text-[10px] font-bold uppercase tracking-wider text-textMuted">No. Tiket</span>
              <p class="font-mono font-bold text-sm text-sidebar">{{ $ticket->ticket_number }}</p>
              <p class="text-xs font-semibold text-textMain mt-1">Pesanan: {{ $ticket->order?->order_number ?? '-' }}</p>
              <p class="text-[11px] text-emerald-600 font-bold mt-0.5">Status: Siap di Serah Terima</p>
            </div>
            <div class="w-16 h-16 bg-panelBg rounded-xl border border-[#E6E4F0] flex items-center justify-center p-1.5 shrink-0">
              <svg class="w-full h-full text-sidebar" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
              </svg>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <!-- 4. TABEL RIWAYAT PEMBELIAN GROSIR PRIBADI -->
  <div class="bg-white rounded-[24px] p-6 border border-[#ECEBF4] shadow-xs space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-bold text-base text-textMain">Riwayat Pembelian Grosir Terkini</h3>
        <p class="text-xs text-textMuted mt-0.5">5 pesanan grosir terakhir akun perusahaan Anda</p>
      </div>
      <a href="{{ route('b2b.orders.index') }}" class="text-xs font-bold text-brandOrange hover:underline">Semua Riwayat Belanja →</a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted uppercase text-[10px] tracking-wider">
            <th class="pb-3 font-bold">No. Pesanan</th>
            <th class="pb-3 font-bold">Waktu</th>
            <th class="pb-3 font-bold text-center">Status Barang</th>
            <th class="pb-3 font-bold text-right">Nilai Belanja</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#F0EFF8]">
          @forelse($profileData['recent_orders'] as $order)
            <tr class="hover:bg-panelBg/50 transition">
              <td class="py-3 font-mono font-bold text-sidebar whitespace-nowrap">{{ $order->order_number }}</td>
              <td class="py-3 text-textMuted whitespace-nowrap">{{ $order->created_at->format('d M Y, H:i') }} WIB</td>
              <td class="py-3 text-center whitespace-nowrap">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $order->status === 'HANDED_OVER' ? 'bg-emerald-50 text-emerald-600' : 'bg-purple-50 text-purple-600' }}">
                  {{ $order->status }}
                </span>
              </td>
              <td class="py-3 text-right font-extrabold text-sidebar whitespace-nowrap">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="py-6 text-center text-textMuted">Belum ada rekaman transaksi pembelian grosir.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
