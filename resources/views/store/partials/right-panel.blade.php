<!-- ============================================================
     CONTEXTUAL RIGHT PANEL: OPERASIONAL TOKO & KASIR POS
     ============================================================ -->
@php
  $currentUser = $user ?? auth()->user();
  $currentShift = $stats['active_shift'] ?? $activeShift ?? null;
@endphp
<div class="space-y-6">

  <!-- CARD 1: AKSI CEPAT OPERASIONAL TOKO -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-display font-bold text-base text-textMain tracking-tight">Aksi Cepat Toko</h2>
        <p class="text-[11px] text-textMuted mt-0.5">Operasional Kasir & Belanja Darurat</p>
      </div>
      <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse" title="Terminal Toko Siap"></span>
    </div>

    <div class="space-y-2.5 pt-1">
      <!-- 1. BUKA TERMINAL KASIR POS (PRIMARY CALL TO ACTION) -->
      @if($currentUser?->hasPermission('pos.access') || $currentUser?->isSuperAdmin())
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
              <p class="text-[10px] text-white/70 mt-0.5">Penjualan Cepat & Barcode</p>
            </div>
          </div>
          <span class="px-2 py-0.5 rounded-lg bg-white/15 text-[10px] font-mono font-bold text-brandOrange">F2 / F9</span>
        </a>
      @endif

      <!-- 2. + INPUT BELANJA EMERGENCY (ORANGE HIGHLIGHT) -->
      @if($currentUser?->hasPermission('emergency.input') || $currentUser?->isSuperAdmin())
        <a href="{{ route('store.emergency.create') }}" 
           class="w-full p-3.5 bg-brandOrange hover:bg-brandOrange/90 text-white rounded-2xl transition duration-200 flex items-center justify-between group shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center transition shrink-0">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
              </svg>
            </div>
            <div class="text-left">
              <p class="text-xs font-bold leading-tight">+ Belanja Emergency</p>
              <p class="text-[10px] text-white/80 mt-0.5">Beli Darurat (+Foto Nota)</p>
            </div>
          </div>
          <span class="px-2 py-0.5 rounded-lg bg-white/20 text-[10px] font-bold text-white uppercase tracking-wider">Instan</span>
        </a>
      @endif

      <!-- 3. KELOLA SHIFT KASIR & LACI UANG -->
      @if($currentUser?->hasPermission('pos.cash_shift') || $currentUser?->isSuperAdmin())
        <a href="{{ route('store.shifts.index') }}" 
           class="w-full p-3 bg-white hover:bg-panelBg text-sidebar font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4] shadow-sm">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-sidebar flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </div>
            <div class="text-left">
              <p class="text-xs font-bold leading-tight text-textMain">Shift Laci Kasir</p>
              <p class="text-[10px] text-textMuted mt-0.5 font-normal">Rekonsiliasi Modal Awal & Akhir</p>
            </div>
          </div>
          <svg class="w-4 h-4 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      @endif

      <!-- 4. DAFTAR TRANSFER MASUK TOKO -->
      <a href="{{ route('store.transfers.index') }}" 
         class="w-full p-3 bg-white hover:bg-panelBg text-sidebar font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4] shadow-sm">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-xs font-bold leading-tight text-textMain">Transfer Masuk Gudang</p>
            <p class="text-[10px] text-textMuted mt-0.5 font-normal">Penerimaan Kiriman Transit</p>
          </div>
        </div>
        @if(($stats['incoming_transfers_count'] ?? 0) > 0)
          <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold">
            {{ $stats['incoming_transfers_count'] }}
          </span>
        @else
          <span class="text-xs text-textMuted font-normal">0</span>
        @endif
      </a>
    </div>
  </div>

  <!-- CARD 2: RINGKASAN OPERASIONAL & METRIK TOKO -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-base text-textMain tracking-tight">Statistik Toko</h2>
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider">Real-time</span>
    </div>

    <div class="space-y-3">
      <!-- Metrik 1: Kiriman Transit Gudang -->
      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
        <div>
          <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Transfer Dalam Transit</span>
          <p class="text-2xl font-black text-blue-600 font-mono mt-0.5">{{ $stats['incoming_transfers_count'] ?? 0 }}</p>
          <p class="text-[11px] text-textMuted mt-0.5">Menuju toko ini dari gudang</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
      </div>

      <!-- Metrik 2: Belanja Emergency Bulan Ini -->
      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
        <div>
          <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Belanja Emergency Bulan Ini</span>
          <div class="flex items-baseline gap-2 mt-0.5">
            <span class="text-2xl font-black text-brandOrange font-mono">{{ $stats['emergency_purchases_count'] ?? 0 }}</span>
            <span class="text-[11px] font-bold text-textMuted">Kali</span>
          </div>
          <p class="text-[11px] text-textMuted mt-0.5 font-medium">
            Total: <strong class="text-sidebar font-mono font-bold">Rp {{ number_format($stats['total_emergency_spent'] ?? 0, 0, ',', '.') }}</strong>
          </p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-brandOrange flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
          </svg>
        </div>
      </div>

      <!-- Metrik 3: Status Shift Kasir Aktif -->
      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] space-y-2 shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Status Laci Kasir</span>
          @if($currentShift)
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              Shift Terbuka
            </span>
          @else
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
              Shift Tertutup
            </span>
          @endif
        </div>

        @if($currentShift)
          <div class="pt-1 text-xs space-y-1">
            <p class="text-textMain font-bold flex items-center justify-between">
              <span class="text-textMuted font-normal">Kasir:</span>
              <span>{{ $currentShift->cashier?->name ?? 'Kasir Bertugas' }}</span>
            </p>
            <p class="text-textMain font-bold flex items-center justify-between">
              <span class="text-textMuted font-normal">Modal Awal:</span>
              <span class="font-mono text-sidebar">Rp {{ number_format($currentShift->opening_cash ?? 0, 0, ',', '.') }}</span>
            </p>
            <p class="text-[10px] text-textMuted flex items-center justify-between">
              <span>Jam Buka:</span>
              <span>{{ $currentShift->opened_at ? \Carbon\Carbon::parse($currentShift->opened_at)->format('H:i') . ' WIB' : '-' }}</span>
            </p>
          </div>
        @else
          <p class="text-[11px] text-textMuted pt-1">
            Belum ada sesi shift kasir yang dibuka hari ini. Buka shift di menu Shift Laci sebelum bertransaksi.
          </p>
        @endif
      </div>
    </div>
  </div>

</div>
