<!-- =======================================================
     PAGE 0 (INDEX 0): DASBOR UTAMA & KESEHATAN FINANSIAL
     Sistem Operasional Pasar Terpadu (Gudang, Toko, & B2B)
     ======================================================= -->
<div id="page-0" class="page-view space-y-7">

  <!-- 1. SOFT SOLID ORANGE HERO BANNER (Mobile & Tablet Priority) -->
  <div class="relative bg-brandOrange rounded-[26px] p-6 sm:p-8 text-white overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-sm">
    <div class="relative z-10 max-w-xl">
      <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-xs font-semibold mb-3">
        <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
        <span data-i18n="hero_live_badge">Sistem Operasional Pasar Terpadu • Real-Time</span>
      </div>
      <h1 class="font-display text-2xl sm:text-[32px] font-bold tracking-tight leading-tight">
        Halo, {{ $user->name }}!
      </h1>
      <p class="text-white/95 text-xs sm:text-sm mt-1.5 font-medium leading-relaxed">
        {{ $stats['user_role_label'] }} • Lokasi Aktif: <strong class="font-bold underline">{{ $stats['active_location_name'] }}</strong> ({{ $stats['active_location_code'] }})
      </p>

      <!-- 3 Pillar Quick Badges -->
      <div class="mt-4 flex flex-wrap items-center gap-2">
        <span class="bg-white/20 hover:bg-white/30 transition px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 cursor-pointer" onclick="switchPage(1)">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
          </svg>
          <span>Gudang: {{ $activeLocation && $activeLocation->isWarehouse() ? 'Lokasi Aktif' : 'Gudang Sentral' }}</span>
        </span>
        <span class="bg-white/20 hover:bg-white/30 transition px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 cursor-pointer" onclick="switchPage(2)">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span>Toko: {{ $activeLocation && $activeLocation->isStore() ? 'Lokasi Aktif' : 'Toko Pasar Raya' }}</span>
        </span>
        <span class="bg-white/20 hover:bg-white/30 transition px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 cursor-pointer" onclick="switchPage(3)">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span>{{ $stats['total_users'] }} Akun Sistem Terdaftar</span>
        </span>
      </div>
    </div>

    <!-- Quick Touch Actions (Min 48x48px Touch Targets for Mobile/Tablet Operators) -->
    <div class="relative z-10 flex flex-col sm:flex-row md:flex-col gap-2.5 w-full md:w-auto shrink-0">
      <button onclick="simulateScanQR()" class="min-h-[48px] w-full px-5 py-3 rounded-2xl bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm active:scale-95">
        <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
        </svg>
        <span data-i18n="btn_scan_qr">Scan QR Member / Tiket</span>
      </button>

      <div class="flex gap-2 w-full">
        @if($user->hasPermission('emergency.input'))
          <button onclick="openEmergencyModal()" class="min-h-[48px] flex-1 px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-95">
            <span class="w-2 h-2 rounded-full bg-moveOrange"></span>
            <span data-i18n="btn_input_emergency">+ Stok Emergency</span>
          </button>
        @else
          <button onclick="openToast('Izin Dibatasi: Input stok emergency toko hanya untuk Kepala Toko dan Staf Toko.')" class="min-h-[48px] flex-1 px-4 py-2.5 rounded-xl bg-white/10 text-white/60 text-xs font-semibold flex items-center justify-center gap-1.5 cursor-not-allowed">
            <span class="w-2 h-2 rounded-full bg-white/40"></span>
            <span>+ Stok Emergency</span>
          </button>
        @endif

        <button onclick="filterMovementStream('red')" class="min-h-[48px] px-3.5 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-95" title="Filter Anomali">
          <span class="w-2 h-2 rounded-full bg-moveRed animate-pulse"></span>
          <span data-i18n="btn_opname_check">Anomali</span>
        </button>
      </div>
    </div>
  </div>

  <!-- 2. KARTU KESEHATAN FINANSIAL ATAU RINGKASAN TUGAS OPERASIONAL -->
  @if($user->hasPermission('dashboard.financial'))
    <!-- Financial Section (For Owner, Super Admin, Kepala Toko, Kepala Gudang) -->
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <h2 class="font-display font-bold text-lg text-textMain" data-i18n="section_financial_health">Kesehatan Finansial & Aset Pasar</h2>
          @if($user->isOwner())
            <span class="text-[10px] font-bold text-sidebar bg-brandLavender px-2 py-0.5 rounded-full border border-[#D5D2E8]">
              Owner (Read-Only)
            </span>
          @endif
        </div>
        <span class="text-xs text-textMuted font-medium" data-i18n="financial_sync_time">Diperbarui: Hari Ini (Real-Time)</span>
      </div>

      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Card 1: Omzet Hari Ini -->
        <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted" data-i18n="stat_omzet_today">Omzet Hari Ini</span>
            <div class="w-7 h-7 rounded-lg bg-movePurpleBg text-movePurple flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <p class="font-display text-xl sm:text-2xl font-extrabold text-textMain mt-3">Rp 48.250.000</p>
          <p class="text-[11px] text-textMuted mt-1" data-i18n="stat_omzet_sub">Toko: Rp 18.5M • B2B: Rp 29.7M</p>
        </div>

        <!-- Card 2: Estimasi Laba Kotor -->
        <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted" data-i18n="stat_profit_est">Laba Kotor</span>
            <div class="w-7 h-7 rounded-lg bg-moveGreenBg text-moveGreen flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
              </svg>
            </div>
          </div>
          <p class="font-display text-xl sm:text-2xl font-extrabold text-moveGreen mt-3">Rp 7.820.000</p>
          <p class="text-[11px] text-textMuted mt-1" data-i18n="stat_profit_sub">Margin Real-Time: +16.2%</p>
        </div>

        <!-- Card 3: Nilai Aset Stok -->
        <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted" data-i18n="stat_stock_asset">Nilai Aset Stok</span>
            <div class="w-7 h-7 rounded-lg bg-moveBlueBg text-moveBlue flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
            </div>
          </div>
          <p class="font-display text-xl sm:text-2xl font-extrabold text-textMain mt-3">Rp 482.900.000</p>
          <p class="text-[11px] text-textMuted mt-1" data-i18n="stat_stock_sub">Gudang 75% • Toko 25%</p>
        </div>

        <!-- Card 4: Piutang B2B Berjalan -->
        <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted" data-i18n="stat_b2b_debt">Piutang B2B</span>
            <div class="w-7 h-7 rounded-lg bg-moveOrangeBg text-moveOrange flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <p class="font-display text-xl sm:text-2xl font-extrabold text-textMain mt-3">Rp 86.400.000</p>
          <p class="text-[11px] text-moveGreen font-semibold mt-1" data-i18n="stat_b2b_sub">Plafon Aman (0 Overdue)</p>
        </div>
      </div>
    </div>
  @else
    <!-- Operational Task & Assignment Section (For Staf Toko, Staf Gudang, Sales, B2B Mitra) -->
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <h2 class="font-display font-bold text-lg text-textMain">Ringkasan Tugas & Penugasan Shift</h2>
        <span class="text-xs text-textMuted font-medium">RBAC Server Guard Aktif</span>
      </div>

      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Card 1: Lokasi Kerja Aktif -->
        <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4]">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Lokasi Tugas</span>
          <p class="font-display text-base sm:text-lg font-bold text-sidebar mt-2 truncate">{{ $stats['active_location_name'] }}</p>
          <p class="text-[11px] text-textMuted mt-1">Kode: {{ $stats['active_location_code'] }}</p>
        </div>

        <!-- Card 2: Peran Akun -->
        <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4]">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Peran Akun</span>
          <p class="font-display text-base sm:text-lg font-bold text-brandOrange mt-2">{{ $stats['user_role_label'] }}</p>
          <p class="text-[11px] text-textMuted mt-1">Zero-Bottleneck Execution</p>
        </div>

        <!-- Card 3: Izin Granular -->
        <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4]">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Hak Akses Granular</span>
          <p class="font-display text-base sm:text-lg font-bold text-movePurple mt-2">{{ $user->allPermissions()->count() }} Izin Terpasang</p>
          <p class="text-[11px] text-textMuted mt-1">Diverifikasi di Server</p>
        </div>

        <!-- Card 4: Keamanan Sesi -->
        <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4]">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Audit & Keamanan</span>
          <p class="font-display text-base sm:text-lg font-bold text-moveGreen mt-2">Sesi Aman Aktif</p>
          <p class="text-[11px] text-textMuted mt-1">Audit Trail Mengikat ID Pengguna</p>
        </div>
      </div>
    </div>
  @endif

  <!-- 3. KOMPONEN STANDAR INDIKATOR 5-WARNA PERGERAKAN (PRD BAGIAN 2.4) -->
  <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-3">
      <div>
        <h3 class="font-display font-bold text-base text-textMain" data-i18n="color_standards_title">
          Standar Indikator Warna Pergerakan Sistem
        </h3>
        <p class="text-xs text-textMuted mt-0.5" data-i18n="color_standards_subtitle">
          Pedoman visual operasional untuk mengenali status stok & transaksi dalam 1 detik tanpa membaca tabel angka rumit
        </p>
      </div>
      <span class="text-[11px] font-bold text-sidebar bg-sidebar/10 px-3 py-1 rounded-full self-start sm:self-auto">
        PRD Rule 2.4
      </span>
    </div>

    <!-- 5 Standard Movement Badges Showcase -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-1">
      <!-- Green Badge -->
      <div onclick="filterMovementStream('green')" class="p-3.5 rounded-2xl bg-[#ECFDF5]/70 border border-[#A7F3D0] flex flex-col justify-between cursor-pointer hover:bg-[#ECFDF5] transition duration-200">
        <div class="flex items-center justify-between">
          <x-movement-badge type="green" size="sm" />
          <span class="text-[10px] font-bold text-[#065F46] font-mono">#10B981</span>
        </div>
        <p class="text-xs font-bold text-[#065F46] mt-2.5" data-i18n="color_green_title">Barang Masuk Normal</p>
        <p class="text-[11px] text-[#065F46]/80 mt-1 leading-snug" data-i18n="color_green_desc">
          Penerimaan PO Suplayer, Konversi Satuan Masuk, Retur Pelanggan Layak Jual
        </p>
      </div>

      <!-- Blue Badge -->
      <div onclick="filterMovementStream('blue')" class="p-3.5 rounded-2xl bg-[#EFF6FF]/70 border border-[#BFDBFE] flex flex-col justify-between cursor-pointer hover:bg-[#EFF6FF] transition duration-200">
        <div class="flex items-center justify-between">
          <x-movement-badge type="blue" size="sm" />
          <span class="text-[10px] font-bold text-[#1E40AF] font-mono">#3B82F6</span>
        </div>
        <p class="text-xs font-bold text-[#1E40AF] mt-2.5" data-i18n="color_blue_title">Pergerakan Internal</p>
        <p class="text-[11px] text-[#1E40AF]/80 mt-1 leading-snug" data-i18n="color_blue_desc">
          Transfer Stok Gudang ↔ Toko, Alokasi Barang untuk Tiket Pick-up Mandiri
        </p>
      </div>

      <!-- Purple Badge -->
      <div onclick="filterMovementStream('purple')" class="p-3.5 rounded-2xl bg-[#F5F3FF]/70 border border-[#DDD6FE] flex flex-col justify-between cursor-pointer hover:bg-[#F5F3FF] transition duration-200">
        <div class="flex items-center justify-between">
          <x-movement-badge type="purple" size="sm" />
          <span class="text-[10px] font-bold text-[#5B21B6] font-mono">#8B5CF6</span>
        </div>
        <p class="text-xs font-bold text-[#5B21B6] mt-2.5" data-i18n="color_purple_title">Penjualan Keluar</p>
        <p class="text-[11px] text-[#5B21B6]/80 mt-1 leading-snug" data-i18n="color_purple_desc">
          Transaksi Kasir POS Ritel Toko & Pesanan Kemitraan B2B Selesai
        </p>
      </div>

      <!-- Orange Badge -->
      <div onclick="filterMovementStream('orange')" class="p-3.5 rounded-2xl bg-[#FFFBEB]/70 border border-[#FDE68A] flex flex-col justify-between cursor-pointer hover:bg-[#FFFBEB] transition duration-200">
        <div class="flex items-center justify-between">
          <x-movement-badge type="orange" size="sm" />
          <span class="text-[10px] font-bold text-[#92400E] font-mono">#F59E0B</span>
        </div>
        <p class="text-xs font-bold text-[#92400E] mt-2.5" data-i18n="color_orange_title">Stok Emergency</p>
        <p class="text-[11px] text-[#92400E]/80 mt-1 leading-snug" data-i18n="color_orange_desc">
          Input Darurat Toko (+Foto Nota), Penyesuaian Harga, Piutang Limit
        </p>
      </div>

      <!-- Red Badge -->
      <div onclick="filterMovementStream('red')" class="p-3.5 rounded-2xl bg-[#FEF2F2]/70 border border-[#FECACA] flex flex-col justify-between cursor-pointer hover:bg-[#FEF2F2] transition duration-200">
        <div class="flex items-center justify-between">
          <x-movement-badge type="red" size="sm" />
          <span class="text-[10px] font-bold text-[#991B1B] font-mono">#EF4444</span>
        </div>
        <p class="text-xs font-bold text-[#991B1B] mt-2.5" data-i18n="color_red_title">Anomali & Selisih</p>
        <p class="text-[11px] text-[#991B1B]/80 mt-1 leading-snug" data-i18n="color_red_desc">
          Selisih Negatif Opname (Rusak/Susut/Hilang), Void Struk, Minus Kasir
        </p>
      </div>
    </div>
  </div>

  <!-- 4. FEED PERGERAKAN BERWARNA REAL-TIME (LIVE COLOR MOVEMENT STREAM) -->
  <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-5">
    
    <!-- Stream Filter Header & Chips -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h3 class="font-display font-bold text-base text-textMain" data-i18n="movement_stream_title">
          Feed Pergerakan 5-Warna Real-Time
        </h3>
        <p class="text-xs text-textMuted mt-0.5" data-i18n="movement_stream_desc">
          Audit trail tanpa botol approval: Setiap pergerakan langsung dieksekusi dengan pertanggungjawaban akun
        </p>
      </div>

      <!-- Filter Chips -->
      <div class="flex flex-wrap items-center gap-1.5 text-xs font-semibold">
        <button id="chipFilterAll" onclick="filterMovementStream('all')" class="stream-chip px-3 py-1.5 rounded-xl bg-sidebar text-white transition active:scale-95" data-i18n="filter_all">
          Semua Alur
        </button>
        <button id="chipFilterGreen" onclick="filterMovementStream('green')" class="stream-chip px-3 py-1.5 rounded-xl bg-panelBg text-textMain hover:bg-[#EAE8F2] transition active:scale-95">
          <span class="w-2 h-2 rounded-full bg-moveGreen inline-block mr-1"></span>
          <span data-i18n="filter_green">Masuk</span>
        </button>
        <button id="chipFilterBlue" onclick="filterMovementStream('blue')" class="stream-chip px-3 py-1.5 rounded-xl bg-panelBg text-textMain hover:bg-[#EAE8F2] transition active:scale-95">
          <span class="w-2 h-2 rounded-full bg-moveBlue inline-block mr-1"></span>
          <span data-i18n="filter_blue">Internal</span>
        </button>
        <button id="chipFilterPurple" onclick="filterMovementStream('purple')" class="stream-chip px-3 py-1.5 rounded-xl bg-panelBg text-textMain hover:bg-[#EAE8F2] transition active:scale-95">
          <span class="w-2 h-2 rounded-full bg-movePurple inline-block mr-1"></span>
          <span data-i18n="filter_purple">Penjualan</span>
        </button>
        <button id="chipFilterOrange" onclick="filterMovementStream('orange')" class="stream-chip px-3 py-1.5 rounded-xl bg-panelBg text-textMain hover:bg-[#EAE8F2] transition active:scale-95">
          <span class="w-2 h-2 rounded-full bg-moveOrange inline-block mr-1"></span>
          <span data-i18n="filter_orange">Emergency</span>
        </button>
        <button id="chipFilterRed" onclick="filterMovementStream('red')" class="stream-chip px-3 py-1.5 rounded-xl bg-panelBg text-textMain hover:bg-[#EAE8F2] transition active:scale-95">
          <span class="w-2 h-2 rounded-full bg-moveRed inline-block mr-1"></span>
          <span data-i18n="filter_red">Anomali</span>
        </button>
      </div>
    </div>

    <!-- Active Movement Stream List (Populated & Filtered by JS) -->
    <div id="movementStreamContainer" class="space-y-3">
      <!-- Populated dynamically by renderMovementStream() -->
    </div>
  </div>

</div>
