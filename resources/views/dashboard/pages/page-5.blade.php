<!-- =======================================================
     PAGE 5 (INDEX 5): MASTER PRODUK, SATUAN & MEMBER QR
     ======================================================= -->
<div id="page-5" class="page-view hidden space-y-6">
  <!-- Header Banner -->
  <div class="bg-panelBg p-6 sm:p-7 rounded-[26px] border border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sidebar text-white uppercase tracking-wider">FASE 2 • MASTER DATA</span>
        <span class="text-xs text-textMuted">• Single Source of Truth</span>
      </div>
      <h1 class="font-display text-2xl font-bold text-textMain mt-1.5">Master Katalog & Entitas Pasar</h1>
      <p class="text-xs text-textMuted mt-1 max-w-xl">
        Seluruh pondasi data produk, satuan multi-level ber-barcode, rekanan suplayer, pelanggan ber-QR token, dan pemetaan multi-lokasi operasional.
      </p>
    </div>
    
    <a href="{{ route('master.products.index') }}" class="px-5 py-3 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-2xl transition flex items-center gap-2 shrink-0 shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
      </svg>
      <span>Buka Modul Master Penuh</span>
    </a>
  </div>

  <!-- Quick Hub Grid of 5 Modules -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    <!-- Hub Card 1: Katalog Produk & Multi-UOM -->
    <a href="{{ route('master.products.index') }}" class="group bg-white p-6 rounded-3xl border border-[#ECEBF4] hover:border-sidebar/40 transition shadow-sm hover:shadow-md flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 rounded-2xl bg-sidebar/10 text-sidebar flex items-center justify-center group-hover:bg-sidebar group-hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <span class="text-xs font-mono font-bold text-sidebar bg-sidebar/5 px-2.5 py-1 rounded-lg">
            {{ \App\Models\Product::count() }} SKU
          </span>
        </div>
        <h3 class="font-bold text-base text-textMain group-hover:text-sidebar transition">Katalog Master Produk</h3>
        <p class="text-xs text-textMuted mt-1.5 leading-relaxed">
          SKU unik, satuan dasar (Base UOM), konversi bertingkat (karung, karton), dan deteksi kemiripan nama produk (Fuzzy >= 75%).
        </p>
      </div>
      <div class="pt-4 mt-4 border-t border-[#ECEBF4] flex items-center justify-between text-xs font-bold text-sidebar">
        <span>Kelola Produk →</span>
        <span class="text-[10px] text-textMuted font-normal">Multi-Barcode</span>
      </div>
    </a>

    <!-- Hub Card 2: Satuan UOM -->
    <a href="{{ route('master.uoms.index') }}" class="group bg-white p-6 rounded-3xl border border-[#ECEBF4] hover:border-sidebar/40 transition shadow-sm hover:shadow-md flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 rounded-2xl bg-brandOrange/10 text-brandOrange flex items-center justify-center group-hover:bg-brandOrange group-hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 18h12l3-18H3z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
            </svg>
          </div>
          <span class="text-xs font-mono font-bold text-brandOrange bg-brandOrange/5 px-2.5 py-1 rounded-lg">
            {{ \App\Models\Uom::count() }} Satuan
          </span>
        </div>
        <h3 class="font-bold text-base text-textMain group-hover:text-brandOrange transition">Satuan Fisik (UOM)</h3>
        <p class="text-xs text-textMuted mt-1.5 leading-relaxed">
          Dimensi fisik ketat (Weight, Count, Volume) dengan proteksi anti-silang dimensi dan konfigurasi presisi desimal hingga 4 digit.
        </p>
      </div>
      <div class="pt-4 mt-4 border-t border-[#ECEBF4] flex items-center justify-between text-xs font-bold text-brandOrange">
        <span>Kelola Satuan →</span>
        <span class="text-[10px] text-textMuted font-normal">3 Dimensi</span>
      </div>
    </a>

    <!-- Hub Card 3: Mitra Pemasok (Suplayer) -->
    <a href="{{ route('master.suppliers.index') }}" class="group bg-white p-6 rounded-3xl border border-[#ECEBF4] hover:border-sidebar/40 transition shadow-sm hover:shadow-md flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
          </div>
          <span class="text-xs font-mono font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">
            {{ \App\Models\Supplier::count() }} Pemasok
          </span>
        </div>
        <h3 class="font-bold text-base text-textMain group-hover:text-emerald-600 transition">Mitra Suplayer</h3>
        <p class="text-xs text-textMuted mt-1.5 leading-relaxed">
          Data petani, produsen beras, distributor minyak, kontak PIC, dan riwayat alamat pengiriman untuk alur Purchase Order.
        </p>
      </div>
      <div class="pt-4 mt-4 border-t border-[#ECEBF4] flex items-center justify-between text-xs font-bold text-emerald-600">
        <span>Kelola Suplayer →</span>
        <span class="text-[10px] text-textMuted font-normal">Vendor Resmi</span>
      </div>
    </a>

    <!-- Hub Card 4: Member & Kartu QR B2B -->
    <a href="{{ route('master.members.index') }}" class="group bg-white p-6 rounded-3xl border border-[#ECEBF4] hover:border-sidebar/40 transition shadow-sm hover:shadow-md flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
          <span class="text-xs font-mono font-bold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-lg">
            {{ \App\Models\Member::count() }} Member
          </span>
        </div>
        <h3 class="font-bold text-base text-textMain group-hover:text-purple-600 transition">Pelanggan & Member QR</h3>
        <p class="text-xs text-textMuted mt-1.5 leading-relaxed">
          Kartu digital dengan QR vector terintegrasi, tier diskon otomatis (Silver, Gold), dan plafon kredit khusus rekanan B2B.
        </p>
      </div>
      <div class="pt-4 mt-4 border-t border-[#ECEBF4] flex items-center justify-between text-xs font-bold text-purple-600">
        <span>Kelola Member & QR →</span>
        <span class="text-[10px] text-textMuted font-normal">Cetak QR Ready</span>
      </div>
    </a>

    <!-- Hub Card 5: Kategori Komoditas -->
    <a href="{{ route('master.categories.index') }}" class="group bg-white p-6 rounded-3xl border border-[#ECEBF4] hover:border-sidebar/40 transition shadow-sm hover:shadow-md flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
            </svg>
          </div>
          <span class="text-xs font-mono font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">
            {{ \App\Models\ProductCategory::count() }} Kategori
          </span>
        </div>
        <h3 class="font-bold text-base text-textMain group-hover:text-blue-600 transition">Kategori Komoditas</h3>
        <p class="text-xs text-textMuted mt-1.5 leading-relaxed">
          Hierarki taksonomi sembako, palawija, kemasan, sayur-mayur, dan produk olahan untuk kemudahan pencarian POS.
        </p>
      </div>
      <div class="pt-4 mt-4 border-t border-[#ECEBF4] flex items-center justify-between text-xs font-bold text-blue-600">
        <span>Kelola Kategori →</span>
        <span class="text-[10px] text-textMuted font-normal">Pohon Kategori</span>
      </div>
    </a>

    <!-- Hub Card 6: Lokasi Gudang & Toko -->
    <a href="{{ route('master.locations.index') }}" class="group bg-white p-6 rounded-3xl border border-[#ECEBF4] hover:border-sidebar/40 transition shadow-sm hover:shadow-md flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <span class="text-xs font-mono font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">
            {{ \App\Models\Location::count() }} Lokasi
          </span>
        </div>
        <h3 class="font-bold text-base text-textMain group-hover:text-amber-600 transition">Lokasi Gudang & Toko</h3>
        <p class="text-xs text-textMuted mt-1.5 leading-relaxed">
          Pusat pengelolaan multi-cabang (Gudang Pusat, Gerai Toko Pasar, Pos Transit) yang mengisolasi stok dan otorisasi staf.
        </p>
      </div>
      <div class="pt-4 mt-4 border-t border-[#ECEBF4] flex items-center justify-between text-xs font-bold text-amber-600">
        <span>Kelola Lokasi →</span>
        <span class="text-[10px] text-textMuted font-normal">Multi-Lokasi</span>
      </div>
    </a>
  </div>
</div>
