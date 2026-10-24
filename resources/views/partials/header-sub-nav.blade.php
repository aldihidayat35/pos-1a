@php
  // Determine current active module and its sub-menu items
  $subMenus = [];
  $activeSubMenuLabel = 'Ringkasan';

  if (request()->routeIs('master.*')) {
      $subMenus = [
          [
              'label' => 'Katalog Produk',
              'route' => route('master.products.index'),
              'active' => request()->routeIs('master.products.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
          ],
          [
              'label' => 'Kategori',
              'route' => route('master.categories.index'),
              'active' => request()->routeIs('master.categories.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>',
          ],
          [
              'label' => 'Satuan UOM',
              'route' => route('master.uoms.index'),
              'active' => request()->routeIs('master.uoms.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 18h12l3-18H3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/>',
          ],
          [
              'label' => 'Suplayer',
              'route' => route('master.suppliers.index'),
              'active' => request()->routeIs('master.suppliers.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
          ],
          [
              'label' => 'Member & B2B',
              'route' => route('master.members.index'),
              'active' => request()->routeIs('master.members.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
          ],
          [
              'label' => 'Gudang & Toko',
              'route' => route('master.locations.index'),
              'active' => request()->routeIs('master.locations.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>',
          ],
          [
              'label' => 'Kebijakan Harga & Floor Lock',
              'route' => route('master.pricing.index'),
              'active' => request()->routeIs('master.pricing.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
          ],
      ];

      if (auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('system.users'))) {
          $subMenus[] = [
              'label' => 'Pengguna & Hak Akses',
              'route' => route('master.users.index'),
              'active' => request()->routeIs('master.users.*') || request()->routeIs('master.roles.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
          ];
      }
  } elseif (request()->routeIs('warehouse.*')) {
      $subMenus = [
          [
              'label' => 'Ringkasan Gudang',
              'route' => route('warehouse.index'),
              'active' => request()->routeIs('warehouse.index'),
              'icon' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>',
          ],
          [
              'label' => 'Pesanan PO Suplayer',
              'route' => route('warehouse.po.index'),
              'active' => request()->routeIs('warehouse.po.*') || request()->routeIs('warehouse.receipts.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
          ],
          [
              'label' => 'Kartu Stok & Ledger',
              'route' => route('warehouse.ledger.index'),
              'active' => request()->routeIs('warehouse.ledger.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
          ],
          [
              'label' => 'Kirim Transfer Toko',
              'route' => route('warehouse.transfers.index'),
              'active' => request()->routeIs('warehouse.transfers.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>',
          ],
          [
              'label' => 'Blind Opname',
              'route' => route('opname.index'),
              'active' => request()->routeIs('opname.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
          ],
      ];
  } elseif (request()->routeIs('store.*')) {
      $subMenus = [
          [
              'label' => 'Terminal POS Kasir',
              'route' => route('store.pos.terminal'),
              'active' => request()->routeIs('store.pos.terminal'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>',
          ],
          [
              'label' => 'Ringkasan Toko',
              'route' => route('store.index'),
              'active' => request()->routeIs('store.index'),
              'icon' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>',
          ],
          [
              'label' => 'Riwayat Struk & Void',
              'route' => route('store.pos.history'),
              'active' => request()->routeIs('store.pos.history') || request()->routeIs('store.pos.receipt'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
          ],
          [
              'label' => 'Shift Laci Kasir',
              'route' => route('store.shifts.index'),
              'active' => request()->routeIs('store.shifts.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>',
          ],
          [
              'label' => 'Stok Emergency (+Nota)',
              'route' => route('store.emergency.index'),
              'active' => request()->routeIs('store.emergency.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
          ],
          [
              'label' => 'Kiriman Transfer Masuk',
              'route' => route('store.transfers.index'),
              'active' => request()->routeIs('store.transfers.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>',
          ],
      ];
  } elseif (request()->routeIs('b2b.*')) {
      $subMenus = [
          [
              'label' => 'Dasbor & Mitra',
              'route' => route('b2b.index'),
              'active' => request()->routeIs('b2b.index'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
          ],
          [
              'label' => 'Buat Order B2B',
              'route' => route('b2b.orders.create'),
              'active' => request()->routeIs('b2b.orders.create'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>',
          ],
          [
              'label' => 'Daftar Pesanan & Reservasi',
              'route' => route('b2b.orders.index'),
              'active' => request()->routeIs('b2b.orders.index') || request()->routeIs('b2b.orders.show'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
          ],
          [
              'label' => 'Scan Tiket Pickup QR',
              'route' => route('b2b.pickup.scan'),
              'active' => request()->routeIs('b2b.pickup.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>',
          ],
          [
              'label' => 'Faktur & Piutang Tempo',
              'route' => route('b2b.invoices.index'),
              'active' => request()->routeIs('b2b.invoices.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
          ],
      ];
  } elseif (request()->routeIs('audit.*')) {
      $subMenus = [
          [
              'label' => 'Semua Anomali',
              'route' => route('audit.index'),
              'active' => !request()->has('severity'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
          ],
          [
              'label' => 'Selisih Opname (Merah)',
              'route' => route('audit.index', ['severity' => 'RED']),
              'active' => request('severity') === 'RED',
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
          ],
          [
              'label' => 'Belanja Darurat (Oranye)',
              'route' => route('audit.index', ['severity' => 'ORANGE']),
              'active' => request('severity') === 'ORANGE',
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
          ],
          [
              'label' => 'Sesi Opname Fisik',
              'route' => route('opname.index'),
              'active' => request()->routeIs('opname.*'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
          ],
      ];
  } elseif (request()->routeIs('opname.*')) {
      $subMenus = [
          [
              'label' => 'Semua Sesi Opname',
              'route' => route('opname.index'),
              'active' => request()->routeIs('opname.index') || request()->routeIs('opname.show'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
          ],
          [
              'label' => 'Buka Sesi Opname Baru',
              'route' => route('opname.create'),
              'active' => request()->routeIs('opname.create'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>',
          ],
          [
              'label' => 'Log Selisih Susut (Merah)',
              'route' => route('audit.index', ['severity' => 'RED']),
              'active' => false,
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
          ],
      ];
  } elseif (request()->routeIs('reports.*')) {
      $subMenus = [
          [
              'label' => 'Ringkasan Finansial',
              'route' => route('reports.index'),
              'active' => request()->routeIs('reports.index'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
          ],
          [
              'label' => 'Buku Penjualan & Margin',
              'route' => route('reports.sales'),
              'active' => request()->routeIs('reports.sales'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
          ],
          [
              'label' => 'Valuasi Stok Aset',
              'route' => route('reports.inventory'),
              'active' => request()->routeIs('reports.inventory'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
          ],
          [
              'label' => 'Penuaan Piutang B2B',
              'route' => route('reports.receivables'),
              'active' => request()->routeIs('reports.receivables'),
              'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
          ],
      ];
  } else {
      // Default: Dasbor Utama (Hanya Ringkasan Utama, tanpa filter warna)
      $subMenus = [
          [
              'label' => 'Ringkasan Utama',
              'route' => route('dashboard'),
              'active' => true,
              'icon' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>',
          ],
      ];
  }

  // Find active label for mobile header
  foreach ($subMenus as $item) {
      if ($item['active']) {
          $activeSubMenuLabel = $item['label'];
          break;
      }
  }
@endphp

<!-- MOBILE ONLY: Hamburger Dropdown for Sub-Menus -->
<div class="relative md:hidden" id="mobileMenuWrapper">
  <button id="mobileMenuBtn" onclick="toggleMobileSubMenu(event)" class="min-h-[44px] flex items-center gap-2 px-3 py-1.5 rounded-xl bg-panelBg hover:bg-[#EAE8F2] text-sidebar transition border border-[#E6E4F0]" aria-label="Menu Sub-Modul">
    <svg id="hamburgerIcon" class="w-4 h-4 text-sidebar shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
    <span id="mobileActiveSubMenuLabel" class="text-xs font-bold text-sidebar max-w-[140px] truncate">
      {{ $activeSubMenuLabel }}
    </span>
    <svg id="mobileMenuChevron" class="w-3.5 h-3.5 text-textMuted transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
    </svg>
  </button>

  <!-- Mobile Dropdown Menu -->
  <div id="mobileSubMenuDropdown" class="hidden absolute left-0 top-full mt-2 w-64 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-[#E8E6F2] p-2 z-50 transform origin-top-left transition-all duration-200 scale-95 opacity-0">
    <div class="px-2.5 py-1 text-[10px] font-bold tracking-wider text-textMuted uppercase border-b border-[#F0EFF6] mb-1">
      Sub-Modul Operasional
    </div>
    <div class="flex flex-col gap-1">
      @foreach($subMenus as $item)
        <a href="{{ $item['route'] }}" 
           @if(isset($item['onclick'])) onclick="{{ $item['onclick'] }}" @endif
           class="flex items-center justify-between w-full px-3 py-2 rounded-xl text-xs transition {{ $item['active'] ? 'bg-panelBg text-sidebar font-bold border border-[#E6E4F0]' : 'text-textMuted hover:text-textMain hover:bg-[#FAF9FD]' }}">
           <div class="flex items-center gap-2">
             @if(!empty($item['icon']))
               <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                 {!! $item['icon'] !!}
               </svg>
             @endif
             <span>{{ $item['label'] }}</span>
           </div>
           @if($item['active'])
             <span class="w-2 h-2 rounded-full bg-brandOrange"></span>
           @endif
        </a>
      @endforeach
    </div>
  </div>
</div>

<!-- DESKTOP ONLY: Contextual Sub-Menu Bar with Dynamic Overflow Arrow Dropdown -->
<div id="desktopSubMenuWrapper" class="hidden md:flex items-center relative min-w-0">
  <div id="desktopSubMenuContainer" class="inline-flex items-center gap-1 bg-panelBg p-1 rounded-xl text-xs font-semibold text-textMuted border border-[#ECEBF4] min-w-0">
    @foreach($subMenus as $idx => $item)
      <a href="{{ $item['route'] }}" 
         data-submenu-index="{{ $idx }}"
         data-submenu-active="{{ $item['active'] ? '1' : '0' }}"
         @if(isset($item['onclick'])) onclick="{{ $item['onclick'] }}" @endif
         class="submenu-desktop-item flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition whitespace-nowrap {{ $item['active'] ? 'bg-white text-sidebar font-bold border border-[#E6E4F0] shadow-sm' : 'text-textMuted hover:text-textMain hover:bg-white/60' }}">
         @if(!empty($item['icon']))
           <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
             {!! $item['icon'] !!}
           </svg>
         @endif
         <span>{{ $item['label'] }}</span>
      </a>
    @endforeach

    <!-- Desktop Overflow Arrow Button (Appears when items are hidden/overflowing) -->
    <div class="relative hidden shrink-0" id="desktopOverflowWrapper">
      <button id="desktopOverflowBtn" onclick="toggleDesktopOverflowMenu(event)" class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg transition bg-white text-sidebar font-bold border border-[#E6E4F0] shadow-sm hover:bg-[#FAF9FD]" title="Menu Tambahan">
        <span id="desktopOverflowCountBadge" class="text-[11px] font-extrabold text-sidebar"></span>
        <svg id="desktopOverflowChevron" class="w-3.5 h-3.5 text-sidebar transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <!-- Desktop Overflow Dropdown Popup -->
      <div id="desktopOverflowDropdown" class="hidden absolute right-0 top-full mt-2 w-56 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-[#E8E6F2] p-2 z-50 transform origin-top-right transition-all duration-200 scale-95 opacity-0">
        <div class="px-2.5 py-1 text-[10px] font-bold tracking-wider text-textMuted uppercase border-b border-[#F0EFF6] mb-1">
          Menu Tambahan
        </div>
        <div id="desktopOverflowList" class="flex flex-col gap-1">
          <!-- Dynamically filled with overflow items -->
        </div>
      </div>
    </div>
  </div>
</div>
