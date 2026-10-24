<!-- ============================================================
     CONTEXTUAL RIGHT PANEL: KEMITRAAN B2B & PLAFON PIUTANG
     ============================================================ -->
@php
  $currentUser = $user ?? auth()->user();
@endphp
<div class="space-y-6">

  <!-- CARD 1: AKSI CEPAT B2B -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-display font-bold text-base text-textMain tracking-tight">Aksi Kemitraan B2B</h2>
        <p class="text-[11px] text-textMuted mt-0.5">Penjualan Grosir & Plafon Kredit</p>
      </div>
      <span class="w-2.5 h-2.5 rounded-full bg-purple-500 animate-pulse" title="Sistem B2B Aktif"></span>
    </div>

    <div class="space-y-2.5 pt-1">
      <!-- 1. BUAT ORDER GROSIR B2B -->
      @if($currentUser?->hasPermission('b2b.order') || $currentUser?->isSuperAdmin())
        <a href="{{ route('b2b.orders.create') }}" 
           class="w-full p-3.5 bg-sidebar hover:bg-sidebar/95 text-white rounded-2xl transition duration-200 flex items-center justify-between group shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition shrink-0">
              <svg class="w-5 h-5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
              </svg>
            </div>
            <div class="text-left">
              <p class="text-xs font-bold leading-tight">+ Buat Order Grosir</p>
              <p class="text-[10px] text-white/70 mt-0.5">Reservasi Stok & Cek Plafon</p>
            </div>
          </div>
          <span class="px-2 py-0.5 rounded-lg bg-white/15 text-[10px] font-bold text-brandOrange">B2B</span>
        </a>
      @endif

      <!-- 2. SCAN QR TIKET PICK-UP MANDIRI -->
      @if($currentUser?->hasPermission('b2b.pickup_validate') || $currentUser?->isSuperAdmin())
        <a href="{{ route('b2b.pickup.scan') }}" 
           class="w-full p-3 bg-white hover:bg-panelBg text-sidebar font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4] shadow-sm">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
              </svg>
            </div>
            <div class="text-left">
              <p class="text-xs font-bold leading-tight text-textMain">Scan Tiket Pick-up QR</p>
              <p class="text-[10px] text-textMuted mt-0.5 font-normal">Verifikasi Serah Terima Barang</p>
            </div>
          </div>
          <svg class="w-4 h-4 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      @endif

      <!-- 3. KELOLA FAKTUR & PELUNASAN PIUTANG -->
      @if($currentUser?->hasPermission('b2b.invoice') || $currentUser?->isSuperAdmin())
        <a href="{{ route('b2b.invoices.index') }}" 
           class="w-full p-3 bg-white hover:bg-panelBg text-sidebar font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4] shadow-sm">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div class="text-left">
              <p class="text-xs font-bold leading-tight text-textMain">Faktur & Piutang Tempo</p>
              <p class="text-[10px] text-textMuted mt-0.5 font-normal">Riwayat Invoice & Pelunasan</p>
            </div>
          </div>
          <svg class="w-4 h-4 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      @endif
    </div>
  </div>

  <!-- CARD 2: RINGKASAN PLAFON & KREDIT B2B -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-base text-textMain tracking-tight">Plafon & Eksposur</h2>
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider">Mitra B2B</span>
    </div>

    <div class="space-y-3">
      <!-- Total Mitra B2B -->
      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
        <div>
          <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Mitra B2B Aktif</span>
          <div class="flex items-baseline gap-2 mt-0.5">
            <span class="text-2xl font-black text-sidebar font-mono">{{ $stats['total_b2b_members'] ?? 0 }}</span>
            <span class="text-[11px] font-bold text-textMuted">Mitra</span>
          </div>
          <p class="text-[11px] text-textMuted mt-0.5">Bermitra resmi dengan pasar</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
      </div>

      <!-- Total Plafon Kredit Dialokasikan -->
      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
        <div>
          <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Total Plafon Kredit</span>
          <p class="text-xl font-black text-sidebar font-mono mt-0.5">
            Rp {{ number_format($stats['total_credit_limit'] ?? 0, 0, ',', '.') }}
          </p>
          <p class="text-[11px] text-textMuted mt-0.5">Batas tempo kredit seluruh mitra</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
        </div>
      </div>

      <!-- Piutang Berjalan & Overdue -->
      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] space-y-2 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Eksposur Piutang Berjalan</span>
          @if(($stats['overdue_count'] ?? 0) > 0)
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
              {{ $stats['overdue_count'] }} Overdue
            </span>
          @else
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
              0 Overdue
            </span>
          @endif
        </div>
        <p class="text-xl font-black text-brandOrange font-mono">
          Rp {{ number_format($stats['total_outstanding_debt'] ?? 0, 0, ',', '.') }}
        </p>
        <p class="text-[11px] text-textMuted">
          Pengunci Otomatis: <strong class="text-sidebar font-semibold">Aktif 100%</strong> (Server-side Credit Lock).
        </p>
      </div>
    </div>
  </div>

</div>
