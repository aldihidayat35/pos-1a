<!-- Ultra-thin 18px Mobile Bottom Blend Gradient (Softly fades scrolling content into white before bottom bar) -->
<div class="md:hidden fixed bottom-[68px] left-0 right-0 h-[18px] pointer-events-none z-40 bg-gradient-to-t from-white via-white/60 to-transparent"></div>

<!-- ============================================================
     FIXED MOBILE NAVIGATION BAR WITH SEAMLESS LIQUID CURVE
     Mobile & Tablet Priority Touch Target (Min 48x48px)
     ============================================================ -->
<nav id="mobileNav" class="mobile-nav-container md:hidden fixed bottom-0 left-0 right-0 z-50 bg-[#342A6E] h-[68px] flex items-center select-none shadow-[0_-4px_16px_rgba(52,42,110,0.12)]">
  
  @php
    $authUser = auth()->user();
    $isSuperAdmin = $authUser ? $authUser->isSuperAdmin() : false;

    $canWarehouse = $isSuperAdmin || ($authUser && $authUser->hasAnyPermission(['inventory.view', 'purchasing.po', 'purchasing.receive', 'transfer.dispatch']));
    $canStore = $isSuperAdmin || ($authUser && $authUser->hasAnyPermission(['pos.access', 'pos.cash_shift', 'emergency.input', 'transfer.receive']));
    $canB2b = $isSuperAdmin || ($authUser && $authUser->hasAnyPermission(['b2b.order', 'b2b.pickup']));
    $canAudit = $isSuperAdmin || ($authUser && $authUser->hasPermission('dashboard.audit'));
    $canMaster = $isSuperAdmin || ($authUser && $authUser->hasAnyPermission(['inventory.view', 'inventory.manage', 'system.users']));

    $visibleMenus = [
        0 => ['show' => true, 'active' => request()->routeIs('dashboard')],
        1 => ['show' => $canWarehouse, 'active' => (request()->routeIs('warehouse.*') || request()->routeIs('opname.*'))],
        2 => ['show' => $canStore, 'active' => request()->routeIs('store.*')],
        3 => ['show' => $canB2b, 'active' => request()->routeIs('b2b.*')],
        4 => ['show' => $canAudit, 'active' => request()->routeIs('audit.*')],
        5 => ['show' => $canMaster, 'active' => (request()->routeIs('master.*') || request()->routeIs('reports.*'))],
    ];

    $filteredVisible = array_filter($visibleMenus, fn($m) => $m['show']);
    $visibleKeys = array_keys($filteredVisible);
    $visibleCount = max(1, count($visibleKeys));
    
    $activeIdx = 0;
    foreach ($visibleKeys as $vIdx => $key) {
        if ($visibleMenus[$key]['active']) {
            $activeIdx = $vIdx;
            break;
        }
    }
    $mobileTransform = "translateX(calc({$activeIdx} * 100%))";
  @endphp

  <!-- Liquid Curve Sliding Indicator (Seamlessly merges to white canvas) -->
  <div id="mobileNavIndicator" class="mobile-nav-indicator" style="width: calc(100% / {{ $visibleCount }}); transform: {{ $mobileTransform }};">
    <svg viewBox="0 0 80 50" preserveAspectRatio="none" class="w-[calc(100%+28px)] h-[52px] absolute top-0 -left-[14px] fill-white overflow-visible pointer-events-none">
      <path d="
        M 0,0 
        C 8,0 12,6 14,16
        C 16,36 24,50 40,50
        C 56,50 64,36 66,16
        C 68,6 72,0 80,0
        L 80,-4
        L 0,-4
        Z
      " />
    </svg>
  </div>

  <!-- Mobile Navigation Buttons Grid - Large Touch Targets (Min 48x48px) -->
  <div class="grid w-full h-full relative z-10" style="grid-template-columns: repeat({{ $visibleCount }}, minmax(0, 1fr));">
    <!-- Menu 0: Dasbor Utama / Executive Health -->
    <a href="{{ route('dashboard') }}" class="mobile-nav-btn {{ $visibleMenus[0]['active'] ? 'active' : '' }} min-w-[48px] min-h-[48px] py-2 flex items-center justify-center" data-nav-idx="0" title="Dasbor Utama" data-i18n-title="nav_menu_0">
      <svg class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <rect x="3" y="3" width="7" height="7" rx="1.5" />
        <rect x="14" y="3" width="7" height="7" rx="1.5" />
        <rect x="14" y="14" width="7" height="7" rx="1.5" />
        <rect x="3" y="14" width="7" height="7" rx="1.5" />
      </svg>
    </a>

    <!-- Menu 1: Operasional Gudang Pusat -->
    @if($canWarehouse)
    <a href="{{ route('warehouse.index') }}" class="mobile-nav-btn {{ $visibleMenus[1]['active'] ? 'active' : '' }} min-w-[48px] min-h-[48px] py-2 flex items-center justify-center" data-nav-idx="1" title="Operasional Gudang" data-i18n-title="nav_menu_1">
      <svg class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
      </svg>
    </a>
    @endif

    <!-- Menu 2: Operasional Toko & POS -->
    @if($canStore)
    <a href="{{ route('store.index') }}" class="mobile-nav-btn {{ $visibleMenus[2]['active'] ? 'active' : '' }} min-w-[48px] min-h-[48px] py-2 flex items-center justify-center" data-nav-idx="2" title="Operasional Toko" data-i18n-title="nav_menu_2">
      <svg class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
    </a>
    @endif

    <!-- Menu 3: Kemitraan B2B & Sales -->
    @if($canB2b)
    <a href="{{ route('b2b.index') }}" class="mobile-nav-btn {{ $visibleMenus[3]['active'] ? 'active' : '' }} min-w-[48px] min-h-[48px] py-2 flex items-center justify-center" data-nav-idx="3" title="Kemitraan B2B" data-i18n-title="nav_menu_3">
      <svg class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
    </a>
    @endif

    <!-- Menu 4: Feed Pergerakan 5-Warna & Anomali (Khusus Super Admin) -->
    @if($canAudit)
    <a href="{{ route('audit.index') }}" class="mobile-nav-btn relative {{ $visibleMenus[4]['active'] ? 'active' : '' }} min-w-[48px] min-h-[48px] py-2 flex items-center justify-center" data-nav-idx="4" title="Feed Pergerakan" data-i18n-title="nav_menu_4">
      <svg class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
      </svg>
      <span class="w-2.5 h-2.5 rounded-full bg-moveRed absolute top-3.5 right-4 animate-pulse"></span>
    </a>
    @endif

    <!-- Menu 5: Master Data & Pengaturan -->
    @if($canMaster)
    <a href="{{ route('master.products.index') }}" class="mobile-nav-btn {{ $visibleMenus[5]['active'] ? 'active' : '' }} min-w-[48px] min-h-[48px] py-2 flex items-center justify-center" data-nav-idx="5" title="Master Data" data-i18n-title="nav_menu_5">
      <svg class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
      </svg>
    </a>
    @endif
  </div>
</nav>
