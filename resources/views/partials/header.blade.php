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

    <!-- Authenticated User Profile Dropdown Component -->
    @auth
      @php
        $authUser = auth()->user();
        $userRoleLabel = $authUser->primaryRole()?->label ?? 'User';
        $userInitials = strtoupper(substr($authUser->name, 0, 2));
      @endphp

      <div class="relative inline-block text-left" id="userProfileDropdownContainer">
        <!-- Trigger Button -->
        <button type="button" 
                id="userProfileTriggerBtn"
                onclick="toggleUserProfileDropdown()" 
                class="flex items-center gap-2.5 p-1.5 sm:px-3 sm:py-1.5 rounded-2xl border border-[#E6E4F0] bg-white hover:bg-panelBg transition shadow-2xs hover:shadow-xs focus:outline-none focus:ring-2 focus:ring-sidebar/20"
                aria-expanded="false"
                aria-haspopup="true">
          
          <!-- Avatar with Online Status Dot -->
          <div class="relative shrink-0">
            <div class="w-9 h-9 rounded-xl overflow-hidden bg-sidebar text-white flex items-center justify-center font-bold text-xs shadow-xs">
              {{ $userInitials }}
            </div>
            <!-- Online Dot -->
            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full" title="Status: Aktif Online"></span>
          </div>

          <!-- User Name & Role Label -->
          <div class="hidden md:flex flex-col text-left pr-0.5">
            <span class="text-xs font-bold text-textMain leading-tight max-w-[150px] lg:max-w-[180px] truncate" title="{{ $authUser->name }}">
              {{ $authUser->name }}
            </span>
            <div class="flex items-center gap-1.5 mt-0.5">
              <span class="text-[10px] font-semibold text-textMuted leading-tight">{{ $userRoleLabel }}</span>
              @if($authUser->isSuperAdmin())
                <span class="px-1 py-0.2 rounded text-[9px] font-bold bg-purple-100 text-purple-700">ROOT</span>
              @endif
            </div>
          </div>

          <!-- Chevron Down Indicator -->
          <svg id="userProfileChevron" class="w-3.5 h-3.5 text-textMuted transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>

        <!-- Dropdown Popover Menu -->
        <div id="userProfileDropdownMenu" 
             class="hidden absolute right-0 mt-2 w-72 sm:w-80 rounded-3xl bg-white border border-[#E6E4F0] shadow-2xl py-2 z-50 transition-all origin-top-right">
          
          <!-- Dropdown Header: Detailed User Identity -->
          <div class="px-5 py-4 bg-gradient-to-br from-[#F8F7FC] to-[#F1EFF9] border-b border-[#ECEBF4] -mt-2 rounded-t-3xl">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-2xl bg-sidebar text-white flex items-center justify-center font-black text-base shadow-xs shrink-0">
                {{ $userInitials }}
              </div>
              <div class="min-w-0 flex-1">
                <h4 class="text-xs sm:text-sm font-bold text-sidebar truncate" title="{{ $authUser->name }}">
                  {{ $authUser->name }}
                </h4>
                <p class="text-[11px] text-textMuted truncate">
                  {{ $authUser->email ?? ('@' . $authUser->username) }}
                </p>
                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-sidebar/10 text-sidebar border border-sidebar/20">
                    🛡️ {{ $userRoleLabel }}
                  </span>
                  @if(isset($activeLocation))
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      📍 {{ $activeLocation->name }}
                    </span>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <!-- Dropdown Actions List -->
          <div class="p-2 space-y-1 text-xs">
            
            <!-- Aksi 1: Ubah Kata Sandi -->
            <button type="button" 
                    onclick="openChangePasswordModal()"
                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-textMain hover:bg-panelBg hover:text-sidebar transition text-left group">
              <div class="w-8 h-8 rounded-xl bg-indigo-50 text-sidebar flex items-center justify-center group-hover:bg-sidebar group-hover:text-white transition shrink-0 shadow-2xs">
                <svg class="w-4 h-4 text-amber-500 group-hover:text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
              </div>
              <div class="flex-1">
                <div class="font-bold text-textMain group-hover:text-sidebar">Ubah Kata Sandi</div>
                <div class="text-[10px] text-textMuted">Perbarui kata sandi login akun Anda</div>
              </div>
            </button>

            <!-- Aksi 2: Manajemen Pengguna (Khusus Role Super Admin / Permission system.users) -->
            @if($authUser->isSuperAdmin() || $authUser->hasPermission('system.users'))
              <a href="{{ route('master.users.index') }}" 
                 class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-textMain hover:bg-panelBg hover:text-sidebar transition text-left group">
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-brandOrange flex items-center justify-center group-hover:bg-brandOrange group-hover:text-white transition shrink-0 shadow-2xs">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <div class="font-bold text-textMain group-hover:text-sidebar">Manajemen Pengguna</div>
                  <div class="text-[10px] text-textMuted">Kelola akun staf, kasir, &amp; hak akses</div>
                </div>
              </a>
            @endif

            <!-- Info Sesi & Waktu Operasional -->
            <div class="px-3.5 py-2 my-1 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between text-[11px] text-slate-500">
              <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Sesi: {{ now()->translatedFormat('d M Y') }}</span>
              </span>
              <span class="font-mono text-[10px] text-slate-400">ID #{{ str_pad($authUser->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>

            <!-- Divider -->
            <div class="border-t border-[#ECEBF4] my-1"></div>

            <!-- Aksi 3: Tombol Logout / Keluar -->
            <form method="POST" action="{{ route('logout') }}" id="headerLogoutForm" class="m-0">
              @csrf
              <button type="button" 
                      onclick="confirmHeaderLogout()"
                      class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-rose-600 hover:bg-rose-50 transition text-left group">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition shrink-0 shadow-2xs">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                </div>
                <div class="flex-1">
                  <div class="font-bold text-rose-600">Keluar dari Sistem</div>
                  <div class="text-[10px] text-rose-400">Akhiri sesi kerja di perangkat ini</div>
                </div>
              </button>
            </form>

          </div>
        </div>
      </div>
    @else
      <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-xl bg-sidebar text-white text-xs font-bold transition hover:bg-sidebarDark">
        Masuk
      </a>
    @endauth

  </div>
