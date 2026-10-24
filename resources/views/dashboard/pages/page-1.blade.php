<!-- =======================================================
     PAGE 1 (INDEX 1): DASHBOARD UTAMA GUDANG (DEFAULT)
     Flat soft colors — No gradients, no neon glow
     ======================================================= -->
<div id="page-1" class="page-view hidden space-y-7">

@if(!$user->hasPermission('purchasing.receive') && !$user->hasPermission('inventory.manage') && !$user->isOwner() && !$user->isSuperAdmin())
    <div class="p-4 rounded-2xl bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E] text-xs font-semibold flex items-center gap-3">
      <svg class="w-5 h-5 text-[#F59E0B] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <div>
        <p class="font-bold">Akses Dibatasi — Khusus Pengelola Gudang Sentral</p>
        <p class="text-[11px] text-[#92400E]/90 mt-0.5">Akun Anda ({{ $user->primaryRole()?->label ?? 'User' }}) hanya memiliki akses pantau dasar. Mutasi dan penerimaan fisik barang gudang dikunci oleh server.</p>
      </div>
    </div>
  @endif

  <!-- 1. SOFT SOLID ORANGE HERO BANNER (No gradient, no glow shadow) -->
  <div id="p1-sec-hero" class="relative bg-brandOrange rounded-[24px] p-7 sm:p-8 text-white overflow-hidden flex items-center justify-between min-h-[155px]">
    <div class="relative z-10 max-w-md">
      <h1 class="font-display text-2xl sm:text-[32px] font-bold tracking-tight leading-tight">
        Halo, {{ $user->name }}!
      </h1>
      <p class="text-white/95 text-sm sm:text-[15px] mt-1.5 font-medium">
        Gudang Pusat Sentral • Operasional Rantai Pasok Terpadu
      </p>
      <div class="mt-4 flex flex-wrap items-center gap-2.5">
        <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
          </svg>
          <span>14.820 SKU Aktif</span>
        </span>
        <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
          </svg>
          <span>18 Armada Siap Muat</span>
        </span>
      </div>
    </div>

    <!-- Flat Soft Isometric Tablet & Boxes Illustration (No gradients, no drop-shadow glow) -->
    <div class="hidden sm:block relative w-56 h-36 pointer-events-none select-none">
      <svg viewBox="0 0 260 180" class="w-full h-full transform rotate-[-6deg] scale-110">
        <!-- Flat sleek warehouse scanner tablet base -->
        <polygon points="90,95 235,35 265,115 120,175" fill="#E2E4EC" />
        <polygon points="95,92 232,35 258,108 122,166" fill="#F8F9FC" />
        <!-- Soft solid coral accent on tablet touchpad area -->
        <polygon points="110,85 180,55 198,102 128,132" fill="#F4B2B7" />
        <!-- Top half hinged screen -->
        <polygon points="55,60 195,5 220,42 85,95" fill="#FFFFFF" stroke="#E5E7EB" stroke-width="2" />
        <!-- Isometric 3D Parcel Box (Flat colors) -->
        <g transform="translate(135, 25)">
          <polygon points="30,0 60,14 30,28 0,14" fill="#FAD689" />
          <polygon points="0,14 30,28 30,60 0,46" fill="#EFA852" />
          <polygon points="30,28 60,14 60,46 30,60" fill="#DE8344" />
          <line x1="15" y1="7" x2="45" y2="21" stroke="#FFF" stroke-width="3" stroke-linecap="round"/>
        </g>
      </svg>
    </div>
  </div>

  <!-- 2. WEEKLY SCHEDULE STRIP -->
  <div id="p1-sec-calendar" class="space-y-4">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-4">
        <h2 id="weekRangeLabel" class="font-display font-bold text-lg text-textMain">
          Oktober 19-25
        </h2>
        <!-- Soft Coral Prev/Next Pill Button (No neon shadow) -->
        <div class="inline-flex items-center bg-brandCoral text-white rounded-full px-2.5 py-1 gap-2">
          <button onclick="shiftWeek(-1)" class="hover:opacity-80 transition" title="Minggu Sebelumnya">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
          </button>
          <button onclick="shiftWeek(1)" class="hover:opacity-80 transition" title="Minggu Berikutnya">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
      </div>

      <!-- Month Pill Button -->
      <button onclick="openToast('Menampilkan Kalender Jadwal Bongkar Muat Bulanan (Oktober 2026)')" class="inline-flex items-center gap-2 bg-panelBg hover:bg-brandLavender text-textMain text-xs font-semibold px-4 py-2 rounded-full transition">
        <svg class="w-3.5 h-3.5 text-sidebar" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
        </svg>
        <span>Bulan</span>
      </button>
    </div>

    <!-- 7-Day Interactive Strip -->
    <div id="calendarGrid" class="grid grid-cols-7 gap-2 sm:gap-4 pt-2 border-t border-gray-100">
      <!-- Populated dynamically by JS -->
    </div>
  </div>

  <!-- 3. WEEKLY REPORTS -> LAPORAN PERGERAKAN STOK -->
  <div id="p1-sec-reports" class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-lg text-textMain">Laporan Pergerakan Stok</h2>
      <div class="flex items-center gap-4 text-xs font-semibold">
        <button onclick="setReportPeriod('today', this)" class="report-period-btn text-textMuted hover:text-textMain transition">Hari Ini</button>
        <button onclick="setReportPeriod('week', this)" class="report-period-btn text-textMain font-bold transition">Minggu</button>
        <button onclick="setReportPeriod('month', this)" class="report-period-btn text-textMuted hover:text-textMain transition">Bulan</button>
      </div>
    </div>

    <!-- 4 Warehouse Metric Cards -->
    <div id="reportCardsContainer" class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <!-- Populated dynamically by JS -->
    </div>
  </div>

  <!-- 4. UPDATING MONITORING -> PEMANTAUAN KAPASITAS ZONA -->
  <div id="p1-sec-capacity" class="space-y-4">
    <h2 class="font-display font-bold text-lg text-textMain">Pemantauan Kapasitas & Rak</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <!-- Left Soft Card (25% Coral Ring, No glow shadow) -->
      <div onclick="cycleRing(0)" class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] flex items-center justify-between cursor-pointer hover:bg-panelBg/50 transition group">
        <div>
          <h3 class="font-bold text-sm text-textMain group-hover:text-brandCoral transition">Zona Cold Storage</h3>
          <p class="text-xs text-textMuted mt-1">Oktober 2026 • Suhu -18°C</p>
          <span class="inline-block mt-2 text-[11px] font-semibold text-brandCoral bg-brandCoral/10 px-2.5 py-0.5 rounded-full">Klik untuk simulasi beban</span>
        </div>
        <div class="relative w-16 h-16 flex items-center justify-center shrink-0">
          <svg class="w-16 h-16">
            <circle class="text-gray-100" stroke-width="5" stroke="currentColor" fill="transparent" r="26" cx="32" cy="32"/>
            <circle id="ringCircle0" class="progress-ring__circle text-brandCoral" stroke-width="5" stroke-dasharray="163.36" stroke-dashoffset="122.52" stroke-linecap="round" stroke="currentColor" fill="transparent" r="26" cx="32" cy="32"/>
          </svg>
          <span id="ringLabel0" class="absolute text-xs font-extrabold text-textMain">25%</span>
        </div>
      </div>

      <!-- Right Deep Indigo Card (50% Orange Ring, Flat color, no glow) -->
      <div onclick="cycleRing(1)" class="bg-sidebar rounded-[26px] p-6 flex items-center justify-between text-white cursor-pointer hover:bg-sidebarDark transition group">
        <div>
          <h3 class="font-bold text-sm text-white group-hover:text-brandOrange transition">Rak Utama (Dry & Pallet)</h3>
          <p class="text-xs text-white/65 mt-1">Oktober 2026 • 4.200 Slot</p>
          <span class="inline-block mt-2 text-[11px] font-semibold text-brandOrange bg-white/10 px-2.5 py-0.5 rounded-full">Klik untuk simulasi beban</span>
        </div>
        <div class="relative w-16 h-16 flex items-center justify-center shrink-0">
          <svg class="w-16 h-16">
            <circle class="text-white/15" stroke-width="5" stroke="currentColor" fill="transparent" r="26" cx="32" cy="32"/>
            <circle id="ringCircle1" class="progress-ring__circle text-brandOrange" stroke-width="5" stroke-dasharray="163.36" stroke-dashoffset="81.68" stroke-linecap="round" stroke="currentColor" fill="transparent" r="26" cx="32" cy="32"/>
          </svg>
          <span id="ringLabel1" class="absolute text-xs font-extrabold text-white">50%</span>
        </div>
      </div>
    </div>
  </div>

</div>
