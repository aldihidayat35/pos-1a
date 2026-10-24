<!-- ============================================================
     CONTEXTUAL RIGHT PANEL: OPERASIONAL GUDANG PUSAT
     ============================================================ -->
@php
  $currentUser = $user ?? auth()->user();
@endphp
<div class="space-y-6">

  <!-- CARD 1: AKSI CEPAT LOGISTIK GUDANG -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-display font-bold text-base text-textMain tracking-tight">Aksi Logistik Gudang</h2>
        <p class="text-[11px] text-textMuted mt-0.5">Pengadaan & Alur Masuk Barang</p>
      </div>
      <span class="w-2.5 h-2.5 rounded-full bg-brandOrange animate-pulse" title="Gudang Operasional"></span>
    </div>

    <div class="space-y-2.5 pt-1">
      <!-- 1. BUAT PO SUPLAYER -->
      @if($currentUser?->hasPermission('purchasing.po') || $currentUser?->hasPermission('inventory.manage') || $currentUser?->isSuperAdmin())
        <a href="{{ route('warehouse.po.create') }}" 
           class="w-full p-3.5 bg-sidebar hover:bg-sidebar/95 text-white rounded-2xl transition duration-200 flex items-center justify-between group shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition shrink-0">
              <svg class="w-5 h-5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
              </svg>
            </div>
            <div class="text-left">
              <p class="text-xs font-bold leading-tight">+ Buat PO Suplayer</p>
              <p class="text-[10px] text-white/70 mt-0.5">Purchase Order Inbound</p>
            </div>
          </div>
          <span class="px-2 py-0.5 rounded-lg bg-white/15 text-[10px] font-bold text-brandOrange">PO Baru</span>
        </a>
      @endif

      <!-- 2. TRANSFER STOK GUDANG KE TOKO -->
      @if($currentUser?->hasPermission('inventory.transfer') || $currentUser?->isSuperAdmin())
        <a href="{{ route('warehouse.transfers.create') }}" 
           class="w-full p-3 bg-white hover:bg-panelBg text-sidebar font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4] shadow-sm">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
              </svg>
            </div>
            <div class="text-left">
              <p class="text-xs font-bold leading-tight text-textMain">Transfer Stok ke Toko</p>
              <p class="text-[10px] text-textMuted mt-0.5 font-normal">Kirim Barang ke Gerai Ritel</p>
            </div>
          </div>
          <svg class="w-4 h-4 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      @endif

      <!-- 3. INPUT STOK OPNAME FISIK -->
      @if($currentUser?->hasPermission('inventory.opname') || $currentUser?->isSuperAdmin())
        <a href="{{ route('opname.create') }}" 
           class="w-full p-3 bg-white hover:bg-panelBg text-sidebar font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4] shadow-sm">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-brandOrange flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
            </div>
            <div class="text-left">
              <p class="text-xs font-bold leading-tight text-textMain">Input Stok Opname</p>
              <p class="text-[10px] text-textMuted mt-0.5 font-normal">Pencocokan Fisik & Selisih</p>
            </div>
          </div>
          <svg class="w-4 h-4 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      @endif

      <!-- 4. KARTU STOK & AUDIT LEDGER -->
      <a href="{{ route('warehouse.ledger.index') }}" 
         class="w-full p-3 bg-white hover:bg-panelBg text-sidebar font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4] shadow-sm">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-xs font-bold leading-tight text-textMain">Kartu Stok & Audit Ledger</p>
            <p class="text-[10px] text-textMuted mt-0.5 font-normal">Riwayat Mutasi & HPP Masuk</p>
          </div>
        </div>
        <svg class="w-4 h-4 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
      </a>
    </div>
  </div>

  <!-- CARD 2: RINGKASAN METRIK GUDANG PUSAT -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-base text-textMain tracking-tight">Kapasitas & Valuasi</h2>
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider">Gudang</span>
    </div>

    <div class="space-y-3">
      <!-- Total SKU -->
      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
        <div>
          <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">SKU Aktif Tersimpan</span>
          <p class="text-2xl font-black text-sidebar font-mono mt-0.5">{{ $stats['total_sku'] ?? 0 }}</p>
          <p class="text-[11px] text-textMuted mt-0.5">Katalog produk pasar terdaftar</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-sidebar/10 text-sidebar flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
          </svg>
        </div>
      </div>

      <!-- PO Menunggu Terima -->
      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
        <div>
          <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">PO Menunggu Terima</span>
          <div class="flex items-baseline gap-2 mt-0.5">
            <span class="text-2xl font-black text-brandOrange font-mono">{{ $stats['pending_po_count'] ?? 0 }}</span>
            <span class="text-[11px] font-bold text-textMuted">Pesanan</span>
          </div>
          <p class="text-[11px] text-textMuted mt-0.5">Barang inbound dari suplayer</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-amber-50 text-brandOrange flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>
      </div>

      <!-- Valuasi Aset HPP -->
      @if(isset($canViewHpp) && $canViewHpp)
        <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] flex items-center justify-between shadow-xs">
          <div>
            <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Estimasi Nilai Stok Gudang</span>
            <p class="text-xl font-black text-emerald-600 font-mono mt-0.5">
              Rp {{ number_format($stats['total_valuation'] ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-textMuted mt-0.5">Akumulasi HPP saldo on-hand</p>
          </div>
          <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      @endif
    </div>
  </div>

</div>
