<!-- =======================================================
     PROFIL DASHBOARD: GUDANG & LOGISTIK (KEPALA GUDANG & STAF GUDANG)
     Fokus: Penerimaan PO, Transfer Antar Lokasi, Stok Opname, Ledger
     ======================================================= -->
<div class="space-y-7">

  <!-- 1. HERO BANNER GUDANG & LOGISTIK -->
  <div class="relative bg-brandOrange rounded-[26px] p-6 sm:p-8 text-white overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-sm">
    <div class="relative z-10 max-w-xl">
      <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-xs font-semibold mb-3">
        <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
        <span>Rantai Pasok &amp; Logistik Sentral • Real-Time</span>
      </div>
      <h1 class="font-display text-2xl sm:text-[32px] font-bold tracking-tight leading-tight">
        Halo, {{ $user->name }}!
      </h1>
      <p class="text-white/95 text-xs sm:text-sm mt-1.5 font-medium leading-relaxed">
        {{ $stats['user_role_label'] }} • Lokasi Gudang: <strong class="font-bold underline">{{ $stats['active_location_name'] }}</strong> ({{ $stats['active_location_code'] }})
      </p>

      <div class="mt-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('warehouse.po.index') }}" class="bg-white/20 hover:bg-white/30 transition px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <span>{{ $profileData['pending_pos_count'] }} PO Menunggu Terima</span>
        </a>
        <a href="{{ route('warehouse.transfers.index') }}" class="bg-white/20 hover:bg-white/30 transition px-3 py-1.5 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
          <span>{{ $profileData['draft_transfers_count'] }} Transfer Draft Keluar</span>
        </a>
      </div>
    </div>

    <!-- Quick Action Touch Targets -->
    <div class="relative z-10 flex flex-col sm:flex-row md:flex-col gap-2.5 w-full md:w-auto shrink-0">
      @if($user->hasPermission('purchasing.po'))
        <a href="{{ route('warehouse.po.create') }}" class="min-h-[48px] w-full px-5 py-3 rounded-2xl bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm active:scale-95">
          <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          <span>Buat PO Suplayer Baru</span>
        </a>
      @endif

      <div class="flex gap-2 w-full">
        @if($user->hasPermission('transfer.dispatch'))
          <a href="{{ route('warehouse.transfers.create') }}" class="min-h-[48px] flex-1 px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            <span>+ Transfer Toko</span>
          </a>
        @endif

        <a href="{{ route('opname.index') }}" class="min-h-[48px] flex-1 px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-95">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          <span>Stok Opname</span>
        </a>
      </div>
    </div>
  </div>

  <!-- 2. KARTU METRIK OPERASIONAL GUDANG -->
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-lg text-textMain">Ringkasan Rantai Pasok Gudang</h2>
      <span class="text-xs text-textMuted font-medium">Gudang: {{ $stats['active_location_name'] }}</span>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <!-- Card 1: PO Menunggu Terima -->
      <a href="{{ route('warehouse.po.index') }}" class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200 block group shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">PO Menunggu Terima</span>
          <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </div>
        </div>
        <p class="font-display text-lg sm:text-2xl font-extrabold text-sidebar mt-3">
          {{ $profileData['pending_pos_count'] }} Dokumen PO
        </p>
        <p class="text-[11px] font-semibold mt-1 {{ $profileData['pending_pos_count'] > 0 ? 'text-emerald-700' : 'text-textMuted' }}">
          {{ $profileData['pending_pos_count'] > 0 ? 'Siap Diterima Fisik di Gudang' : 'Semua PO Telah Diterima' }}
        </p>
      </a>

      <!-- Card 2: Transfer Keluar (Draft) -->
      <a href="{{ route('warehouse.transfers.index') }}" class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200 block group shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Transfer Keluar (Draft)</span>
          <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
          </div>
        </div>
        <p class="font-display text-lg sm:text-2xl font-extrabold text-blue-800 mt-3">
          {{ $profileData['draft_transfers_count'] }} Pengiriman
        </p>
        <p class="text-[11px] font-semibold mt-1 {{ $profileData['draft_transfers_count'] > 0 ? 'text-blue-700' : 'text-textMuted' }}">
          {{ $profileData['draft_transfers_count'] > 0 ? 'Perlu Dipacking & Dispatch' : 'Tidak Ada Draft Pengiriman' }}
        </p>
      </a>

      <!-- Card 3: Peringatan Stok Menipis -->
      <a href="{{ route('warehouse.index') }}" class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200 block group shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Stok Menipis (&le; 10)</span>
          <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          </div>
        </div>
        <p class="font-display text-lg sm:text-2xl font-extrabold text-brandOrange mt-3">
          {{ $profileData['low_stock_count'] }} SKU Kritis
        </p>
        <p class="text-[11px] text-textMuted mt-1">
          Pertimbangkan Buat PO Suplayer
        </p>
      </a>

      <!-- Card 4: Sesi Stok Opname -->
      <a href="{{ route('opname.index') }}" class="bg-white rounded-[22px] p-5 border border-[#ECEBF4] hover:bg-panelBg/40 transition duration-200 block group shadow-xs">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Sesi Stok Opname</span>
          <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          </div>
        </div>
        <p class="font-display text-lg sm:text-xl font-extrabold text-sidebar mt-3">
          {{ !empty($profileData['active_opname']) ? 'Sesi Sedang Berjalan' : 'Tidak Ada Sesi' }}
        </p>
        <p class="text-[11px] font-semibold mt-1 {{ !empty($profileData['active_opname']) ? 'text-purple-700' : 'text-textMuted' }}">
          {{ !empty($profileData['active_opname']) ? 'Status: ' . $profileData['active_opname']->status : 'Jadwalkan Opname Buta' }}
        </p>
      </a>
    </div>
  </div>

  <!-- 3. TABEL 5 PERGERAKAN STOK GUDANG TERAKHIR -->
  <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-4 shadow-xs">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-display font-bold text-base text-textMain">Pergerakan Stok Gudang Terkini</h3>
        <p class="text-xs text-textMuted mt-0.5">Buku besar mutasi fisik barang masuk, transfer keluar, dan alokasi</p>
      </div>
      <a href="{{ route('warehouse.ledger.index') }}" class="text-xs font-bold text-sidebar hover:underline inline-flex items-center gap-1">
        <span>Buku Besar Ledger</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      </a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted">
            <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Waktu</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Produk &amp; SKU</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px] text-center">Tipe Alur</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px] text-right">Mutasi Qty</th>
            <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Aktor / Petugas</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @forelse($profileData['recent_movements'] as $m)
            <tr class="hover:bg-panelBg/50 transition">
              <td class="py-3 px-3 text-textMuted">
                {{ $m->occurred_at?->format('d/m/Y H:i') ?? '-' }}
              </td>
              <td class="py-3 px-3 font-semibold text-textMain">
                {{ $m->product?->name ?? 'Produk' }}
                <span class="text-[11px] text-textMuted font-normal font-mono block">({{ $m->product?->sku ?? '-' }})</span>
              </td>
              <td class="py-3 px-3 text-center">
                <x-movement-badge :type="strtolower($m->semantic_status ?: 'green')" size="sm" />
              </td>
              <td class="py-3 px-3 font-mono font-bold text-right {{ (float)$m->delta_qty_base >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                {{ (float)$m->delta_qty_base >= 0 ? '+' : '' }}{{ number_format($m->delta_qty_base, 2, ',', '.') }}
              </td>
              <td class="py-3 px-3 text-textMuted">
                {{ $m->operation?->actor?->name ?? 'Sistem' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-6 text-center text-textMuted italic">
                Belum ada mutasi pergerakan stok tercatat di gudang ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
