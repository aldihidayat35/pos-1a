@extends('layouts.app')

@section('title', 'Operasional Gudang Pusat — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-7">
  <!-- RBAC Access Warning if role is not warehouse / owner / superadmin -->
  @if(!$user->hasPermission('purchasing.receive') && !$user->hasPermission('inventory.manage') && !$user->isOwner() && !$user->isSuperAdmin())
    <div class="p-4 rounded-2xl bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E] text-xs font-semibold flex items-center gap-3">
      <svg class="w-5 h-5 text-[#F59E0B] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <div>
        <p class="font-bold">Akses Terbatas — Khusus Pengelola Gudang Sentral</p>
        <p class="text-[11px] text-[#92400E]/90 mt-0.5">Akun Anda ({{ $user->primaryRole()?->label ?? 'User' }}) hanya memiliki akses pantau dasar. Mutasi dan penerimaan fisik barang gudang dikunci oleh server.</p>
      </div>
    </div>
  @endif

  <!-- Flash Notifications -->
  @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
      <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
      </svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
      <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
      <span>{{ session('error') }}</span>
    </div>
  @endif

  <!-- 1. SOFT SOLID ORANGE HERO BANNER (Clean) -->
  <div class="relative bg-brandOrange rounded-[24px] p-6 sm:p-8 text-white overflow-hidden">
    <div class="relative z-10 max-w-2xl">
      <h1 class="font-display text-lg sm:text-2xl md:text-[30px] font-bold tracking-tight leading-tight">
        Operasional Gudang Pusat
      </h1>
      <p class="text-white/95 text-[11px] sm:text-xs md:text-[15px] mt-1.5 font-medium">
        {{ $activeLocation?->name ?? 'Gudang Pusat Sentral' }} • Rantai Pasok Inbound & Saldo Stok Real-time
      </p>
    </div>
  </div>

  <!-- 2. QUICK AUTOMATION FUNCTION SHORTCUTS -->
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-sm sm:text-base md:text-lg text-textMain tracking-tight">Fungsi Alur Logistik</h2>
      <div class="flex items-center gap-2 sm:gap-3 text-[11px] sm:text-xs">
        <a href="{{ route('warehouse.po.index') }}" class="font-semibold text-brandCoral hover:underline">Semua PO ({{ $stats['pending_po_count'] }}) →</a>
        <span class="text-gray-300">|</span>
        <a href="{{ route('warehouse.ledger.index') }}" class="font-semibold text-brandOrange hover:underline">Kartu Stok & Audit →</a>
      </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-3.5">
      <!-- Feature 1: Pesanan PO -->
      <a href="{{ route('warehouse.po.index') }}" class="bg-panelBg hover:bg-white border border-[#ECEBF4] hover:border-gray-300 p-3 sm:p-4 rounded-[20px] transition flex items-center gap-2.5 sm:gap-3 group">
        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
          <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
          </svg>
        </div>
        <div>
          <h4 class="font-bold text-[11px] sm:text-xs text-textMain leading-snug">Penerimaan PO</h4>
          <span class="text-[9px] sm:text-[11px] text-textMuted">Inbound Suplayer</span>
        </div>
      </a>

      <!-- Feature 2: Kartu Stok & Ledger -->
      <a href="{{ route('warehouse.ledger.index') }}" class="bg-panelBg hover:bg-white border border-[#ECEBF4] hover:border-gray-300 p-3 sm:p-4 rounded-[20px] transition flex items-center gap-2.5 sm:gap-3 group">
        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
          <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
          </svg>
        </div>
        <div>
          <h4 class="font-bold text-[11px] sm:text-xs text-textMain leading-snug">Kartu Stok</h4>
          <span class="text-[9px] sm:text-[11px] text-textMuted">Audit & HPP Rata-rata</span>
        </div>
      </a>

      <!-- Feature 3: Kirim Transfer Toko -->
      <div onclick="openToast('Kirim Transfer Toko: Modul aktif penuh pada alur mutasi Fase 4!')" class="bg-panelBg hover:bg-white border border-[#ECEBF4] hover:border-gray-300 p-3 sm:p-4 rounded-[20px] cursor-pointer transition flex items-center gap-2.5 sm:gap-3">
        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
          <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
          </svg>
        </div>
        <div>
          <h4 class="font-bold text-[11px] sm:text-xs text-textMain leading-snug">Kirim Transfer</h4>
          <span class="text-[9px] sm:text-[11px] text-textMuted">Gudang → Toko</span>
        </div>
      </div>

      <!-- Feature 4: Blind Count Opname -->
      <div onclick="openToast('Blind Opname Gudang Sentral: Modul aktif penuh pada Fase 7!')" class="bg-panelBg hover:bg-white border border-[#ECEBF4] hover:border-gray-300 p-3 sm:p-4 rounded-[20px] cursor-pointer transition flex items-center gap-2.5 sm:gap-3">
        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
          <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <h4 class="font-bold text-[11px] sm:text-xs text-textMain leading-snug">Blind Opname</h4>
          <span class="text-[9px] sm:text-[11px] text-textMuted">Hitung Fisik Stok</span>
        </div>
      </div>
    </div>
  </div>

  <!-- 3. REAL LIVE PENDING PURCHASE ORDERS TABLE -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#ECEBF4] pb-4">
      <div>
        <h3 class="font-bold text-base text-textMain flex items-center gap-2">
          <span>Pesanan Pembelian Aktif (Inbound Buffer)</span>
          <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold border border-blue-200">
            {{ $pendingPos->count() }} Menunggu Pengiriman
          </span>
        </h3>
        <p class="text-xs text-textMuted mt-0.5">PO yang diterbitkan ke suplayer dan siap diterima fisik di dermaga bongkar muat.</p>
      </div>

      <div class="flex items-center gap-2">
        @if($user->hasPermission('purchasing.po') || $user->hasPermission('inventory.manage') || $user->isSuperAdmin())
          <a href="{{ route('warehouse.po.create') }}" class="px-3.5 py-1.5 bg-sidebar text-white text-xs font-bold rounded-xl hover:bg-sidebar/90 transition flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span>PO Baru</span>
          </a>
        @endif
        <a href="{{ route('warehouse.po.index') }}" class="px-3 py-1.5 bg-panelBg text-sidebar text-xs font-bold rounded-xl hover:bg-[#EAE8F2] transition border border-[#ECEBF4]">
          Lihat Semua PO
        </a>
      </div>
    </div>

    @if($pendingPos->isEmpty())
      <div class="py-10 text-center text-textMuted text-xs">
        Tidak ada pesanan pembelian yang berstatus menunggu penerimaan.
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3 px-3">No. PO</th>
              <th class="py-3 px-3">Suplayer</th>
              <th class="py-3 px-3">Item Pesanan</th>
              <th class="py-3 px-3">Status</th>
              <th class="py-3 px-3 text-right">Nilai Total</th>
              <th class="py-3 px-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @foreach($pendingPos as $po)
              <tr class="hover:bg-panelBg/60 transition">
                <td class="py-3 px-3 font-extrabold text-sidebar">
                  <a href="{{ route('warehouse.po.show', $po->id) }}" class="hover:underline text-brandOrange">
                    {{ $po->po_number }}
                  </a>
                  <span class="block text-[10px] text-textMuted font-normal">
                    {{ $po->ordered_at?->format('d/m/Y H:i') }}
                  </span>
                </td>
                <td class="py-3 px-3">
                  <span class="font-bold text-textMain block">{{ $po->supplier->name }}</span>
                  <span class="text-[10px] text-textMuted">Tujuan: {{ $po->destinationLocation->name }}</span>
                </td>
                <td class="py-3 px-3">
                  <div class="flex flex-col gap-0.5">
                    @foreach($po->items->take(2) as $item)
                      <span class="text-textMain text-[11px]">
                        • {{ $item->product->name }} ({{ number_format($item->quantity_ordered, 0) }} {{ $item->productUom?->uom?->symbol }})
                      </span>
                    @endforeach
                    @if($po->items->count() > 2)
                      <span class="text-[10px] text-textMuted italic">+{{ $po->items->count() - 2 }} item lainnya</span>
                    @endif
                  </div>
                </td>
                <td class="py-3 px-3">
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-block {{ $po->status_badge_class }}">
                    {{ $po->status_label }}
                  </span>
                </td>
                <td class="py-3 px-3 text-right font-extrabold text-textMain">
                  @if($canViewHpp)
                    Rp {{ number_format($po->grand_total, 0, ',', '.') }}
                  @else
                    <span class="text-textMuted italic font-normal">*** [Terkunci]</span>
                  @endif
                </td>
                <td class="py-3 px-3 text-center">
                  @if($user->hasPermission('purchasing.receive') || $user->isSuperAdmin())
                    <a href="{{ route('warehouse.receipts.create', $po->id) }}" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded-lg transition inline-flex items-center gap-1 shadow-sm">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                      </svg>
                      <span>Terima Barang</span>
                    </a>
                  @else
                    <a href="{{ route('warehouse.po.show', $po->id) }}" class="text-xs font-semibold text-sidebar hover:underline">
                      Detail
                    </a>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  <!-- 4. PHYSICAL STOCK ON-HAND & MOVING AVERAGE HPP PREVIEW -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#ECEBF4] pb-4">
      <div>
        <h3 class="font-bold text-base text-textMain flex items-center gap-2">
          <span>Saldo Fisik Stok & Moving Average HPP</span>
          <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">
            {{ $activeLocation?->name ?? 'Gudang Pusat' }}
          </span>
        </h3>
        <p class="text-xs text-textMuted mt-0.5">Saldo fisik dihitung otomatis dari append-only stock movement ledger per lokasi.</p>
      </div>

      <a href="{{ route('warehouse.ledger.index') }}" class="text-xs font-bold text-brandCoral hover:underline self-start sm:self-auto">
        Buka Buku Kartu Stok Lengkap →
      </a>
    </div>

    @if($balances->isEmpty())
      <div class="py-8 text-center text-textMuted text-xs">
        Belum ada saldo barang yang dibukukan di lokasi ini. Catat saldo awal atau penerimaan PO untuk memulai.
      </div>
    @else
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($balances as $bal)
          <div class="p-4 bg-panelBg rounded-2xl border border-[#ECEBF4] flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between gap-1 mb-1">
                <span class="text-[10px] font-bold text-brandOrange font-mono uppercase">{{ $bal->product->sku }}</span>
                <span class="px-2 py-0.5 bg-white rounded-md text-[10px] font-bold text-textMuted border border-gray-200">
                  {{ $bal->product->baseUom->symbol }}
                </span>
              </div>
              <h4 class="font-bold text-sm text-textMain line-clamp-1" title="{{ $bal->product->name }}">
                {{ $bal->product->name }}
              </h4>
            </div>

            <div class="mt-3 pt-3 border-t border-[#ECEBF4]/80 flex items-end justify-between">
              <div>
                <span class="text-[10px] font-bold text-textMuted block">Fisik On-Hand</span>
                <span class="text-lg font-black text-sidebar">
                  {{ number_format((float)$bal->on_hand_qty, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-textMuted font-bold">{{ $bal->product->baseUom->symbol }}</span>
              </div>

              <div class="text-right">
                <span class="text-[10px] font-bold text-textMuted block">Moving Avg HPP</span>
                @if($canViewHpp)
                  <span class="text-xs font-extrabold text-emerald-700">
                    Rp {{ number_format((float)$bal->avg_unit_cost, 0, ',', '.') }}
                  </span>
                  <span class="text-[10px] text-textMuted block">/{{ $bal->product->baseUom->symbol }}</span>
                @else
                  <span class="text-[11px] text-textMuted italic">[Terkunci]</span>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <!-- 5. RECENT INBOUND GOODS RECEIPTS (Surat Jalan Digital) -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-4">
      <div>
        <h3 class="font-bold text-base text-textMain">Bukti Penerimaan Barang Terakhir (GRN)</h3>
        <p class="text-xs text-textMuted mt-0.5">Surat jalan vendor dan pencatatan fisik yang telah diverifikasi oleh tim dermaga gudang.</p>
      </div>
      <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-bold">
        {{ $recentReceipts->count() }} Surat Masuk
      </span>
    </div>

    @if($recentReceipts->isEmpty())
      <div class="py-6 text-center text-textMuted text-xs">
        Belum ada penerimaan barang yang dicatat.
      </div>
    @else
      <div class="divide-y divide-[#ECEBF4]">
        @foreach($recentReceipts as $grn)
          <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <div class="flex items-center gap-2">
                  <a href="{{ route('warehouse.receipts.show', $grn->id) }}" class="font-extrabold text-xs text-sidebar hover:text-brandOrange">
                    {{ $grn->receipt_number }}
                  </a>
                  <span class="text-[10px] px-2 py-0.5 bg-gray-100 rounded text-textMuted font-bold">
                    DO: {{ $grn->supplier_do_number ?? '-' }}
                  </span>
                </div>
                <p class="text-[11px] text-textMuted mt-0.5">
                  Dari: <span class="font-semibold text-textMain">{{ $grn->supplier?->name ?? 'Suplayer' }}</span> • 
                  PO: <a href="{{ route('warehouse.po.show', $grn->purchase_order_id) }}" class="text-blue-600 hover:underline">{{ $grn->purchaseOrder?->po_number ?? '-' }}</a> • 
                  Penerima: {{ $grn->creator?->name }}
                </p>
              </div>
            </div>

            <div class="flex items-center gap-3 self-end sm:self-auto">
              <span class="text-[11px] text-textMuted">
                {{ $grn->posted_at?->format('d/m/Y H:i') }}
              </span>
              <a href="{{ route('warehouse.receipts.show', $grn->id) }}" class="px-2.5 py-1 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-[11px] rounded-lg transition border border-[#ECEBF4]">
                Lihat Bukti →
              </a>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</main>
@endsection

@section('right_panel')
  @include('warehouse.partials.right-panel')
@endsection
