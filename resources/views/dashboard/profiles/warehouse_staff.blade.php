<!-- =======================================================
     PROFIL DASHBOARD: STAF GUDANG / OPERATOR LOGISTIK
     Fokus: Kinerja Personal Operator, Tugas Penerimaan PO, Picking Transfer & Blind Count Opname
     ======================================================= -->
<div class="space-y-7">

  <!-- 1. HERO BANNER OPERATOR GUDANG (BERSIH) -->
  <div class="relative bg-brandOrange rounded-[26px] p-6 sm:p-8 text-white overflow-hidden shadow-sm">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-xs font-semibold mb-3">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span>Operator Logistik Fisik • Sentral Pergudangan</span>
      </div>
      <h1 class="font-display text-2xl sm:text-[32px] font-bold tracking-tight leading-tight">
        Selamat Bertugas, {{ $user->name }}!
      </h1>
      <p class="text-white/95 text-xs sm:text-sm mt-1.5 font-medium leading-relaxed">
        Staf Gudang • Lokasi Kerja: <strong class="font-bold underline">{{ $stats['active_location_name'] }}</strong> ({{ $stats['active_location_code'] }})
      </p>

      <div class="mt-4 flex flex-wrap items-center gap-2">
        <div class="bg-white/20 px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span>{{ $profileData['total_ops_count'] }} Tugas Fisik Diselesaikan Bulan Ini</span>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. KARTU KINERJA PERSONAL OPERATOR GUDANG -->
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-lg text-textMain">Kinerja Tugas Logistik Anda (Bulan Ini)</h2>
      <span class="text-xs text-textMuted font-medium">Bulan: {{ now()->translatedFormat('F Y') }}</span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Card 1: Penerimaan PO -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Penerimaan PO Diterima</span>
          <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-emerald-600 mt-3">
          {{ $profileData['po_received_count'] }} Dokumen
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Barang suplayer diverifikasi fisik
        </p>
      </div>

      <!-- Card 2: Transfer Disiapkan / Picking -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Transfer Disiapkan (Picking)</span>
          <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-blue-600 mt-3">
          {{ $profileData['transfers_dispatched_count'] }} Order
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Pengiriman disiapkan untuk toko
        </p>
      </div>

      <!-- Card 3: Blind Count Opname -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Hitung Blind Count</span>
          <div class="w-7 h-7 rounded-lg bg-amber-50 text-brandOrange flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-textMain mt-3">
          {{ $profileData['blind_count_items_count'] }} Item SKU
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Fisik rak gudang dihitung buta
        </p>
      </div>

      <!-- Card 4: Total Operasi Fisik -->
      <div class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Total Aktivitas Tugas</span>
          <div class="w-7 h-7 rounded-lg bg-purple-50 text-sidebar flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          </div>
        </div>
        <p class="font-display text-xl sm:text-2xl font-extrabold text-sidebar mt-3">
          {{ $profileData['total_ops_count'] }} Tugas
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Aktivitas fisik tercatat di sistem
        </p>
      </div>
    </div>
  </div>

  <!-- 3. INDIKATOR TARGET AKTIVITAS KERJA GUDANG (PROGRESS BAR) -->
  <div class="bg-white rounded-[24px] p-6 border border-[#ECEBF4] shadow-xs space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-bold text-base text-textMain">Target Aktivitas Operasional Bulan Ini</h3>
        <p class="text-xs text-textMuted mt-0.5">Pantau target verifikasi dan penanganan barang fisik Anda</p>
      </div>
      <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
        Target: {{ $profileData['target_ops'] }} Tugas ({{ $profileData['ops_progress'] }}%)
      </span>
    </div>

    <div>
      <div class="w-full h-3.5 bg-panelBg rounded-full overflow-hidden border border-[#E6E4F0]">
        <div class="h-full bg-gradient-to-r from-blue-500 to-sidebar rounded-full transition-all duration-500" style="width: {{ $profileData['ops_progress'] }}%"></div>
      </div>
    </div>
  </div>

  <!-- 4. RIWAYAT TUGAS FISIK LOGISTIK TERAKHIR -->
  <div class="bg-white rounded-[24px] p-6 border border-[#ECEBF4] shadow-xs space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-bold text-base text-textMain">Aktivitas Fisik Terakhir yang Anda Tangani</h3>
        <p class="text-xs text-textMuted mt-0.5">5 log mutasi logistik yang melibatkan akun Anda</p>
      </div>
      <span class="text-xs text-textMuted">Terekam Otomatis</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted uppercase text-[10px] tracking-wider">
            <th class="pb-3 font-bold">Waktu</th>
            <th class="pb-3 font-bold">Produk</th>
            <th class="pb-3 font-bold">Aktivitas</th>
            <th class="pb-3 font-bold text-right">Kuantitas</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#F0EFF8]">
          @forelse($profileData['recent_movements'] as $m)
            <tr class="hover:bg-panelBg/50 transition">
              <td class="py-3 text-textMuted whitespace-nowrap">{{ $m->occurred_at->format('d M, H:i') }} WIB</td>
              <td class="py-3 font-bold text-textMain whitespace-nowrap">{{ $m->product?->name ?? 'Produk' }}</td>
              <td class="py-3 whitespace-nowrap">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-panelBg text-sidebar">
                  {{ $m->movement_type }}
                </span>
              </td>
              <td class="py-3 text-right font-extrabold whitespace-nowrap text-sidebar">
                {{ $m->delta_qty_base > 0 ? '+'.(float)$m->delta_qty_base : (float)$m->delta_qty_base }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="py-6 text-center text-textMuted">Belum ada tugas fisik logistik yang tercatat atas nama Anda bulan ini.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
