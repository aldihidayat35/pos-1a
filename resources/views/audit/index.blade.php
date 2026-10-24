@extends('layouts.app')

@section('title', 'Feed Pergerakan 5-Warna & Anomali — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-7">
  <!-- 1. HEADER BANNER (Flat Solid Sidebar Color #342A6E, no gradient) -->
  <div class="bg-sidebar text-white p-6 sm:p-8 rounded-[26px] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
    <div>
      <div class="inline-flex items-center gap-2 bg-white/15 px-3 py-1 rounded-full text-xs font-semibold mb-2.5">
        <span class="w-2 h-2 rounded-full bg-moveRed animate-pulse"></span>
        <span>Zero-Bottleneck Accountability Board</span>
        @if($user->isOwner())
          <span class="ml-2 bg-brandCoral text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
            Mode Owner Read-Only
          </span>
        @endif
      </div>
      <h1 class="font-display text-2xl sm:text-[30px] font-bold tracking-tight leading-tight">
        Audit Trail & Laporan Anomali Terpadu
      </h1>
      <p class="text-white/85 text-xs sm:text-sm mt-1.5 font-medium leading-relaxed max-w-2xl">
        Kendali mutu tanpa botol approval: Setiap tindakan mengikat <code class="bg-white/20 px-1 py-0.5 rounded text-white font-mono">user_id</code>, role, dan timestamp permanen.
      </p>
    </div>
    
    <div class="flex items-center gap-2 shrink-0">
      <button onclick="openToast('Filter warna aktif: Menampilkan riwayat selisih merah!')" class="inline-flex items-center gap-2 bg-brandCoral hover:opacity-95 text-white text-xs font-bold px-5 py-3 rounded-2xl transition active:scale-95 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span>Filter Anomali Merah</span>
      </button>
    </div>
  </div>

  <!-- 2. 5-WARNA STANDARD LEGEND BAR -->
  <div class="bg-white p-5 rounded-[22px] border border-[#ECEBF4] flex flex-wrap items-center justify-between gap-4 text-xs">
    <div class="flex items-center gap-2">
      <span class="font-bold text-textMain">Standar 5 Warna:</span>
    </div>
    <div class="flex flex-wrap items-center gap-3 font-semibold">
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-moveGreenBg text-moveGreen border border-emerald-200">
        <span class="w-2 h-2 rounded-full bg-moveGreen"></span>
        <span>Hijau: Masuk Suplayer</span>
      </div>
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-moveBlueBg text-moveBlue border border-blue-200">
        <span class="w-2 h-2 rounded-full bg-moveBlue"></span>
        <span>Biru: Mutasi Internal</span>
      </div>
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-movePurpleBg text-movePurple border border-purple-200">
        <span class="w-2 h-2 rounded-full bg-movePurple"></span>
        <span>Ungu: Penjualan POS / B2B</span>
      </div>
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-moveOrangeBg text-moveOrange border border-amber-200">
        <span class="w-2 h-2 rounded-full bg-moveOrange"></span>
        <span>Oranye: Belanja Emergency</span>
      </div>
      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-moveRedBg text-moveRed border border-red-200">
        <span class="w-2 h-2 rounded-full bg-moveRed"></span>
        <span>Merah: Selisih Opname / Susut</span>
      </div>
    </div>
  </div>

  <!-- 3. 3 ANOMALY KEY METRIC CARDS -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <div class="flex justify-between items-center">
        <span class="text-[11px] font-bold text-moveRed bg-moveRedBg px-2.5 py-0.5 rounded-full border border-red-200">SELISIH MINUS</span>
        <span class="text-xs font-extrabold text-moveRed font-mono">-Rp 224.000</span>
      </div>
      <h3 class="font-bold text-sm text-textMain mt-3">Stok Opname Toko & Gudang</h3>
      <p class="text-xs text-textMuted mt-1">2 Produk Susut/Bocor • Disahkan Langsung oleh Kepala Toko</p>
    </div>

    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <div class="flex justify-between items-center">
        <span class="text-[11px] font-bold text-moveOrange bg-moveOrangeBg px-2.5 py-0.5 rounded-full border border-amber-200">EMERGENCY STOCK</span>
        <span class="text-xs font-extrabold text-moveOrange font-mono">3 Transaksi</span>
      </div>
      <h3 class="font-bold text-sm text-textMain mt-3">Belanja Cepat Tanpa Approval</h3>
      <p class="text-xs text-textMuted mt-1">Total Rp 2.650.000 • Seluruh Foto Nota Tersimpan di Log</p>
    </div>

    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <div class="flex justify-between items-center">
        <span class="text-[11px] font-bold text-movePurple bg-movePurpleBg px-2.5 py-0.5 rounded-full border border-purple-200">SYSTEM LOCK STATUS</span>
        <span class="text-xs font-extrabold text-emerald-600">AKTIF (0 Jebol)</span>
      </div>
      <h3 class="font-bold text-sm text-textMain mt-3">Floor Price & Credit Plafon Lock</h3>
      <p class="text-xs text-textMuted mt-1">Kunci Otomatis Kasir POS & Pengunci Piutang B2B Bekerja 100%</p>
    </div>
  </div>

  <!-- 4. ACCOUNTABILITY BOARD -->
  <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-bold text-base text-textMain">
          Tabel Pertanggungjawaban Langsung Akun
        </h3>
        <p class="text-xs text-textMuted mt-0.5">
          Daftar eksekusi krusial tanpa approval yang mengikat identitas operator secara permanen
        </p>
      </div>
      <span class="text-xs font-semibold text-textMuted">Audit Trail RBAC</span>
    </div>

    <div class="space-y-3 text-xs">
      <!-- Item 1: Opname Discrepancy -->
      <div class="p-4 rounded-2xl bg-panelBg border border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#F2F1FA] transition">
        <div class="flex items-start gap-3">
          <div class="w-9 h-9 rounded-xl bg-moveRedBg text-moveRed flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="font-bold text-textMain">Finalisasi Opname Toko: Selisih -Rp 74.000</span>
              <x-movement-badge type="red" size="sm" />
            </div>
            <p class="text-textMuted mt-1">
              Produk: <strong>Susu UHT 1L (-4 Pcs)</strong> • Alasan: <span class="text-textMain font-semibold">Kemasan Bocor di Rak A-02</span>
            </p>
            <p class="text-[11px] text-textMuted mt-0.5">
              Eksekutor: <span class="font-semibold text-textMain">Raka (Kepala Toko)</span> • User ID: #USR-004 • 14:15 WIB
            </p>
          </div>
        </div>
        <button onclick="openToast('Detail Audit #OPN-881: Operator Raka bertanggung jawab atas selisih susut -Rp 74.000.')" class="px-3 py-1.5 rounded-xl bg-white text-sidebar font-bold text-xs border border-[#E6E4F0] hover:bg-sidebar hover:text-white transition self-start sm:self-auto shrink-0">
          Lihat Log Audit
        </button>
      </div>

      <!-- Item 2: Emergency Stock -->
      <div class="p-4 rounded-2xl bg-panelBg border border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#F2F1FA] transition">
        <div class="flex items-start gap-3">
          <div class="w-9 h-9 rounded-xl bg-moveOrangeBg text-moveOrange flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="font-bold text-textMain">Input Stok Emergency Toko: Rp 1.450.000</span>
              <x-movement-badge type="orange" size="sm" />
            </div>
            <p class="text-textMuted mt-1">
              Produk: <strong>Telur Ayam Broiler (+6 Krat)</strong> • Beli Tunai Langsung ke Pasar Raya
            </p>
            <p class="text-[11px] text-textMuted mt-0.5">
              Eksekutor: <span class="font-semibold text-textMain">Maya (Staf Toko Kasir)</span> • User ID: #USR-006 • 13:30 WIB • Foto Nota: Terverifikasi
            </p>
          </div>
        </div>
        <button onclick="openToast('Foto Nota Telur #EMG-004: Rp 1.450.000 tercatat di laporan kas kecil toko.')" class="px-3 py-1.5 rounded-xl bg-white text-sidebar font-bold text-xs border border-[#E6E4F0] hover:bg-sidebar hover:text-white transition self-start sm:self-auto shrink-0">
          Lihat Foto Nota
        </button>
      </div>

      <!-- Item 3: PO Inbound Warehouse -->
      <div class="p-4 rounded-2xl bg-panelBg border border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#F2F1FA] transition">
        <div class="flex items-start gap-3">
          <div class="w-9 h-9 rounded-xl bg-moveGreenBg text-moveGreen flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="font-bold text-textMain">Penerimaan PO Suplayer: 40 Karung Beras</span>
              <x-movement-badge type="green" size="sm" />
            </div>
            <p class="text-textMuted mt-1">
              PO #PO-SUP-8921 • Suplayer: PT Lumbung Pangan Nusantara • Total: Rp 26.000.000
            </p>
            <p class="text-[11px] text-textMuted mt-0.5">
              Eksekutor: <span class="font-semibold text-textMain">Hendra (Staf Gudang)</span> • Timbangan RFID Dock A1 • 12:40 WIB
            </p>
          </div>
        </div>
        <button onclick="openToast('Audit Manifest #PO-SUP-8921: 40 Karung Beras lolos uji timbang digital.')" class="px-3 py-1.5 rounded-xl bg-white text-sidebar font-bold text-xs border border-[#E6E4F0] hover:bg-sidebar hover:text-white transition self-start sm:self-auto shrink-0">
          Surat Jalan Digital
        </button>
      </div>
    </div>
  </div>
</main>
@endsection
