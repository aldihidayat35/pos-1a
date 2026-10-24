<!-- ==========================================================
     FIXED HEADER (Clean borderless header, does not move when scrolling)
     Contains: Contextual Sub-Menus (Mobile Dropdown & Desktop Pills) + Location Scope + Language Switcher & Profile
     ========================================================== -->
<header class="shrink-0 bg-white px-4 md:px-0 py-2.5 md:py-3 flex items-center justify-between gap-3 sm:gap-4 z-30 min-w-0">
  
  <!-- Left Header: Contextual Sub-Menus (Mobile Dropdown & Desktop Dynamic Pill Bar) -->
  <div id="headerSubMenuContainer" class="flex items-center gap-2 min-w-0 flex-1 mr-2">
    @hasSection('header_sub_menu')
      @yield('header_sub_menu')
    @else
      @include('partials.header-sub-nav')
    @endif
  </div>

  <!-- Right Header: Location Scope Selector, Language Switcher, Notifications & Profile Avatar -->
  <div class="flex items-center justify-end gap-2 sm:gap-3 shrink-0">
    
    <!-- Active Location Scope Pill / Dropdown -->
    @if(isset($activeLocation))
      <div class="relative inline-flex items-center">
        @if(isset($userLocations) && $userLocations->count() > 1)
          <form method="POST" action="{{ route('location.switch') }}" id="locationSwitchForm" class="m-0">
            @csrf
            <div class="relative inline-flex items-center bg-panelBg border border-[#E6E4F0] px-2.5 py-1.5 rounded-xl text-xs font-bold text-sidebar shadow-sm hover:border-sidebar transition">
              <span class="w-2 h-2 rounded-full {{ $activeLocation->isWarehouse() ? 'bg-brandOrange' : ($activeLocation->isStore() ? 'bg-movePurple' : 'bg-brandCyan') }} mr-1.5 shrink-0"></span>
              <select name="location_id" onchange="document.getElementById('locationSwitchForm').submit()" class="bg-transparent text-sidebar text-xs font-bold focus:outline-none cursor-pointer pr-4 appearance-none">
                @foreach($userLocations as $loc)
                  <option value="{{ $loc->id }}" {{ $loc->id === $activeLocation->id ? 'selected' : '' }}>
                    {{ $loc->name }} ({{ $loc->code }})
                  </option>
                @endforeach
              </select>
              <svg class="w-3 h-3 text-textMuted pointer-events-none absolute right-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
          </form>
        @else
          <div class="hidden sm:inline-flex items-center gap-1.5 bg-panelBg border border-[#E6E4F0] px-2.5 py-1.5 rounded-xl text-xs font-bold text-sidebar shadow-sm" title="Lokasi Operasional Aktif">
            <span class="w-2 h-2 rounded-full {{ $activeLocation->isWarehouse() ? 'bg-brandOrange' : 'bg-movePurple' }}"></span>
            <span class="max-w-[150px] truncate">{{ $activeLocation->name }}</span>
          </div>
        @endif
      </div>
    @endif

    <!-- Instant Language Switcher (ID / EN) -->
    <div class="inline-flex items-center bg-panelBg border border-[#E6E4F0] p-0.5 rounded-xl text-xs font-bold shadow-sm" id="langSwitcherContainer">
      <button id="langBtnID" onclick="setAppLanguage('id')" class="px-2.5 py-1.5 rounded-lg transition duration-200 bg-sidebar text-white shadow-sm" title="Bahasa Indonesia">
        ID
      </button>
      <button id="langBtnEN" onclick="setAppLanguage('en')" class="px-2.5 py-1.5 rounded-lg transition duration-200 text-textMuted hover:text-textMain" title="English">
        EN
      </button>
    </div>

    <!-- Authenticated User Profile Avatar & Role Tag -->
    @auth
      <div onclick="openUserProfileInfo()" class="flex items-center gap-2 pl-1 cursor-pointer transition hover:opacity-90">
        <div class="w-9 h-9 rounded-xl overflow-hidden border border-[#E6E4F0] shrink-0 bg-sidebar text-white flex items-center justify-center font-bold text-xs">
          {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div class="hidden xl:flex flex-col text-left">
          <span class="text-xs font-bold text-textMain leading-tight truncate max-w-[120px]">{{ auth()->user()->name }}</span>
          <span class="text-[10px] font-semibold text-textMuted">{{ auth()->user()->primaryRole()?->label ?? 'User' }}</span>
        </div>
      </div>

      <!-- Logout Form Button -->
      <form method="POST" action="{{ route('logout') }}" class="inline ml-0.5">
        @csrf
        <button type="submit" title="Keluar dari Sistem (Logout)" class="w-9 h-9 rounded-xl flex items-center justify-center text-textMuted hover:text-brandCoral hover:bg-panelBg transition border border-transparent hover:border-[#E6E4F0]">
          <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </form>
    @else
      <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-xl bg-sidebar text-white text-xs font-bold transition hover:bg-sidebarDark">
        Masuk
      </a>
    @endauth

  </div>
</header>
