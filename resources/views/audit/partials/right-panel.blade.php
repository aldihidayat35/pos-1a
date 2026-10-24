<!-- ============================================================
     CONTEXTUAL RIGHT PANEL: AUDIT TRAIL & NOTIFIKASI ANOMALI
     ============================================================ -->
<div class="space-y-6">

  <!-- CARD 1: FILTER CEPAT TINGKAT ANOMALI -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-display font-bold text-base text-textMain tracking-tight">Filter Anomali</h2>
        <p class="text-[11px] text-textMuted mt-0.5">Klasifikasi Tingkat Risiko</p>
      </div>
      <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse" title="Sistem Audit Aktif"></span>
    </div>

    <div class="space-y-2.5 pt-1">
      <!-- 1. SEMUA ANOMALI -->
      <a href="{{ route('audit.index') }}" 
         class="w-full p-3 {{ !request('severity') ? 'bg-sidebar text-white shadow-sm' : 'bg-white hover:bg-panelBg text-sidebar' }} font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4]">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl {{ !request('severity') ? 'bg-white/15 text-white' : 'bg-indigo-50 text-sidebar' }} flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-xs font-bold leading-tight">Semua Catatan Audit</p>
            <p class="text-[10px] {{ !request('severity') ? 'text-white/70' : 'text-textMuted' }} mt-0.5 font-normal">Seluruh mutasi tercatat</p>
          </div>
        </div>
        <span class="px-2 py-0.5 rounded-full {{ !request('severity') ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }} text-[10px] font-bold">
          {{ $counts['ALL'] ?? 0 }}
        </span>
      </a>

      <!-- 2. ANOMALI ORANYE (PERINGATAN) -->
      <a href="{{ route('audit.index', ['severity' => 'ORANGE']) }}" 
         class="w-full p-3 {{ request('severity') === 'ORANGE' ? 'bg-brandOrange text-white shadow-sm' : 'bg-white hover:bg-panelBg text-sidebar' }} font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4]">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl {{ request('severity') === 'ORANGE' ? 'bg-white/20 text-white' : 'bg-amber-50 text-brandOrange' }} flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-xs font-bold leading-tight">Anomali Oranye</p>
            <p class="text-[10px] {{ request('severity') === 'ORANGE' ? 'text-white/80' : 'text-textMuted' }} mt-0.5 font-normal">Belanja Darurat & Toleransi</p>
          </div>
        </div>
        <span class="px-2 py-0.5 rounded-full {{ request('severity') === 'ORANGE' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }} text-[10px] font-bold">
          {{ $counts['ORANGE'] ?? 0 }}
        </span>
      </a>

      <!-- 3. ANOMALI MERAH (KRITIS) -->
      <a href="{{ route('audit.index', ['severity' => 'RED']) }}" 
         class="w-full p-3 {{ request('severity') === 'RED' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white hover:bg-panelBg text-sidebar' }} font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4]">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl {{ request('severity') === 'RED' ? 'bg-white/20 text-white' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-xs font-bold leading-tight">Anomali Merah Kritis</p>
            <p class="text-[10px] {{ request('severity') === 'RED' ? 'text-white/80' : 'text-textMuted' }} mt-0.5 font-normal">Selisih Minus, Void, & Plafon</p>
          </div>
        </div>
        <span class="px-2 py-0.5 rounded-full {{ request('severity') === 'RED' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-800' }} text-[10px] font-bold">
          {{ $counts['RED'] ?? 0 }}
        </span>
      </a>
    </div>
  </div>

  <!-- CARD 2: PRINSIP ZERO-BOTTLENECK -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-base text-textMain tracking-tight">Kendali Mutu RBAC</h2>
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider">Tanpa Approval</span>
    </div>

    <div class="space-y-3">
      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] space-y-1.5 shadow-xs">
        <div class="flex items-center gap-2 text-sidebar font-bold text-xs">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span>Pertanggungjawaban Akun</span>
        </div>
        <p class="text-[11px] text-textMuted leading-relaxed">
          Tidak ada bottleneck birokrasi persetujuan. Setiap aksi operasional langsung dieksekusi dan mengikat identitas <strong class="text-sidebar">user_id</strong> serta peran secara permanen.
        </p>
      </div>

      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] space-y-1.5 shadow-xs">
        <div class="flex items-center gap-2 text-sidebar font-bold text-xs">
          <span class="w-2 h-2 rounded-full bg-blue-500"></span>
          <span>Floor Price & Plafon Lock</span>
        </div>
        <p class="text-[11px] text-textMuted leading-relaxed">
          Sistem otomatis mengunci diskon di bawah harga dasar kasir dan menolak pesanan kredit B2B yang melebihi batas plafon.
        </p>
      </div>
    </div>
  </div>

  <!-- CARD 3: PINTASAN REKONSILIASI -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-3 shadow-sm">
    <h2 class="font-display font-bold text-base text-textMain tracking-tight">Pintasan Investigasi</h2>
    <div class="space-y-2">
      <a href="{{ route('warehouse.ledger.index') }}" class="w-full p-2.5 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-xl transition flex items-center justify-between border border-[#ECEBF4]">
        <span>Buka Kartu Stok & Audit Ledger</span>
        <svg class="w-4 h-4 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
      </a>
      <a href="{{ route('opname.create') }}" class="w-full p-2.5 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-xl transition flex items-center justify-between border border-[#ECEBF4]">
        <span>Formulir Stok Opname Fisik</span>
        <svg class="w-4 h-4 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
      </a>
    </div>
  </div>

</div>
