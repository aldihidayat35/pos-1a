<!-- ==========================================================
     FIXED HEADER (Clean borderless header, does not move when scrolling)
     Contains: Contextual Sub-Menus (Mobile Dropdown & Desktop Pills) + Right Profile & Language Switcher
     ========================================================== -->
<header class="shrink-0 bg-white px-4 md:px-0 py-2.5 md:py-3 flex items-center justify-between gap-3 sm:gap-4 z-30">
  
  <!-- Left Header: Mobile Hamburger Dropdown + Desktop Dynamic Pill Bar -->
  <div class="flex items-center gap-2">
    
    <!-- MOBILE ONLY: 3-Lines Hamburger Button with Animated Popup Dropdown -->
    <div class="relative md:hidden" id="mobileMenuWrapper">
      <button id="mobileMenuBtn" onclick="toggleMobileSubMenu(event)" class="min-h-[44px] flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-panelBg hover:bg-[#EAE8F2] text-sidebar transition border border-[#E6E4F0]" aria-label="Menu Sub-Modul">
        <!-- 3-Lines Hamburger Icon -->
        <svg id="hamburgerIcon" class="w-4 h-4 text-sidebar shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <span id="mobileActiveSubMenuLabel" class="text-xs font-bold text-sidebar max-w-[130px] truncate">
          Ringkasan Operasional
        </span>
        <!-- Small chevron indicator -->
        <svg id="mobileMenuChevron" class="w-3.5 h-3.5 text-textMuted transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <!-- Mobile Sub-Menu Popup Dropdown with Animation -->
      <div id="mobileSubMenuDropdown" class="hidden absolute left-0 top-full mt-2 w-64 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-[#E8E6F2] p-2 z-50 transform origin-top-left transition-all duration-200 scale-95 opacity-0">
        <div class="px-2.5 py-1 text-[10px] font-bold tracking-wider text-textMuted uppercase border-b border-[#F0EFF6] mb-1" data-i18n="header_sub_menu_title">
          Sub-Modul Operasional
        </div>
        <div id="mobileSubMenuList" class="flex flex-col gap-1">
          <!-- Populated dynamically by JS -->
        </div>
      </div>
    </div>

    <!-- DESKTOP ONLY: Dynamic Contextual Sub-Menu Pill Bar -->
    <div id="subMenuContainer" class="hidden md:inline-flex flex-wrap items-center gap-1 bg-panelBg p-1 rounded-xl text-[11px] font-semibold text-textMuted">
      <!-- Populated dynamically by JS based on current module -->
    </div>

  </div>

  <!-- Right Header: Language Switcher, Action Shortcuts, Notifications & Profile Avatar -->
  <div class="flex items-center justify-end gap-2 sm:gap-3 shrink-0">
    
    <!-- Instant Language Switcher (ID / EN) -->
    <div class="inline-flex items-center bg-panelBg border border-[#E6E4F0] p-0.5 rounded-xl text-xs font-bold shadow-sm" id="langSwitcherContainer">
      <button id="langBtnID" onclick="setAppLanguage('id')" class="px-2.5 py-1.5 rounded-lg transition duration-200 bg-sidebar text-white shadow-sm" title="Bahasa Indonesia">
        ID
      </button>
      <button id="langBtnEN" onclick="setAppLanguage('en')" class="px-2.5 py-1.5 rounded-lg transition duration-200 text-textMuted hover:text-textMain" title="English">
        EN
      </button>
    </div>

    <!-- Live Color Codes Quick Legend Pill (PRD Movement Color Standard) -->
    <button onclick="switchPage(4)" title="Lihat Standar 5 Indikator Warna Pergerakan" class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-panelBg hover:bg-[#EAE8F2] text-sidebar transition border border-[#E6E4F0] text-xs font-bold">
      <span class="flex items-center gap-1">
        <span class="w-2 h-2 rounded-full bg-moveGreen"></span>
        <span class="w-2 h-2 rounded-full bg-moveBlue"></span>
        <span class="w-2 h-2 rounded-full bg-movePurple"></span>
        <span class="w-2 h-2 rounded-full bg-moveOrange"></span>
        <span class="w-2 h-2 rounded-full bg-moveRed"></span>
      </span>
      <span class="hidden md:inline" data-i18n="header_color_legend">Warna Pergerakan</span>
    </button>

    <!-- Gear Settings Icon -->
    <button onclick="switchPage(3)" title="Pengaturan Sistem & Parameter Kunci" class="w-9 h-9 rounded-xl flex items-center justify-center text-textMain/70 hover:text-sidebar hover:bg-panelBg transition border border-transparent hover:border-[#E6E4F0]">
      <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
        <circle cx="12" cy="12" r="3" />
      </svg>
    </button>

    <!-- Notification Bell with Coral Dot -->
    <button onclick="openNotificationModal()" title="Notifikasi Anomali & Peringatan Sistem" class="relative w-9 h-9 rounded-xl flex items-center justify-center text-textMain/70 hover:text-sidebar hover:bg-panelBg transition border border-transparent hover:border-[#E6E4F0]">
      <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <span class="w-2.5 h-2.5 rounded-full bg-brandCoral absolute top-1.5 right-1.5 ring-2 ring-white"></span>
    </button>

    <!-- User Profile Avatar & Role Tag -->
    <div onclick="openUserProfileInfo()" class="flex items-center gap-2 pl-1 cursor-pointer transition hover:opacity-90">
      <div class="w-9 h-9 rounded-xl overflow-hidden border border-[#E6E4F0] shrink-0">
        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80" alt="Avatar" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/100x100/342A6E/FFFFFF?text=BW'" />
      </div>
      <div class="hidden xl:flex flex-col text-left">
        <span class="text-xs font-bold text-textMain leading-tight">Barbara W.</span>
        <span class="text-[10px] font-semibold text-textMuted" data-i18n="role_owner">Owner & Pengawas</span>
      </div>
    </div>

  </div>
</header>
