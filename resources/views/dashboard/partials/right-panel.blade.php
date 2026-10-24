<!-- ============================================================
     CONTEXTUAL RIGHT PANEL: DASBOR UTAMA & EKSEKUTIF
     Pintasan Cepat: Grid 4-Kolom Kompak / Ikon Tooltip Modern
     ============================================================ -->
<div class="space-y-6">

  <!-- CARD 1: TOOLBAR PINTASAN AKSI GRID 4-KOLOM -->
  <div class="bg-panelBg rounded-[28px] p-5 border border-[#ECEBF4] space-y-3.5 shadow-sm">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-display font-bold text-sm text-textMain tracking-tight">Pintasan Cepat</h2>
        <p class="text-[10px] text-textMuted">Toolbar Akses Langsung Seluruh Modul</p>
      </div>
      <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" title="Sistem Real-Time"></span>
    </div>

    <!-- GRID 4-KOLOM KOMPAK -->
    <div class="grid grid-cols-4 gap-2 pt-0.5">
      @if(auth()->user()->hasPermission('pos.access'))
        <!-- Terminal POS -->
        <a href="{{ route('store.pos.terminal') }}" 
           class="aspect-square flex flex-col items-center justify-center p-2 rounded-2xl bg-white hover:bg-sidebar text-sidebar hover:text-white border border-[#ECEBF4] shadow-xs group transition relative" 
           title="Terminal Kasir POS (F2)">
          <div class="w-8 h-8 rounded-xl bg-purple-50 group-hover:bg-white/10 flex items-center justify-center transition">
            <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </div>
          <span class="text-[10px] font-bold mt-1 text-textMain group-hover:text-white truncate">POS</span>
        </a>
      @endif

      @if(auth()->user()->hasPermission('purchasing.po'))
        <!-- + Buat PO -->
        <a href="{{ route('warehouse.po.create') }}" 
           class="aspect-square flex flex-col items-center justify-center p-2 rounded-2xl bg-white hover:bg-sidebar text-sidebar hover:text-white border border-[#ECEBF4] shadow-xs group transition relative" 
           title="Buat PO Suplayer">
          <div class="w-8 h-8 rounded-xl bg-emerald-50 group-hover:bg-white/10 flex items-center justify-center transition">
            <svg class="w-4 h-4 text-moveGreen" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </div>
          <span class="text-[10px] font-bold mt-1 text-textMain group-hover:text-white truncate">+PO</span>
        </a>
      @endif

      @if(auth()->user()->hasPermission('b2b.pickup'))
        <!-- Scan QR Tiket -->
        <a href="{{ route('b2b.pickup.scan') }}" 
           class="aspect-square flex flex-col items-center justify-center p-2 rounded-2xl bg-white hover:bg-sidebar text-sidebar hover:text-white border border-[#ECEBF4] shadow-xs group transition relative" 
           title="Scan QR Member / Tiket B2B">
          <div class="w-8 h-8 rounded-xl bg-purple-50 group-hover:bg-white/10 flex items-center justify-center transition">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
          </div>
          <span class="text-[10px] font-bold mt-1 text-textMain group-hover:text-white truncate">Scan QR</span>
        </a>
      @endif

      @if(auth()->user()->hasPermission('emergency.input'))
        <!-- Belanja Emergency -->
        <a href="{{ route('store.emergency.create') }}" 
           class="aspect-square flex flex-col items-center justify-center p-2 rounded-2xl bg-white hover:bg-sidebar text-sidebar hover:text-white border border-[#ECEBF4] shadow-xs group transition relative" 
           title="Belanja Emergency (+Foto Nota)">
          <div class="w-8 h-8 rounded-xl bg-amber-50 group-hover:bg-white/10 flex items-center justify-center transition">
            <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          </div>
          <span class="text-[10px] font-bold mt-1 text-textMain group-hover:text-white truncate">Darurat</span>
        </a>
      @endif

      @if(auth()->user()->isOwner() || auth()->user()->hasPermission('reports.export'))
        <!-- Pusat Laporan -->
        <a href="{{ route('reports.index') }}" 
           class="aspect-square flex flex-col items-center justify-center p-2 rounded-2xl bg-white hover:bg-sidebar text-sidebar hover:text-white border border-[#ECEBF4] shadow-xs group transition relative" 
           title="Pusat Laporan Eksekutif">
          <div class="w-8 h-8 rounded-xl bg-indigo-50 group-hover:bg-white/10 flex items-center justify-center transition">
            <svg class="w-4 h-4 text-sidebar group-hover:text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          </div>
          <span class="text-[10px] font-bold mt-1 text-textMain group-hover:text-white truncate">Laporan</span>
        </a>
      @endif

      <!-- Audit Trail -->
      <a href="{{ route('audit.index') }}" 
         class="aspect-square flex flex-col items-center justify-center p-2 rounded-2xl bg-white hover:bg-sidebar text-sidebar hover:text-white border border-[#ECEBF4] shadow-xs group transition relative" 
         title="Audit Trail & Investigasi Anomali">
        <div class="w-8 h-8 rounded-xl bg-rose-50 group-hover:bg-white/10 flex items-center justify-center transition">
          <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <span class="text-[10px] font-bold mt-1 text-textMain group-hover:text-white truncate">Audit</span>
      </a>

      <!-- Master Produk -->
      @if(auth()->user()->hasPermission('inventory.view'))
        <a href="{{ route('master.products.index') }}" 
           class="aspect-square flex flex-col items-center justify-center p-2 rounded-2xl bg-white hover:bg-sidebar text-sidebar hover:text-white border border-[#ECEBF4] shadow-xs group transition relative" 
           title="Katalog Produk & Master">
          <div class="w-8 h-8 rounded-xl bg-blue-50 group-hover:bg-white/10 flex items-center justify-center transition">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
          </div>
          <span class="text-[10px] font-bold mt-1 text-textMain group-hover:text-white truncate">Produk</span>
        </a>
      @endif

      <!-- Sesi Opname -->
      @if(auth()->user()->hasPermission('opname.blind_count') || auth()->user()->hasPermission('opname.reconcile'))
        <a href="{{ route('opname.index') }}" 
           class="aspect-square flex flex-col items-center justify-center p-2 rounded-2xl bg-white hover:bg-sidebar text-sidebar hover:text-white border border-[#ECEBF4] shadow-xs group transition relative" 
           title="Sesi Stok Opname">
          <div class="w-8 h-8 rounded-xl bg-amber-50 group-hover:bg-white/10 flex items-center justify-center transition">
            <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          </div>
          <span class="text-[10px] font-bold mt-1 text-textMain group-hover:text-white truncate">Opname</span>
        </a>
      @endif
    </div>
  </div>

  <!-- CARD 2: STATUS OPERASIONAL & PERINGATAN CEPAT (DIPINDAHKAN DARI HERO BANNER) -->
  <div class="bg-panelBg rounded-[28px] p-5 border border-[#ECEBF4] space-y-3.5 shadow-sm">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-sm text-textMain tracking-tight">Status Operasional</h2>
      <span class="text-[10px] font-bold text-sidebar bg-white px-2 py-0.5 rounded-full border border-[#ECEBF4] uppercase tracking-wider">Terpadu</span>
    </div>

    <div class="flex flex-col gap-2">
      <!-- Gudang Sentral -->
      <div class="bg-white p-3 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2.5">
          <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
          <span class="text-xs font-bold text-textMain">Gudang Sentral</span>
        </div>
        <span class="font-mono font-extrabold text-xs text-blue-700 bg-blue-50 px-2.5 py-1 rounded-xl border border-blue-100">
          {{ $stats['pending_pos_count'] ?? 0 }} PO Pending
        </span>
      </div>

      <!-- Toko Ritel Pasar -->
      <div class="bg-white p-3 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2.5">
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0 animate-pulse"></span>
          <span class="text-xs font-bold text-textMain">Toko Pasar Raya</span>
        </div>
        <span class="font-mono font-extrabold text-xs text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-xl border border-emerald-100">
          {{ $stats['active_cashiers_count'] ?? 1 }} Kasir Aktif
        </span>
      </div>

      <!-- Kemitraan B2B -->
      <div class="bg-white p-3 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2.5">
          <span class="w-2.5 h-2.5 rounded-full bg-purple-500 shrink-0"></span>
          <span class="text-xs font-bold text-textMain">Kemitraan Grosir</span>
        </div>
        <span class="font-mono font-extrabold text-xs text-purple-700 bg-purple-50 px-2.5 py-1 rounded-xl border border-purple-100">
          {{ $stats['ready_tickets_count'] ?? 0 }} Tiket QR
        </span>
      </div>
    </div>
  </div>

  <!-- CARD 3: RINGKASAN FINANSIAL & ASET KONSOLIDASI -->
  @if(isset($financialSummary))
    <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
      <div class="flex items-center justify-between">
        <h2 class="font-display font-bold text-base text-textMain tracking-tight">Status Finansial</h2>
        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Real-Time</span>
      </div>

      <div class="space-y-3 pt-1">
        <!-- Metrik 1: Total Omzet -->
        <div class="p-3.5 bg-white rounded-2xl border border-[#ECEBF4] flex items-center justify-between">
          <div>
            <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Omzet Hari Ini</span>
            <p class="font-display text-base font-extrabold text-sidebar mt-0.5">
              Rp {{ number_format($financialSummary['revenue']['total'] ?? 0, 0, ',', '.') }}
            </p>
          </div>
          <span class="text-[10px] font-bold text-textMuted">Toko & B2B</span>
        </div>

        <!-- Metrik 2: Laba Kotor -->
        <div class="p-3.5 bg-white rounded-2xl border border-[#ECEBF4] flex items-center justify-between">
          <div>
            <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Laba Kotor</span>
            <p class="font-display text-base font-extrabold text-moveGreen mt-0.5">
              Rp {{ number_format($financialSummary['profit']['gross_profit'] ?? 0, 0, ',', '.') }}
            </p>
          </div>
          <span class="text-[10px] font-bold text-moveGreen bg-moveGreenBg px-2 py-0.5 rounded-full">
            +{{ $financialSummary['profit']['margin_percent'] ?? 0 }}%
          </span>
        </div>

        <!-- Metrik 3: Valuasi Persediaan -->
        <div class="p-3.5 bg-white rounded-2xl border border-[#ECEBF4] flex items-center justify-between">
          <div>
            <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Aset Persediaan</span>
            <p class="font-display text-base font-extrabold text-textMain mt-0.5">
              Rp {{ number_format($financialSummary['inventory']['total_valuation'] ?? 0, 0, ',', '.') }}
            </p>
          </div>
          <span class="text-[10px] font-bold text-textMuted">HPP</span>
        </div>
      </div>
    </div>
  @endif

  <!-- CARD 3: TATA KELOLA KONTROL & PENGENDALIAN SISTEM -->
  <div class="bg-sidebar rounded-[28px] p-6 text-white space-y-3 shadow-sm">
    <div class="flex items-center gap-2.5">
      <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
        <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
      </div>
      <div>
        <h3 class="font-display font-bold text-xs">Prinsip Pasar Terpadu</h3>
        <p class="text-[10px] text-white/70">Pengawasan Mutasi & Kunci Harga Dasar</p>
      </div>
    </div>
    <p class="text-[11px] text-white/80 leading-relaxed">
      Sistem memvalidasi mutasi stok real-time, perlindungan batas floor price kasir, dan audit integritas tanpa hambatan otorisasi berbelit.
    </p>
  </div>

</div>
