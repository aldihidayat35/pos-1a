<!-- =======================================================
     PAGE 2 (INDEX 2): INVENTARIS & STOK RAK
     ======================================================= -->
<div id="page-2" class="page-view hidden space-y-6">
  @if(!$user->hasPermission('pos.access') && !$user->isOwner() && !$user->isSuperAdmin())
    <div class="p-4 rounded-2xl bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E] text-xs font-semibold flex items-center gap-3">
      <svg class="w-5 h-5 text-[#F59E0B] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <div>
        <p class="font-bold">Akses Dibatasi — Khusus Pengelola Toko & Kasir</p>
        <p class="text-[11px] text-[#92400E]/90 mt-0.5">Akun Anda ({{ $user->primaryRole()?->label ?? 'User' }}) tidak memiliki izin transaksi kasir POS. Otorisasi penjualan dan shift laci uang dilindungi di server.</p>
      </div>
    </div>
  @endif
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-panelBg p-6 rounded-[24px] border border-[#ECEBF4]">
    <div>
      <h1 class="font-display text-2xl font-bold text-textMain">Inventaris & Direktori Rak</h1>
      <p class="text-xs text-textMuted mt-1">Kelola stok SKU berdasarkan kategori zona penyimpanan gudang.</p>
    </div>
    <div class="flex items-center gap-2">
      <input id="skuSearchInput" oninput="filterInventory()" type="text" placeholder="Cari kode SKU atau barang..." class="bg-white text-xs px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-sidebar w-52" />
      <button onclick="addSampleSku()" class="bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold px-4 py-2.5 rounded-xl transition">
        + Tambah SKU
      </button>
    </div>
  </div>

  <!-- Rack Category Folders (Flat soft solid colors, no shadow glow) -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="bg-brandOrange text-white p-5 rounded-[22px]">
      <p class="text-xs opacity-85">ZONA RAK A</p>
      <h3 class="font-display font-bold text-lg mt-1">Elektronik</h3>
      <p class="text-2xl font-extrabold mt-3">4.210 <span class="text-xs font-normal">Unit</span></p>
    </div>
    <div class="bg-brandCoral text-white p-5 rounded-[22px]">
      <p class="text-xs opacity-85">ZONA RAK B</p>
      <h3 class="font-display font-bold text-lg mt-1">FMCG & Pangan</h3>
      <p class="text-2xl font-extrabold mt-3">6.890 <span class="text-xs font-normal">Unit</span></p>
    </div>
    <div class="bg-brandCyan text-sidebar p-5 rounded-[22px]">
      <p class="text-xs opacity-75 font-semibold">ZONA RAK C</p>
      <h3 class="font-display font-bold text-lg mt-1">Suku Cadang</h3>
      <p class="text-2xl font-extrabold mt-3">2.140 <span class="text-xs font-normal">Unit</span></p>
    </div>
    <div class="bg-sidebar text-white p-5 rounded-[22px]">
      <p class="text-xs opacity-85">ZONA RAK D</p>
      <h3 class="font-display font-bold text-lg mt-1">Cold Storage</h3>
      <p class="text-2xl font-extrabold mt-3">1.580 <span class="text-xs font-normal">Unit</span></p>
    </div>
  </div>

  <!-- Inventory Table -->
  <div class="bg-white rounded-[24px] p-5 border border-[#ECEBF4] overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="text-[11px] uppercase text-textMuted border-b border-gray-100">
          <th class="pb-3 font-bold">Kode SKU</th>
          <th class="pb-3 font-bold">Nama Barang</th>
          <th class="pb-3 font-bold">Lokasi Rak</th>
          <th class="pb-3 font-bold">Stok Tersedia</th>
          <th class="pb-3 font-bold">Status</th>
        </tr>
      </thead>
      <tbody id="inventoryTableBody" class="text-xs divide-y divide-gray-50">
        <!-- Populated by JS -->
      </tbody>
    </table>
  </div>
</div>
