<!-- =======================================================
     PAGE 5 (INDEX 5): SURAT JALAN & NOTIFIKASI
     ======================================================= -->
<div id="page-5" class="page-view hidden space-y-6">
  <div class="bg-panelBg p-6 rounded-[24px] border border-[#ECEBF4] flex items-center justify-between">
    <div>
      <h1 class="font-display text-2xl font-bold text-textMain">Persetujuan Surat Jalan & Dokumen</h1>
      <p class="text-xs text-textMuted mt-1">Daftar manifest pengiriman (DO) dan surat pesanan (PO) yang menunggu tanda tangan digital.</p>
    </div>
    <span class="bg-brandCoral text-white text-xs font-bold px-3.5 py-1.5 rounded-full">3 Menunggu</span>
  </div>

  <div class="space-y-3">
    <div class="bg-white p-5 rounded-[22px] border border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <span class="text-[11px] font-bold text-brandOrange bg-brandOrange/10 px-2.5 py-0.5 rounded-full">SURAT JALAN KELUAR • #SJ-2026-891</span>
        <h3 class="font-bold text-sm mt-2">Pengiriman 450 Unit Monitor LED ke Cabang Surabaya</h3>
        <p class="text-xs text-textMuted mt-0.5">Ekspedisi: Siba Cargo • Sopir: Pak Wahyu • Siap di Gate B2</p>
      </div>
      <button onclick="this.innerText='✓ Disetujui'; this.className='bg-emerald-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl pointer-events-none'; openToast('Surat Jalan #SJ-2026-891 berhasil ditandatangani secara digital!')" class="bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shrink-0">
        Setujui & Cetak
      </button>
    </div>

    <div class="bg-white p-5 rounded-[22px] border border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <span class="text-[11px] font-bold text-brandCoral bg-brandCoral/10 px-2.5 py-0.5 rounded-full">KLAIM RETUR • #RT-2026-104</span>
        <h3 class="font-bold text-sm mt-2">Laporan Kemasan Penyok (12 Box Susu UHT)</h3>
        <p class="text-xs text-textMuted mt-0.5">Diajukan oleh Tim QC Inbound • Menunggu isolasi ke Zona Karantina</p>
      </div>
      <button onclick="this.innerText='✓ Terkonfirmasi'; this.className='bg-emerald-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl pointer-events-none'; openToast('Barang retur dipindahkan ke Rak Karantina!')" class="bg-brandCoral hover:opacity-90 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shrink-0">
        Konfirmasi Isolasi
      </button>
    </div>
  </div>
</div>
