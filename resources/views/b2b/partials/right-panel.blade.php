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

    <!-- GRID 4-KOLOM KOMPAK AKSI B2B -->
    <div class="grid grid-cols-4 gap-2 pt-0.5">
      <!-- 1. BUAT ORDER GROSIR B2B -->
      @if($currentUser?->hasPermission('b2b.order') || $currentUser?->isSuperAdmin())
        <a href="{{ route('b2b.orders.create') }}" 
           class="aspect-square flex flex-col items-center justify-center p-2 rounded-2xl bg-white hover:bg-sidebar text-sidebar hover:text-white border border-[#ECEBF4] shadow-xs group transition relative" 
           title="Buat Order Grosir B2B">
          <div class="w-8 h-8 rounded-xl bg-purple-50 group-hover:bg-white/10 flex items-center justify-center transition">
            <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
          </div>
          <span class="text-[10px] font-bold mt-1 text-textMain group-hover:text-white truncate">+Order</span>
        </a>
      @endif

      <!-- 2. SCAN QR TIKET PICK-UP MANDIRI -->
      @if($currentUser?->hasPermission('b2b.pickup_validate') || $currentUser?->isSuperAdmin())
        <a href="{{ route('b2b.pickup.scan') }}" 
           class="aspect-square flex flex-col items-center justify-center p-2 rounded-2xl bg-white hover:bg-sidebar text-sidebar hover:text-white border border-[#ECEBF4] shadow-xs group transition relative" 
           title="Scan QR Tiket Pick-up">
          <div class="w-8 h-8 rounded-xl bg-purple-50 group-hover:bg-white/10 flex items-center justify-center transition">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
          </div>
          <span class="text-[10px] font-bold mt-1 text-textMain group-hover:text-white truncate">Scan QR</span>
        </a>
      @endif

      <!-- 3. KELOLA FAKTUR & PELUNASAN PIUTANG -->
      @if($currentUser?->hasPermission('b2b.invoice') || $currentUser?->isSuperAdmin())
        <a href="{{ route('b2b.invoices.index') }}" 
           class="aspect-square flex flex-col items-center justify-center p-2 rounded-2xl bg-white hover:bg-sidebar text-sidebar hover:text-white border border-[#ECEBF4] shadow-xs group transition relative" 
           title="Faktur & Tagihan Tempo">
          <div class="w-8 h-8 rounded-xl bg-emerald-50 group-hover:bg-white/10 flex items-center justify-center transition">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <span class="text-[10px] font-bold mt-1 text-textMain group-hover:text-white truncate">Faktur</span>
        </a>
      @endif

      <!-- 4. DATA MITRA GROSIR -->
      <a href="{{ route('master.members.index') }}" 
         class="aspect-square flex flex-col items-center justify-center p-2 rounded-2xl bg-white hover:bg-sidebar text-sidebar hover:text-white border border-[#ECEBF4] shadow-xs group transition relative" 
         title="Data Mitra & Plafon Kredit">
        <div class="w-8 h-8 rounded-xl bg-blue-50 group-hover:bg-white/10 flex items-center justify-center transition">
          <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
        <span class="text-[10px] font-bold mt-1 text-textMain group-hover:text-white truncate">Mitra</span>
      </a>
    </div>
  </div>

  <!-- CARD 2: STATUS KEMITRAAN & PERINGATAN CEPAT (DIPINDAHKAN DARI HERO BANNER) -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-3.5 shadow-sm">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-base text-textMain tracking-tight">Status Kemitraan</h2>
      <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-100 uppercase tracking-wider">Live B2B</span>
    </div>

    <div class="flex flex-col gap-2">
      <!-- 1. Tiket QR Siap Ambil -->
      <div class="bg-white p-3 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2.5">
          <span class="w-2.5 h-2.5 rounded-full bg-purple-500 shrink-0 animate-pulse"></span>
          <span class="text-xs font-bold text-textMain">Tiket QR Siap Ambil</span>
        </div>
        <span class="font-mono font-extrabold text-xs text-purple-700 bg-purple-50 px-2.5 py-1 rounded-xl border border-purple-100">
          {{ $stats['ready_tickets_count'] ?? (isset($profileData['active_tickets']) ? $profileData['active_tickets']->count() : 0) }} Tiket
        </span>
      </div>

      <!-- 2. Pesanan Grosir Aktif -->
      <div class="bg-white p-3 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2.5">
          <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
          <span class="text-xs font-bold text-textMain">Pesanan Grosir Aktif</span>
        </div>
        <span class="font-mono font-extrabold text-xs text-amber-700 bg-amber-50 px-2.5 py-1 rounded-xl border border-amber-100">
          {{ $stats['active_orders_count'] ?? ($profileData['pending_orders_count'] ?? 0) }} Order
        </span>
      </div>

      <!-- 3. Tagihan Tempo Overdue -->
      <div class="bg-white p-3 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2.5">
          <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>
          <span class="text-xs font-bold text-textMain">Faktur Tempo Overdue</span>
        </div>
        <span class="font-mono font-extrabold text-xs text-rose-700 bg-rose-50 px-2.5 py-1 rounded-xl border border-rose-100">
          {{ $stats['overdue_count'] ?? $stats['overdue_invoices_count'] ?? 0 }} Faktur
        </span>
      </div>
    </div>
  </div>

  <!-- CARD 3: RINGKASAN PLAFON & KREDIT B2B -->
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
