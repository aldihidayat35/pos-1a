<!-- ============================================================
     CONTEXTUAL RIGHT PANEL: DASBOR UTAMA & EKSEKUTIF
     ============================================================ -->
<div class="space-y-6">

  <!-- CARD 1: PINTASAN AKSI EKSEKUTIF -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-display font-bold text-base text-textMain tracking-tight">Pintasan Cepat</h2>
        <p class="text-[11px] text-textMuted mt-0.5">Akses Langsung Seluruh Modul</p>
      </div>
      <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse" title="Sistem Real-Time"></span>
    </div>

    <div class="space-y-2.5 pt-1">
      <!-- 1. TERMINAL KASIR POS -->
      <a href="{{ route('store.pos.terminal') }}" 
         class="w-full p-3.5 bg-sidebar hover:bg-sidebar/95 text-white rounded-2xl transition duration-200 flex items-center justify-between group shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition shrink-0">
            <svg class="w-5 h-5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-xs font-bold leading-tight">Terminal Kasir POS</p>
            <p class="text-[10px] text-white/70 mt-0.5">Penjualan Ritel Toko</p>
          </div>
        </div>
        <span class="px-2 py-0.5 rounded-lg bg-white/15 text-[10px] font-mono font-bold text-brandOrange">Kasir</span>
      </a>

      <!-- 2. SCAN QR TIKET PICKUP / MEMBER -->
      <a href="{{ route('b2b.pickup.scan') }}" 
         class="w-full p-3 bg-white hover:bg-panelBg text-sidebar font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4] shadow-sm">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-xs font-bold leading-tight text-textMain">Scan QR Member / Tiket</p>
            <p class="text-[10px] text-textMuted mt-0.5 font-normal">Validasi Pengambilan B2B</p>
          </div>
        </div>
        <svg class="w-4 h-4 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
      </a>

      <!-- 3. + BELANJA EMERGENCY INSTAN -->
      <a href="{{ route('store.emergency.create') }}" 
         class="w-full p-3 bg-white hover:bg-panelBg text-sidebar font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4] shadow-sm">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-amber-50 text-brandOrange flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-xs font-bold leading-tight text-textMain">+ Belanja Emergency</p>
            <p class="text-[10px] text-textMuted mt-0.5 font-normal">Darurat Toko (+Nota Foto)</p>
          </div>
        </div>
        <svg class="w-4 h-4 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
      </a>

      <!-- 4. FEED PERGERAKAN 5-WARNA & ANOMALI -->
      <a href="{{ route('audit.index') }}" 
         class="w-full p-3 bg-white hover:bg-panelBg text-sidebar font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4] shadow-sm">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-xs font-bold leading-tight text-textMain">Audit Trail & Anomali</p>
            <p class="text-[10px] text-textMuted mt-0.5 font-normal">Log Pelanggaran & Selisih</p>
          </div>
        </div>
        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
      </a>
    </div>
  </div>

  <!-- CARD 2: RINGKASAN OMZET & KESEHATAN KEUANGAN -->
  @if(isset($financialSummary))
    <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
      <div class="flex items-center justify-between">
        <h2 class="font-display font-bold text-base text-textMain tracking-tight">Finansial Hari Ini</h2>
        <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider">Eksekutif</span>
      </div>

      <div class="space-y-3">
        <!-- Total Omzet -->
        <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] space-y-1 shadow-xs">
          <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Total Omzet Gabungan</span>
          <p class="text-2xl font-black text-sidebar font-mono">
            Rp {{ number_format($financialSummary['total_revenue'] ?? 0, 0, ',', '.') }}
          </p>
          <div class="flex items-center justify-between text-[11px] text-textMuted pt-1 border-t border-gray-100 mt-2">
            <span>Ritel Toko: <strong class="text-textMain font-mono">Rp {{ number_format($financialSummary['retail_revenue'] ?? 0, 0, ',', '.') }}</strong></span>
            <span>B2B: <strong class="text-textMain font-mono">Rp {{ number_format($financialSummary['b2b_revenue'] ?? 0, 0, ',', '.') }}</strong></span>
          </div>
        </div>

        <!-- Laba Kotor & Margin -->
        <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
          <div>
            <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Laba Kotor Real-time</span>
            <p class="text-xl font-black text-emerald-600 font-mono mt-0.5">
              Rp {{ number_format($financialSummary['gross_profit'] ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-textMuted mt-0.5">
              Margin Real-time: <strong class="text-emerald-700 font-bold">+{{ number_format($financialSummary['margin_percentage'] ?? 0, 1) }}%</strong>
            </p>
          </div>
          <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
          </div>
        </div>

        <!-- Nilai Aset Stok -->
        <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
          <div>
            <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Total Nilai Aset Stok</span>
            <p class="text-lg font-black text-blue-600 font-mono mt-0.5">
              Rp {{ number_format($financialSummary['total_stock_value'] ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-textMuted mt-0.5">Gudang 75% • Toko 25%</p>
          </div>
          <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
        </div>
      </div>
    </div>
  @endif

</div>
