<!-- ============================================================
     APPLICATION LOGIC & INTERACTIVE DATA (FASE 1: PASAR TERPADU)
     - i18n Multi-Language Engine (ID / EN)
     - Standard 5-Movement Color Stream & Filtering
     - Mobile & Tablet Priority Interactions
     - Liquid Curve Navigation & Dashboard Controls
     ============================================================ -->
<script data-global-scripts="true">
  // ============================================================
  // 1. MULTI-LANGUAGE (i18n) ENGINE & DICTIONARY
  // ============================================================
  let currentLang = localStorage.getItem('pasar_lang') || 'id';

  const i18n = {
    id: {
      app_title: 'PasarHub — Sistem Manajemen Operasional Pasar Terpadu',
      role_owner: 'Owner & Pengawas',
      header_sub_menu_title: 'Sub-Modul Operasional',
      header_color_legend: 'Warna Pergerakan',
      
      // Nav Menu Titles
      nav_menu_0: 'Dasbor Utama & Kesehatan Finansial',
      nav_menu_1: 'Operasional Gudang Pusat',
      nav_menu_2: 'Operasional Toko & Kasir POS',
      nav_menu_3: 'Kemitraan B2B & Plafon Piutang',
      nav_menu_4: 'Feed Pergerakan 5-Warna & Anomali',
      nav_menu_5: 'Master Produk, Satuan & Member QR',
      nav_exit_session: 'Akhiri Sesi / Kunci Terminal',
      
      // Toasts
      toast_shift_saved: 'Sesi operasional disimpan. Sistem siap dikunci.',
      toast_lang_switched: 'Bahasa sistem berhasil diubah ke Bahasa Indonesia (ID).',
      toast_qr_scanned: 'Scan Berhasil: Member QR B2B #MBR-7702 (CV Berkah Jaya) • Plafon Kredit Rp 50.000.000 (Sisa Aman: Rp 22.400.000)',
      toast_emergency_added: 'Stok Emergency Berhasil Disimpan: Telur Ayam (+6 Krat) otomatis bertambah di Toko tanpa bottleneck approval!',
      toast_audit_info: 'Audit Trail: Tercatat permanen atas user_id dan role eksekutor.',
      
      // Hero & Dashboard
      hero_live_badge: 'Sistem Operasional Pasar Terpadu • Real-Time',
      hero_welcome: 'Halo, Barbara!',
      hero_subtitle: 'Ekosistem Terpadu Rantai Pasok Gudang Pusat, Penjualan Ritel Toko, dan Kemitraan Grosir B2B',
      hero_badge_wh: 'Gudang: 14.820 SKU',
      hero_badge_store: 'Toko: 18 Kasir & Counter',
      hero_badge_b2b: 'B2B: 42 Mitra Aktif',
      
      // Quick Buttons
      btn_scan_qr: 'Scan QR Member / Tiket',
      btn_input_emergency: '+ Stok Emergency',
      btn_opname_check: 'Anomali',
      btn_reset_view: 'Reset Tampilan',
      
      // Financial Health Section
      section_financial_health: 'Kesehatan Finansial & Aset Pasar',
      financial_sync_time: 'Diperbarui: Hari Ini, 14:40 WIB',
      stat_omzet_today: 'Omzet Hari Ini',
      stat_omzet_sub: 'Toko: Rp 18.5M • B2B: Rp 29.7M',
      stat_profit_est: 'Laba Kotor',
      stat_profit_sub: 'Margin Real-Time: +16.2%',
      stat_stock_asset: 'Nilai Aset Stok',
      stat_stock_sub: 'Gudang 75% • Toko 25%',
      stat_b2b_debt: 'Piutang B2B',
      stat_b2b_sub: 'Plafon Aman (0 Overdue)',
      
      // Color Standards
      color_standards_title: 'Standar Indikator Warna Pergerakan Sistem',
      color_standards_subtitle: 'Pedoman visual operasional untuk mengenali status stok & transaksi dalam 1 detik tanpa membaca tabel angka rumit',
      color_green_title: 'Barang Masuk Normal',
      color_green_desc: 'Penerimaan PO Suplayer, Konversi Satuan Masuk, Retur Pelanggan Layak Jual',
      color_blue_title: 'Pergerakan Internal',
      color_blue_desc: 'Transfer Stok Gudang ↔ Toko, Alokasi Barang untuk Tiket Pick-up Mandiri',
      color_purple_title: 'Penjualan Keluar',
      color_purple_desc: 'Transaksi Kasir POS Ritel Toko & Pesanan Kemitraan B2B Selesai',
      color_orange_title: 'Stok Emergency',
      color_orange_desc: 'Input Darurat Toko (+Foto Nota), Penyesuaian Harga, Piutang Limit',
      color_red_title: 'Anomali & Selisih',
      color_red_desc: 'Selisih Negatif Opname (Rusak/Susut/Hilang), Void Struk, Minus Kasir',
      
      // Movement Stream Filter
      movement_stream_title: 'Feed Pergerakan 5-Warna Real-Time',
      movement_stream_desc: 'Audit trail tanpa botol approval: Setiap pergerakan langsung dieksekusi dengan pertanggungjawaban akun',
      filter_all: 'Semua Alur',
      filter_green: 'Masuk',
      filter_blue: 'Internal',
      filter_purple: 'Penjualan',
      filter_orange: 'Emergency',
      filter_red: 'Anomali',

      // Page 4 strings
      p4_badge: 'Zero-Bottleneck Accountability Board',
      p4_title: 'Audit Trail & Laporan Anomali Terpadu',
      p4_desc: 'Kendali mutu tanpa botol approval: Setiap tindakan mengikat user_id, role, dan timestamp permanen.',
      p4_btn_stream: 'Buka Live Stream',
      p4_card1_title: 'Stok Opname Toko & Gudang',
      p4_card1_sub: '2 Produk Susut/Bocor • Disahkan Langsung oleh Kepala Toko',
      p4_card2_title: 'Belanja Cepat Tanpa Approval',
      p4_card2_sub: 'Total Rp 2.650.000 • Seluruh Foto Nota Tersimpan di Log',
      p4_card3_title: 'Floor Price & Credit Plafon Lock',
      p4_card3_sub: 'Kunci Otomatis Kasir POS & Pengunci Piutang B2B Bekerja 100%',
      p4_board_title: 'Tabel Pertanggungjawaban Langsung Akun',
      p4_board_sub: 'Daftar eksekusi krusial tanpa approval yang mengikat identitas operator secara permanen',
      p4_audit_tag: 'Audit Trail RBAC'
    },
    en: {
      app_title: 'PasarHub — Unified Market Management System',
      role_owner: 'Owner & Supervisor',
      header_sub_menu_title: 'Operational Sub-Modules',
      header_color_legend: 'Movement Colors',
      
      // Nav Menu Titles
      nav_menu_0: 'Main Dashboard & Financial Health',
      nav_menu_1: 'Central Warehouse Operations',
      nav_menu_2: 'Store Operations & POS Register',
      nav_menu_3: 'B2B Partners & Credit Limits',
      nav_menu_4: '5-Color Movement Stream & Anomalies',
      nav_menu_5: 'Master Products, UOM & Member QR',
      nav_exit_session: 'End Session / Lock Terminal',
      
      // Toasts
      toast_shift_saved: 'Operational session saved. Terminal ready to lock.',
      toast_lang_switched: 'System language successfully switched to English (EN).',
      toast_qr_scanned: 'Scan Successful: B2B Member QR #MBR-7702 (CV Berkah Jaya) • Credit Limit Rp 50,000,000 (Available: Rp 22,400,000)',
      toast_emergency_added: 'Emergency Stock Saved: Fresh Eggs (+6 Crates) added immediately without approval bottleneck!',
      toast_audit_info: 'Audit Trail: Permanently bound to operator user_id and role.',
      
      // Hero & Dashboard
      hero_live_badge: 'Unified Market Operations • Real-Time Stream',
      hero_welcome: 'Hello, Barbara!',
      hero_subtitle: 'Integrated Supply Chain Ecosystem for Central Warehouse, Retail Store, and B2B Partners',
      hero_badge_wh: 'Warehouse: 14,820 SKUs',
      hero_badge_store: 'Store: 18 Registers & Counters',
      hero_badge_b2b: 'B2B: 42 Active Partners',
      
      // Quick Buttons
      btn_scan_qr: 'Scan Member / Ticket QR',
      btn_input_emergency: '+ Emergency Stock',
      btn_opname_check: 'Anomalies',
      btn_reset_view: 'Reset View',
      
      // Financial Health Section
      section_financial_health: 'Financial Health & Market Assets',
      financial_sync_time: 'Synced: Today, 14:40 WIB',
      stat_omzet_today: "Today's Revenue",
      stat_omzet_sub: 'Store: Rp 18.5M • B2B: Rp 29.7M',
      stat_profit_est: 'Gross Profit',
      stat_profit_sub: 'Real-Time Margin: +16.2%',
      stat_stock_asset: 'Stock Asset Value',
      stat_stock_sub: 'Warehouse 75% • Store 25%',
      stat_b2b_debt: 'B2B Receivables',
      stat_b2b_sub: 'Safe Credit (0 Overdue)',
      
      // Color Standards
      color_standards_title: '5-Color Standard Movement Indicators',
      color_standards_subtitle: 'Visual operational guidelines to recognize stock & cashflow states in 1 second without complex tables',
      color_green_title: 'Normal Inbound',
      color_green_desc: 'Supplier PO Receiving, Inbound UOM Conversion, Resellable Returns',
      color_blue_title: 'Internal Movement',
      color_blue_desc: 'Warehouse ↔ Store Stock Transfer, Self Pick-up Ticket Allocation',
      color_purple_title: 'Outbound Sales',
      color_purple_desc: 'Retail POS Cashier Transactions & Fulfilled B2B Orders',
      color_orange_title: 'Emergency Stock',
      color_orange_desc: 'Store Emergency Buying (+Receipt Photo), Price Adjustment, Credit Warnings',
      color_red_title: 'Anomalies & Deficits',
      color_red_desc: 'Negative Stocktake Discrepancies (Spillage/Loss), Receipt Voids, Cashier Shortage',
      
      // Movement Stream Filter
      movement_stream_title: 'Real-Time 5-Color Movement Feed',
      movement_stream_desc: 'Zero-bottleneck audit trail: Every action executes instantly with full account accountability',
      filter_all: 'All Flows',
      filter_green: 'Inbound',
      filter_blue: 'Internal',
      filter_purple: 'Sales',
      filter_orange: 'Emergency',
      filter_red: 'Anomalies',

      // Page 4 strings
      p4_badge: 'Zero-Bottleneck Accountability Board',
      p4_title: 'Audit Trail & Unified Anomaly Report',
      p4_desc: 'Quality control without approval bottlenecks: Every action permanently binds user_id, role, and timestamp.',
      p4_btn_stream: 'Open Live Stream',
      p4_card1_title: 'Store & Warehouse Stocktake',
      p4_card1_sub: '2 Items Damaged/Spilled • Finalized Directly by Store Manager',
      p4_card2_title: 'Fast Purchasing Without Approval',
      p4_card2_sub: 'Total Rp 2,650,000 • All Receipt Photos Stored in Log',
      p4_card3_title: 'Floor Price & Credit Plafon Lock',
      p4_card3_sub: 'Automated Cashier POS Lock & B2B Debt Blocker Working 100%',
      p4_board_title: 'Account Direct Responsibility Board',
      p4_board_sub: 'List of crucial operations executed without approval binding operator identities permanently',
      p4_audit_tag: 'RBAC Audit Trail'
    }
  };

  function setAppLanguage(lang) {
    currentLang = lang;
    localStorage.setItem('pasar_lang', lang);
    document.documentElement.lang = lang;

    // Update Language Toggle Button Styles
    const btnID = document.getElementById('langBtnID');
    const btnEN = document.getElementById('langBtnEN');
    if (btnID && btnEN) {
      if (lang === 'id') {
        btnID.className = 'px-2.5 py-1.5 rounded-lg transition duration-200 bg-sidebar text-white shadow-sm';
        btnEN.className = 'px-2.5 py-1.5 rounded-lg transition duration-200 text-textMuted hover:text-textMain';
      } else {
        btnEN.className = 'px-2.5 py-1.5 rounded-lg transition duration-200 bg-sidebar text-white shadow-sm';
        btnID.className = 'px-2.5 py-1.5 rounded-lg transition duration-200 text-textMuted hover:text-textMain';
      }
    }

    // Translate all [data-i18n] text nodes
    document.querySelectorAll('[data-i18n]').forEach(el => {
      const key = el.getAttribute('data-i18n');
      if (i18n[lang] && i18n[lang][key]) {
        el.innerText = i18n[lang][key];
      }
    });

    // Translate titles
    document.querySelectorAll('[data-i18n-title]').forEach(el => {
      const key = el.getAttribute('data-i18n-title');
      if (i18n[lang] && i18n[lang][key]) {
        el.title = i18n[lang][key];
      }
    });

    // Translate movement badge labels
    document.querySelectorAll('.badge-label-text').forEach(el => {
      const txt = lang === 'en' ? el.getAttribute('data-i18n-en') : el.getAttribute('data-i18n-id');
      if (txt) el.innerText = txt;
    });

    // Re-render sub menus and movement feed
    renderSubMenus();
    renderMovementStream();

    // Sync with backend session & user preference
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
      fetch('{{ route("locale.switch") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ locale: lang })
      }).catch(() => {});
    }

    openToast(i18n[lang].toast_lang_switched);
  }

  // ============================================================
  // 2. MOVEMENT STREAM DATA & 5-COLOR FILTER SYSTEM
  // ============================================================
  let activeStreamFilter = 'all';

  const movementStreamData = [
    {
      id: 'WH-MOV-1001',
      color: 'green',
      typeText: { id: 'Masuk Normal', en: 'Normal Inbound' },
      ref: 'PO-SUP-8921',
      title: 'Beras Pandan Wangi Premium (Karung 50 Kg)',
      qty: '+40 Karung (2.000 Kg)',
      location: 'Gudang Pusat • Loading Dock A1',
      actor: 'Hendra (Staf Gudang)',
      role: 'Staff Gudang • ID #USR-005',
      time: { id: '8 Menit Lalu', en: '8 mins ago' },
      notes: 'Penerimaan PO Suplayer lolos timbang digital.'
    },
    {
      id: 'WH-MOV-1002',
      color: 'blue',
      typeText: { id: 'Pergerakan Internal', en: 'Internal Transfer' },
      ref: 'TRF-INT-4011',
      title: 'Minyak Goreng Kemasan 2L (Karton)',
      qty: '15 Karton (Transfer Gudang -> Toko)',
      location: 'Lorong B-04 -> Rak Display Toko',
      actor: 'Raka (Kepala Toko)',
      role: 'Kepala Toko • ID #USR-003',
      time: { id: '22 Menit Lalu', en: '22 mins ago' },
      notes: 'Pemindahan antar lokasi tercatat di saldo kedua inventori.'
    },
    {
      id: 'WH-MOV-1003',
      color: 'purple',
      typeText: { id: 'Penjualan Keluar', en: 'Outbound Sales' },
      ref: 'POS-TRX-1044',
      title: 'Penjualan Kasir POS Ritel: 12 Item Sembako',
      qty: 'Total: Rp 485.000 (Tunai & QRIS)',
      location: 'Kasir Toko 01 • Layar Kasir',
      actor: 'Maya (Staf Toko Kasir)',
      role: 'Staf Toko • ID #USR-006',
      time: { id: '34 Menit Lalu', en: '34 mins ago' },
      notes: 'Transaksi dalam batas harga pasar & di atas floor price.'
    },
    {
      id: 'WH-MOV-1004',
      color: 'purple',
      typeText: { id: 'Pesanan B2B', en: 'B2B Sales' },
      ref: 'B2B-ORD-5502',
      title: 'Pesanan B2B: CV Berkah Jaya (Tiket Pick-up #TKT-88)',
      qty: '50 Karung Beras (Tempo 30 Hari - Rp 27.500.000)',
      location: 'Gudang Pusat • Siap Ambil',
      actor: 'Doni (Sales Eksekutif)',
      role: 'Sales • ID #USR-007',
      time: { id: '45 Menit Lalu', en: '45 mins ago' },
      notes: 'Plafon kredit mitra terverifikasi aman oleh sistem.'
    },
    {
      id: 'WH-MOV-1005',
      color: 'orange',
      typeText: { id: 'Stok Emergency', en: 'Emergency Stock' },
      ref: 'EMG-STK-004',
      title: 'Input Stok Emergency Toko: Telur Ayam Broiler',
      qty: '+6 Krat (Rp 1.450.000) [Foto Nota Terlampir]',
      location: 'Toko Sentral • Pembelian Darurat Pasar',
      actor: 'Raka (Kepala Toko)',
      role: 'Kepala Toko • ID #USR-003',
      time: { id: '52 Menit Lalu', en: '52 mins ago' },
      notes: 'Stok langsung bertambah tanpa menunggu approval Owner.'
    },
    {
      id: 'WH-MOV-1006',
      color: 'red',
      typeText: { id: 'Anomali Opname', en: 'Stocktake Anomaly' },
      ref: 'ANM-OPN-018',
      title: 'Selisih Negatif Opname Toko: Susu UHT 1L',
      qty: 'Selisih Fisik: -4 Pcs (-Rp 74.000)',
      location: 'Rak Display A-02 • Kategori: Rusak/Bocor',
      actor: 'Raka (Kepala Toko)',
      role: 'Kepala Toko • ID #USR-003',
      time: { id: '1 Jam Lalu', en: '1 hour ago' },
      notes: 'Disahkan langsung oleh Kepala Toko & masuk laporan kerugian.'
    }
  ];

  function filterMovementStream(color) {
    activeStreamFilter = color;

    // Update Chip Button Active Styles
    const chips = {
      all: document.getElementById('chipFilterAll'),
      green: document.getElementById('chipFilterGreen'),
      blue: document.getElementById('chipFilterBlue'),
      purple: document.getElementById('chipFilterPurple'),
      orange: document.getElementById('chipFilterOrange'),
      red: document.getElementById('chipFilterRed')
    };

    Object.keys(chips).forEach(k => {
      const chip = chips[k];
      if (!chip) return;
      if (k === color) {
        chip.className = 'stream-chip px-3 py-1.5 rounded-xl bg-sidebar text-white shadow-sm transition active:scale-95 font-bold';
      } else {
        chip.className = 'stream-chip px-3 py-1.5 rounded-xl bg-panelBg text-textMain hover:bg-[#EAE8F2] transition active:scale-95 font-semibold';
      }
    });

    renderMovementStream();
  }

  function renderMovementStream() {
    const container = document.getElementById('movementStreamContainer');
    if (!container) return;
    container.innerHTML = '';

    const filtered = activeStreamFilter === 'all'
      ? movementStreamData
      : movementStreamData.filter(item => item.color === activeStreamFilter);

    if (filtered.length === 0) {
      container.innerHTML = `
        <div class="py-8 text-center text-textMuted text-xs">
          ${currentLang === 'id' ? 'Tidak ada catatan pergerakan untuk filter ini.' : 'No movement records found for this filter.'}
        </div>
      `;
      return;
    }

    filtered.forEach(item => {
      const card = document.createElement('div');
      card.onclick = () => showMovementAuditToast(item);
      card.className = 'p-4 rounded-2xl bg-panelBg border border-[#ECEBF4] hover:bg-[#F2F1FA] cursor-pointer transition duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3';

      const typeLabel = item.typeText[currentLang] || item.typeText.id;
      const timeLabel = item.time[currentLang] || item.time.id;

      let badgeBg = 'bg-[#ECFDF5] text-[#065F46] border-[#A7F3D0]';
      let dotBg = 'bg-[#10B981]';
      if (item.color === 'blue') {
        badgeBg = 'bg-[#EFF6FF] text-[#1E40AF] border-[#BFDBFE]';
        dotBg = 'bg-[#3B82F6]';
      } else if (item.color === 'purple') {
        badgeBg = 'bg-[#F5F3FF] text-[#5B21B6] border-[#DDD6FE]';
        dotBg = 'bg-[#8B5CF6]';
      } else if (item.color === 'orange') {
        badgeBg = 'bg-[#FFFBEB] text-[#92400E] border-[#FDE68A]';
        dotBg = 'bg-[#F59E0B]';
      } else if (item.color === 'red') {
        badgeBg = 'bg-[#FEF2F2] text-[#991B1B] border-[#FECACA]';
        dotBg = 'bg-[#EF4444]';
      }

      card.innerHTML = `
        <div class="flex items-start gap-3">
          <span class="w-3 h-3 rounded-full ${dotBg} mt-1.5 shrink-0"></span>
          <div>
            <div class="flex flex-wrap items-center gap-2">
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border ${badgeBg}">${typeLabel}</span>
              <span class="font-mono text-xs font-bold text-sidebar">${item.ref}</span>
              <span class="text-xs font-bold text-textMain">• ${item.title}</span>
            </div>
            <p class="text-xs text-textMuted mt-1">
              <strong>${item.qty}</strong> • ${item.location}
            </p>
            <p class="text-[11px] text-textMuted mt-0.5">
              ${currentLang === 'id' ? 'Eksekutor:' : 'Operator:'} <span class="font-semibold text-textMain">${item.actor}</span> (${item.role})
            </p>
          </div>
        </div>
        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center shrink-0 border-t sm:border-t-0 pt-2 sm:pt-0 border-gray-200">
          <span class="text-xs font-bold text-textMain">${timeLabel}</span>
          <span class="text-[10px] font-semibold text-textMuted mt-0.5">${currentLang === 'id' ? 'Klik untuk audit' : 'Click for audit'}</span>
        </div>
      `;
      container.appendChild(card);
    });
  }

  function showMovementAuditToast(item) {
    const msg = `[AUDIT TRAIL] Ref: ${item.ref} | ${item.title} | ${item.qty} | Diotorisasi: ${item.actor} (${item.role}) | Catatan: ${item.notes}`;
    openToast(msg);
  }

  // ============================================================
  // 3. TOUCH-FRIENDLY QUICK ACTIONS (FASE 1 OPERATORS)
  // ============================================================
  function simulateScanQR() {
    const randomMemberId = Math.floor(1000 + Math.random() * 8999);
    openToast(i18n[currentLang].toast_qr_scanned);
  }

  function openEmergencyModal() {
    const newId = Math.floor(100 + Math.random() * 899);
    const newEntry = {
      id: `WH-MOV-${newId}`,
      color: 'orange',
      typeText: { id: 'Stok Emergency', en: 'Emergency Stock' },
      ref: `EMG-STK-0${newId}`,
      title: 'Input Darurat Toko: Bawang Merah Brebes',
      qty: '+25 Kg (Rp 875.000) [Foto Nota #NT-90]',
      location: 'Toko Sentral • Pembelian Cepat Pasar',
      actor: 'Maya (Staf Toko Kasir)',
      role: 'Staf Toko • ID #USR-006',
      time: { id: 'Baru Saja', en: 'Just now' },
      notes: 'Stok langsung tayang di POS Kasir tanpa approval.'
    };
    movementStreamData.unshift(newEntry);
    renderMovementStream();
    openToast(i18n[currentLang].toast_emergency_added);
  }

  function openNotificationModal() {
    openToast('Notifikasi Sistem: 1 Anomali Opname (Susu UHT), 1 Stok Emergency (Telur), 0 Overdue Piutang B2B.');
  }

  function openUserProfileInfo() {
    openToast('Akun Aktif: Barbara Wijaya (Owner Pasar) • Hak Akses: Penuh (Read-Only Audit & Keuangan)');
  }

  // ============================================================
  // 4. NAVIGATION, LIQUID CURVE ANIMATION & SUB-MENUS
  // ============================================================
  let currentModuleIndex = 0; // Default active module: Page 0
  let currentSubMenuIndex = 0;

  const moduleSubMenus = {
    0: [
      { label: { id: 'Ringkasan Utama', en: 'Executive Summary' }, desc: { id: 'Kesehatan Finansial & Feed 5-Warna', en: 'Financial Health & 5-Color Feed' } },
      { label: { id: 'Stream Masuk (Hijau)', en: 'Inbound Stream (Green)' }, desc: { id: 'Filter barang masuk normal & PO', en: 'Filter normal inbound & PO' } },
      { label: { id: 'Stream Transfer (Biru)', en: 'Internal Stream (Blue)' }, desc: { id: 'Filter perpindahan antar unit', en: 'Filter internal unit transfers' } },
      { label: { id: 'Stream Penjualan (Ungu)', en: 'Sales Stream (Purple)' }, desc: { id: 'Filter transaksi POS & B2B', en: 'Filter POS & B2B transactions' } },
      { label: { id: 'Stream Anomali (Merah)', en: 'Anomaly Stream (Red)' }, desc: { id: 'Filter selisih opname & void', en: 'Filter stocktake discrepancies & voids' } }
    ],
    1: [
      { label: { id: 'Penerimaan PO', en: 'PO Receiving' }, desc: { id: 'Bongkar muat suplayer & QC timbang', en: 'Supplier unloading & digital scale QC' } },
      { label: { id: 'Konversi Multi-Satuan', en: 'Multi-UOM Split' }, desc: { id: 'Bongkar karung ke satuan eceran', en: 'Breakdown sacks to retail base UOM' } },
      { label: { id: 'Distribusi Toko', en: 'Store Distribution' }, desc: { id: 'Transfer stok gudang ke rak toko', en: 'Transfer warehouse stock to shelves' } },
      { label: { id: 'Blind Opname Gudang', en: 'Warehouse Blind Count' }, desc: { id: 'Hitung fisik tanpa melihat angka sistem', en: 'Physical count without system numbers' } }
    ],
    2: [
      { label: { id: 'Kasir POS Ritel', en: 'Retail POS Register' }, desc: { id: 'Tawar harga dalam range & di atas floor price', en: 'Bargain within range & above floor price' } },
      { label: { id: 'Input Stok Emergency', en: 'Emergency Inbound' }, desc: { id: 'Input darurat barang habis (+Foto Nota)', en: 'Emergency out-of-stock input (+Receipt)' } },
      { label: { id: 'Validasi Kasir', en: 'Shift Cashier Validation' }, desc: { id: 'Buka/tutup laci & verifikasi selisih kas', en: 'Open/close drawer & cash discrepancy' } },
      { label: { id: 'Blind Opname Toko', en: 'Store Blind Count' }, desc: { id: 'Opname rak mandiri oleh staf toko', en: 'Independent shelf count by store staff' } }
    ],
    3: [
      { label: { id: 'Katalog B2B & Harga Tier', en: 'B2B Catalog & Tiers' }, desc: { id: 'Daftar harga khusus mitra grosir', en: 'Wholesale partner special price tiers' } },
      { label: { id: 'Plafon Kredit Mitra', en: 'Partner Credit Limits' }, desc: { id: 'Sistem kunci otomatis jika piutang tembus limit', en: 'Auto-block lock when debt exceeds limit' } },
      { label: { id: 'Tiket QR Pick-up', en: 'QR Pick-up Tickets' }, desc: { id: 'Ambil mandiri di Toko atau Gudang', en: 'Self pick-up at Store or Warehouse' } }
    ],
    4: [
      { label: { id: 'Semua Anomali', en: 'All Anomalies' }, desc: { id: 'Daftar penyimpangan warna merah & oranye', en: 'List of red and orange variances' } },
      { label: { id: 'Selisih Opname', en: 'Stocktake Variance' }, desc: { id: 'Susut alami, busuk, rusak, hilang', en: 'Natural shrinkage, spoilage, theft' } },
      { label: { id: 'Accountability Board', en: 'Accountability Board' }, desc: { id: 'Audit trail pertanggungjawaban akun', en: 'Account direct liability audit trail' } }
    ],
    5: [
      { label: { id: 'Master Produk & Satuan', en: 'Products & Multi-UOM' }, desc: { id: 'Fuzzy matching 75% pencegah produk ganda', en: '75% fuzzy matching anti-duplicate' } },
      { label: { id: 'Kartu Member QR', en: 'Member QR Cards' }, desc: { id: 'QR & Barcode pelanggan ritel & B2B', en: 'QR & Barcode for retail & B2B members' } },
      { label: { id: 'Parameter System Lock', en: 'System Lock Parameters' }, desc: { id: 'Batas floor price & plafon kredit', en: 'Floor price bounds & credit limit rules' } }
    ]
  };

  function renderSubMenus() {
    const pills = document.querySelectorAll('#desktopSubMenuContainer a');
    if (pills && pills.length > 0) {
      pills.forEach((p, idx) => {
        if (p.getAttribute('onclick') && p.getAttribute('onclick').includes('selectSubMenu')) {
          if (idx === currentSubMenuIndex) {
            p.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition whitespace-nowrap bg-white text-sidebar font-bold border border-[#E6E4F0] shadow-sm';
          } else {
            p.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition whitespace-nowrap text-textMuted hover:text-textMain hover:bg-white/60';
          }
        }
      });
    }

    const mobilePills = document.querySelectorAll('#mobileSubMenuList a');
    if (mobilePills && mobilePills.length > 0) {
      mobilePills.forEach((mBtn, idx) => {
        if (mBtn.getAttribute('onclick') && mBtn.getAttribute('onclick').includes('selectSubMenu')) {
          if (idx === currentSubMenuIndex) {
            mBtn.className = 'flex items-center justify-between w-full px-3 py-2 rounded-xl text-xs transition bg-panelBg text-sidebar font-bold border border-[#E6E4F0]';
          } else {
            mBtn.className = 'flex items-center justify-between w-full px-3 py-2 rounded-xl text-xs transition text-textMuted hover:text-textMain hover:bg-[#FAF9FD]';
          }
        }
      });
    }

    const mobileLabel = document.getElementById('mobileActiveSubMenuLabel');
    if (mobileLabel && moduleSubMenus[currentModuleIndex] && moduleSubMenus[currentModuleIndex][currentSubMenuIndex]) {
      const activeObj = moduleSubMenus[currentModuleIndex][currentSubMenuIndex];
      mobileLabel.innerText = activeObj.label[currentLang] || activeObj.label.id;
    }
  }

  function toggleMobileSubMenu(e) {
    if (e) e.stopPropagation();
    const dropdown = document.getElementById('mobileSubMenuDropdown');
    const chevron = document.getElementById('mobileMenuChevron');
    if (!dropdown) return;

    const isHidden = dropdown.classList.contains('hidden');
    if (isHidden) {
      dropdown.classList.remove('hidden');
      requestAnimationFrame(() => {
        dropdown.classList.remove('scale-95', 'opacity-0');
        dropdown.classList.add('scale-100', 'opacity-100');
      });
      if (chevron) chevron.classList.add('rotate-180');
    } else {
      closeMobileSubMenu();
    }
  }

  function closeMobileSubMenu() {
    const dropdown = document.getElementById('mobileSubMenuDropdown');
    const chevron = document.getElementById('mobileMenuChevron');
    if (!dropdown || dropdown.classList.contains('hidden')) return;

    dropdown.classList.remove('scale-100', 'opacity-100');
    dropdown.classList.add('scale-95', 'opacity-0');
    if (chevron) chevron.classList.remove('rotate-180');
    setTimeout(() => {
      dropdown.classList.add('hidden');
    }, 200);
  }

  // Desktop Overflow Dropdown Handling
  function toggleDesktopOverflowMenu(e) {
    if (e) e.stopPropagation();
    const dropdown = document.getElementById('desktopOverflowDropdown');
    const chevron = document.getElementById('desktopOverflowChevron');
    if (!dropdown) return;

    const isHidden = dropdown.classList.contains('hidden');
    if (isHidden) {
      dropdown.classList.remove('hidden');
      requestAnimationFrame(() => {
        dropdown.classList.remove('scale-95', 'opacity-0');
        dropdown.classList.add('scale-100', 'opacity-100');
      });
      if (chevron) chevron.classList.add('rotate-180');
    } else {
      closeDesktopOverflowMenu();
    }
  }

  function closeDesktopOverflowMenu() {
    const dropdown = document.getElementById('desktopOverflowDropdown');
    const chevron = document.getElementById('desktopOverflowChevron');
    if (!dropdown || dropdown.classList.contains('hidden')) return;

    dropdown.classList.remove('scale-100', 'opacity-100');
    dropdown.classList.add('scale-95', 'opacity-0');
    if (chevron) chevron.classList.remove('rotate-180');
    setTimeout(() => {
      dropdown.classList.add('hidden');
    }, 200);
  }

  function calculateDesktopSubMenuOverflow() {
    const wrapper = document.getElementById('desktopSubMenuWrapper');
    const container = document.getElementById('desktopSubMenuContainer');
    const overflowWrapper = document.getElementById('desktopOverflowWrapper');
    const overflowList = document.getElementById('desktopOverflowList');
    const overflowBadge = document.getElementById('desktopOverflowCountBadge');
    const overflowBtn = document.getElementById('desktopOverflowBtn');

    if (!wrapper || !container || !overflowWrapper || !overflowList) return;
    if (window.innerWidth < 768) return;

    const items = container.querySelectorAll('.submenu-desktop-item');
    if (items.length === 0) return;

    // Temporarily unhide all items and hide overflow button to measure natural sizes
    overflowWrapper.classList.add('hidden');
    items.forEach(el => el.classList.remove('hidden'));

    // Determine max available width for left container inside header
    const header = document.querySelector('header');
    const rightHeader = header ? header.querySelector('.flex.items-center.justify-end') : null;
    let maxAllowedWidth = 600;

    if (header && rightHeader) {
      maxAllowedWidth = Math.max(160, header.clientWidth - rightHeader.getBoundingClientRect().width - 36);
    }

    let totalWidth = 0;
    items.forEach(el => {
      totalWidth += el.offsetWidth + 4;
    });

    if (totalWidth <= maxAllowedWidth) {
      overflowWrapper.classList.add('hidden');
      return;
    }

    // Allocate space reserving ~52px for the overflow arrow button
    const targetWidth = Math.max(120, maxAllowedWidth - 52);
    let accumulatedWidth = 0;
    let hiddenItems = [];

    items.forEach((el, idx) => {
      const itemWidth = el.offsetWidth + 4;
      if (accumulatedWidth + itemWidth <= targetWidth && hiddenItems.length === 0) {
        accumulatedWidth += itemWidth;
        el.classList.remove('hidden');
      } else {
        el.classList.add('hidden');
        hiddenItems.push(el);
      }
    });

    if (hiddenItems.length > 0) {
      overflowWrapper.classList.remove('hidden');
      overflowList.innerHTML = '';
      let hasActiveInOverflow = false;

      hiddenItems.forEach(el => {
        const isActive = el.getAttribute('data-submenu-active') === '1' || el.classList.contains('bg-white');
        if (isActive) hasActiveInOverflow = true;

        const clone = document.createElement('a');
        clone.href = el.href;
        if (el.getAttribute('onclick')) clone.setAttribute('onclick', el.getAttribute('onclick'));
        clone.className = isActive
          ? 'flex items-center justify-between w-full px-3 py-2 rounded-xl text-xs transition bg-panelBg text-sidebar font-bold border border-[#E6E4F0]'
          : 'flex items-center justify-between w-full px-3 py-2 rounded-xl text-xs transition text-textMuted hover:text-textMain hover:bg-[#FAF9FD]';

        const iconEl = el.querySelector('svg');
        const textSpan = el.querySelector('span');
        const labelText = textSpan ? textSpan.innerText : '';
        const iconHtml = iconEl ? iconEl.outerHTML : '';

        clone.innerHTML = `
          <div class="flex items-center gap-2">
            ${iconHtml}
            <span>${labelText}</span>
          </div>
          ${isActive ? '<span class="w-2 h-2 rounded-full bg-brandOrange"></span>' : ''}
        `;
        clone.addEventListener('click', () => {
          closeDesktopOverflowMenu();
        });
        overflowList.appendChild(clone);
      });

      if (overflowBadge) {
        overflowBadge.innerText = `+${hiddenItems.length}`;
      }

      if (hasActiveInOverflow) {
        overflowBtn.className = 'flex items-center gap-1 px-2.5 py-1.5 rounded-lg transition bg-sidebar text-white font-bold border border-sidebar shadow-sm';
        const chevron = document.getElementById('desktopOverflowChevron');
        if (chevron) chevron.className = 'w-3.5 h-3.5 text-white transition-transform duration-200';
      } else {
        overflowBtn.className = 'flex items-center gap-1 px-2.5 py-1.5 rounded-lg transition bg-white text-sidebar font-bold border border-[#E6E4F0] shadow-sm hover:bg-[#FAF9FD]';
        const chevron = document.getElementById('desktopOverflowChevron');
        if (chevron) chevron.className = 'w-3.5 h-3.5 text-sidebar transition-transform duration-200';
      }
    } else {
      overflowWrapper.classList.add('hidden');
    }
  }

  window.calculateDesktopSubMenuOverflow = calculateDesktopSubMenuOverflow;

  document.addEventListener('click', (e) => {
    const mobileWrapper = document.getElementById('mobileMenuWrapper');
    if (mobileWrapper && !mobileWrapper.contains(e.target)) {
      closeMobileSubMenu();
    }
    const overflowWrapper = document.getElementById('desktopOverflowWrapper');
    if (overflowWrapper && !overflowWrapper.contains(e.target)) {
      closeDesktopOverflowMenu();
    }
  });

  function selectSubMenu(subIdx) {
    currentSubMenuIndex = subIdx;
    renderSubMenus();

    const subObj = moduleSubMenus[currentModuleIndex] ? moduleSubMenus[currentModuleIndex][subIdx] : null;
    const contextBox = document.getElementById('subMenuContextBox');
    const contextText = document.getElementById('subMenuContextText');

    if (contextBox && contextText) {
      if (subIdx === 0) {
        contextBox.classList.add('hidden');
        contextBox.classList.remove('flex');
      } else {
        contextBox.classList.remove('hidden');
        contextBox.classList.add('flex');
        if (subObj) {
          const lTxt = subObj.label[currentLang] || subObj.label.id;
          const dTxt = subObj.desc[currentLang] || subObj.desc.id;
          contextText.innerText = `${lTxt} — ${dTxt}`;
        }
      }
    }

    // Special quick filter link if on Module 0 (Main Dashboard)
    if (currentModuleIndex === 0) {
      if (subIdx === 0) filterMovementStream('all');
      else if (subIdx === 1) filterMovementStream('green');
      else if (subIdx === 2) filterMovementStream('blue');
      else if (subIdx === 3) filterMovementStream('purple');
      else if (subIdx === 4) filterMovementStream('red');
    }
  }

  function switchPage(index) {
    currentModuleIndex = index;
    currentSubMenuIndex = 0;

    // Update desktop sidebar buttons
    const buttons = document.querySelectorAll('#sidebarNav .sidebar-btn');
    buttons.forEach((btn, idx) => {
      if (idx === index) btn.classList.add('active');
      else btn.classList.remove('active');
    });

    // Update mobile bottom nav buttons
    const mobileButtons = document.querySelectorAll('#mobileNav .mobile-nav-btn');
    mobileButtons.forEach((btn, idx) => {
      if (idx === index) btn.classList.add('active');
      else btn.classList.remove('active');
    });

    // Animate desktop liquid curve indicator (68px step)
    const indicator = document.getElementById('navIndicator');
    if (indicator) {
      const stepY = index * 68;
      indicator.style.transform = `translateY(${stepY}px)`;
    }

    // Animate mobile liquid curve indicator (horizontal step)
    const mobileIndicator = document.getElementById('mobileNavIndicator');
    if (mobileIndicator) {
      mobileIndicator.style.transform = `translateX(calc(${index} * 100%))`;
    }

    // Show selected page container and hide others
    for (let i = 0; i < 6; i++) {
      const pageEl = document.getElementById(`page-${i}`);
      if (pageEl) {
        if (i === index) {
          pageEl.classList.remove('hidden');
          pageEl.style.animation = 'none';
          pageEl.offsetHeight; // trigger reflow
          pageEl.style.animation = null;
        } else {
          pageEl.classList.add('hidden');
        }
      }
    }

    selectSubMenu(0);

    const scroller = document.getElementById('scrollableWorkspace');
    if (scroller) scroller.scrollTop = 0;
  }

  // ============================================================
  // 5. RIGHT PANEL FUNCTIONS (AUTOMATION TOGGLES & CHARTS)
  // ============================================================
  const fnStates = [true, true, true, false];

  function toggleFunctionCard(index, activeHex) {
    fnStates[index] = !fnStates[index];
    const isOn = fnStates[index];
    const dot = document.getElementById(`fnDot${index}`);
    const top = document.getElementById(`fnTop${index}`);

    if (!dot || !top) return;

    if (isOn) {
      dot.style.transform = 'translateX(14px)';
      dot.style.backgroundColor = activeHex;
      top.style.backgroundColor = activeHex;
      top.style.color = '#FFFFFF';
    } else {
      dot.style.transform = 'translateX(0px)';
      dot.style.backgroundColor = '#9D97C4';
      top.style.backgroundColor = '#E6E6F6';
      top.style.color = '#9A96B3';
    }
  }

  const barValues = [74, 48, 60, 82, 58, 42, 50];
  const barLabels = ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'];

  function renderBarChart() {
    const container = document.getElementById('barChartView');
    if (!container) return;
    container.innerHTML = '';

    barValues.forEach((val, idx) => {
      const isCurrent = idx === 3; // Thursday
      const col = document.createElement('div');
      col.onclick = () => openToast(`Throughput Hari ${barLabels[idx]}: ${val}% Kapasitas Terpenuhi.`);
      col.className = 'flex flex-col items-center gap-3 cursor-pointer group h-full justify-end';

      const barColor = isCurrent ? 'bg-brandOrange' : 'bg-brandCyan group-hover:bg-sidebar';
      const labelColor = isCurrent ? 'text-brandOrange font-extrabold' : 'text-textMuted font-semibold';

      col.innerHTML = `
        <div class="w-1.5 h-28 bg-[#F0F1F7] rounded-full flex items-end overflow-hidden">
          <div class="bar-fill w-full rounded-full ${barColor}" style="height: ${val}%"></div>
        </div>
        <span class="text-[10px] tracking-wider ${labelColor}">${barLabels[idx]}</span>
      `;
      container.appendChild(col);
    });
  }

  function setChartMode(mode) {
    const barView = document.getElementById('barChartView');
    const lineView = document.getElementById('lineChartView');
    const btnBar = document.getElementById('btnBarMode');
    const btnLine = document.getElementById('btnLineMode');

    if (!barView || !lineView || !btnBar || !btnLine) return;

    if (mode === 'bar') {
      barView.classList.remove('hidden');
      lineView.classList.add('hidden');
      btnBar.className = 'w-8 h-7 rounded-lg bg-brandOrange text-white flex items-center justify-center transition';
      btnLine.className = 'w-8 h-7 rounded-lg text-textMuted hover:text-textMain flex items-center justify-center transition';
    } else {
      barView.classList.add('hidden');
      lineView.classList.remove('hidden');
      btnLine.className = 'w-8 h-7 rounded-lg bg-brandOrange text-white flex items-center justify-center transition';
      btnBar.className = 'w-8 h-7 rounded-lg text-textMuted hover:text-textMain flex items-center justify-center transition';
    }
  }

  // ============================================================
  // 6. TOAST NOTIFICATION ENGINE
  // ============================================================
  let toastTimer = null;
  function openToast(msg) {
    const box = document.getElementById('toastBox');
    const msgEl = document.getElementById('toastMessage');
    if (!box || !msgEl) return;
    msgEl.innerText = msg;
    box.classList.remove('translate-y-24', 'opacity-0');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
      box.classList.add('translate-y-24', 'opacity-0');
    }, 3800);
  }

  // ============================================================
  // 7. INITIALIZATION ON DOM READY
  // ============================================================
  window.addEventListener('DOMContentLoaded', function () {
    // Initialize language from storage
    const savedLang = localStorage.getItem('pasar_lang') || 'id';
    setAppLanguage(savedLang);

    // Initial render
    renderSubMenus();
    calculateDesktopSubMenuOverflow();
    renderMovementStream();
    renderBarChart();
  });

  let resizeTimer = null;
  window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(calculateDesktopSubMenuOverflow, 50);
  });
</script>
