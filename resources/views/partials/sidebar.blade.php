<!-- ============================================================
     1. FIXED DESKTOP SIDEBAR WITH SEAMLESS LIQUID CURVE
     Sistem Operasional Pasar Terpadu (Gudang, Toko, & B2B)
     ============================================================ -->
<aside class="sidebar-container hidden md:flex md:w-[92px] h-full rounded-[26px] flex-col items-center justify-between py-7 shrink-0">
  
  <!-- Brand Logo: Pasar Modern -->
  <div class="flex flex-col items-center justify-center cursor-pointer group" onclick="switchPage(0)">
    <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition">
      <svg class="w-6 h-6 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
      </svg>
    </div>
    <span class="font-display text-xs tracking-tight text-white/80 mt-1.5 font-bold">Pasar<strong class="font-extrabold text-white">Hub</strong></span>
  </div>

  <!-- Navigation Icons Wrapper (Desktop Vertical) -->
  <nav id="sidebarNav" class="relative flex flex-col items-center justify-center w-full my-auto gap-3 py-2">
    
    <!-- Animated White Curve Pill (Desktop Only) -->
    <div id="navIndicator" class="nav-indicator" style="top: 8px; transform: translateY(0px);"></div>

    <!-- Menu 0: Dasbor Utama & Kesehatan Finansial -->
    <button onclick="switchPage(0)" data-index="0" title="Dasbor Utama & Kesehatan Finansial" class="sidebar-btn active min-h-[48px]" data-i18n-title="nav_menu_0">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <rect x="3" y="3" width="7" height="7" rx="1.5" />
        <rect x="14" y="3" width="7" height="7" rx="1.5" />
        <rect x="14" y="14" width="7" height="7" rx="1.5" />
        <rect x="3" y="14" width="7" height="7" rx="1.5" />
      </svg>
    </button>

    <!-- Menu 1: Operasional Gudang Pusat -->
    <button onclick="switchPage(1)" data-index="1" title="Operasional Gudang Pusat" class="sidebar-btn min-h-[48px]" data-i18n-title="nav_menu_1">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
      </svg>
    </button>

    <!-- Menu 2: Operasional Toko & Kasir POS -->
    <button onclick="switchPage(2)" data-index="2" title="Operasional Toko & Kasir POS" class="sidebar-btn min-h-[48px]" data-i18n-title="nav_menu_2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
    </button>

    <!-- Menu 3: Kemitraan B2B & Sales -->
    <button onclick="switchPage(3)" data-index="3" title="Kemitraan B2B & Plafon Piutang" class="sidebar-btn min-h-[48px]" data-i18n-title="nav_menu_3">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
    </button>

    <!-- Menu 4: Feed Pergerakan 5-Warna & Anomali -->
    <button onclick="switchPage(4)" data-index="4" title="Feed Pergerakan 5-Warna & Anomali" class="sidebar-btn relative min-h-[48px]" data-i18n-title="nav_menu_4">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
      </svg>
      <span class="w-2 h-2 rounded-full bg-moveRed absolute top-3.5 right-5 animate-pulse"></span>
    </button>

    <!-- Menu 5: Master Produk, UOM, & Member QR -->
    <button onclick="switchPage(5)" data-index="5" title="Master Produk, Satuan & Member QR" class="sidebar-btn min-h-[48px]" data-i18n-title="nav_menu_5">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
      </svg>
    </button>
  </nav>

  <!-- Bottom Shift / Exit Button -->
  <button onclick="openToast(i18n[currentLang].toast_shift_saved)" title="Akhiri Sesi / Kunci Terminal" class="text-white/60 hover:text-white transition p-2 min-h-[48px] min-w-[48px] flex items-center justify-center rounded-xl hover:bg-white/10" data-i18n-title="nav_exit_session">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
    </svg>
  </button>
</aside>