</header>

<script>
  function toggleUserProfileDropdown() {
    const menu = document.getElementById('userProfileDropdownMenu');
    const chevron = document.getElementById('userProfileChevron');
    const btn = document.getElementById('userProfileTriggerBtn');
    
    if (!menu) return;
    
    const isHidden = menu.classList.contains('hidden');
    if (isHidden) {
      menu.classList.remove('hidden');
      if (chevron) chevron.classList.add('rotate-180');
      if (btn) btn.setAttribute('aria-expanded', 'true');
    } else {
      closeUserProfileDropdown();
    }
  }

  function closeUserProfileDropdown() {
    const menu = document.getElementById('userProfileDropdownMenu');
    const chevron = document.getElementById('userProfileChevron');
    const btn = document.getElementById('userProfileTriggerBtn');
    
    if (menu && !menu.classList.contains('hidden')) {
      menu.classList.add('hidden');
      if (chevron) chevron.classList.remove('rotate-180');
      if (btn) btn.setAttribute('aria-expanded', 'false');
    }
  }

  function confirmHeaderLogout() {
    if (typeof SwalConfirm !== 'undefined') {
      SwalConfirm.fire({
        title: 'Konfirmasi Keluar',
        text: 'Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem PasarHub?',
        icon: 'warning',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        customClass: {
          popup: 'swal2-modal',
          title: 'font-display text-base font-bold text-textMain',
          confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-xs shadow-sm transition active:scale-95 text-white bg-rose-600 hover:bg-rose-700 cursor-pointer',
          cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-xs bg-panelBg hover:bg-[#EAE8F2] text-textMain transition active:scale-95 mr-2 cursor-pointer'
        }
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('headerLogoutForm').submit();
        }
      });
    } else {
      if (confirm('Apakah Anda yakin ingin keluar dari sistem PasarHub?')) {
        document.getElementById('headerLogoutForm').submit();
      }
    }
  }

  // Close dropdown on outside click
  document.addEventListener('click', function(event) {
    const container = document.getElementById('userProfileDropdownContainer');
    if (container && !container.contains(event.target)) {
      closeUserProfileDropdown();
    }
  });

  // Close dropdown on Escape key
  window.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      closeUserProfileDropdown();
    }
  });
</script>
