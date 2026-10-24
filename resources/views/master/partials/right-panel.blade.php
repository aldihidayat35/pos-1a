<!-- ============================================================
     CONTEXTUAL RIGHT PANEL: MASTER DATA & ATURAN HARGA
     ============================================================ -->
@php
  $currentUser = $user ?? auth()->user();
@endphp
<div class="space-y-6">

  <!-- CARD 1: AKSI CEPAT MASTER DATA -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-display font-bold text-base text-textMain tracking-tight">Aksi Master Data</h2>
        <p class="text-[11px] text-textMuted mt-0.5">Katalog, Satuan & Aturan Harga</p>
      </div>
      <span class="w-2.5 h-2.5 rounded-full bg-sidebar animate-pulse" title="Master Data"></span>
    </div>

    <div class="space-y-2.5 pt-1">
      <!-- 1. TAMBAH PRODUK BARU -->
      @if($currentUser?->hasPermission('product.create') || $currentUser?->isSuperAdmin())
        <a href="{{ route('master.products.create') }}" 
           class="w-full p-3.5 bg-sidebar hover:bg-sidebar/95 text-white rounded-2xl transition duration-200 flex items-center justify-between group shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 group-hover:bg-white/20 flex items-center justify-center transition shrink-0">
              <svg class="w-5 h-5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
              </svg>
            </div>
            <div class="text-left">
              <p class="text-xs font-bold leading-tight">+ Tambah Produk</p>
              <p class="text-[10px] text-white/70 mt-0.5">SKU & Multi-Satuan UOM</p>
            </div>
          </div>
          <span class="px-2 py-0.5 rounded-lg bg-white/15 text-[10px] font-bold text-brandOrange">Baru</span>
        </a>
      @endif

      <!-- 2. ATURAN HARGA & FLOOR PRICE -->
      <a href="{{ route('master.pricing.index') }}" 
         class="w-full p-3 bg-white hover:bg-panelBg text-sidebar font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4] shadow-sm">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-amber-50 text-brandOrange flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-xs font-bold leading-tight text-textMain">Harga & Floor Price</p>
            <p class="text-[10px] text-textMuted mt-0.5 font-normal">Kunci Margin & Harga Dasar Kasir</p>
          </div>
        </div>
        <svg class="w-4 h-4 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
      </a>

      <!-- 3. KATEGORI & SATUAN UOM -->
      <a href="{{ route('master.categories.index') }}" 
         class="w-full p-3 bg-white hover:bg-panelBg text-sidebar font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4] shadow-sm">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-xs font-bold leading-tight text-textMain">Kategori & UOM</p>
            <p class="text-[10px] text-textMuted mt-0.5 font-normal">Struktur Golongan Komoditas</p>
          </div>
        </div>
        <svg class="w-4 h-4 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
      </a>

      <!-- 4. MASTER MEMBER QR -->
      <a href="{{ route('master.members.index') }}" 
         class="w-full p-3 bg-white hover:bg-panelBg text-sidebar font-bold rounded-2xl transition duration-150 flex items-center justify-between border border-[#ECEBF4] shadow-sm">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-xs font-bold leading-tight text-textMain">Member & Kartu QR</p>
            <p class="text-[10px] text-textMuted mt-0.5 font-normal">Plafon B2B & Poin Pelanggan</p>
          </div>
        </div>
        <svg class="w-4 h-4 text-textMuted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
      </a>
    </div>
  </div>

  <!-- CARD 2: STANDARISASI KATALOG -->
  <div class="bg-panelBg rounded-[28px] p-6 border border-[#ECEBF4] space-y-4 shadow-sm">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-base text-textMain tracking-tight">Integritas Katalog</h2>
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider">Master</span>
    </div>

    <div class="space-y-3">
      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] space-y-1 shadow-xs">
        <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Multi-UOM Bertingkat</span>
        <p class="text-xs font-semibold text-textMain leading-relaxed">
          Satu produk dapat diperjualbelikan dalam satuan Dus, Bal, Krat, atau Ritel Satuan Dasar dengan rasio konversi otomatis.
        </p>
      </div>

      <div class="bg-white p-4 rounded-2xl border border-[#ECEBF4] space-y-1 shadow-xs">
        <span class="text-[10px] text-textMuted uppercase font-bold tracking-wider">Kunci Harga Dasar (Floor Price)</span>
        <p class="text-xs font-semibold text-textMain leading-relaxed">
          Kasir POS otomatis menolak transaksi jika diskon atau potongan harga menyebabkan penjualan di bawah HPP batas bawah.
        </p>
      </div>
    </div>
  </div>

</div>
