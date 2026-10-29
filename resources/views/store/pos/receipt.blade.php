@extends('layouts.app')

@section('title', 'Struk Kasir ' . $sale->sale_number . ' — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Navigation & Actions -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('store.index') }}" class="hover:underline">Operasional Toko</a>
        <span>/</span>
        <a href="{{ route('store.pos.terminal') }}" class="hover:underline">Kasir POS</a>
        <span>/</span>
        <span class="text-textMain font-mono font-bold">{{ $sale->sale_number }}</span>
      </div>
      <div class="flex items-center gap-3">
        <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
          Struk Pembayaran Transaksi
        </h1>
        <span class="px-2.5 py-1 rounded-xl text-xs font-bold border {{ $sale->status_badge_class }}">
          {{ $sale->status_label }}
        </span>
      </div>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('store.pos.terminal') }}" class="px-4 py-2.5 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
        <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Transaksi Baru (Kasir)</span>
      </a>

      <button type="button" onclick="window.print()" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        <span>Cetak Struk (Print)</span>
      </button>

      <a href="{{ route('store.pos.history') }}" class="px-3.5 py-2.5 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
        Riwayat Transaksi
      </a>
    </div>
  </div>

  @if($sale->isVoided())
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold flex items-center gap-3">
      <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
      <div>
        <p class="font-bold">Transaksi Ini Telah Dibatalkan (VOID)</p>
        <p class="text-[11px] text-rose-800">
          Dibatalkan pada {{ $sale->voided_at?->format('d/m/Y H:i') }} oleh {{ $sale->voidedByUser?->name }}. Alasan: "{{ $sale->void_reason }}".
        </p>
      </div>
    </div>
  @endif

  <!-- Thermal Paper Receipt Container (Max Width 400px, 80mm feel) -->
  <div id="thermalReceipt" class="printable-receipt max-w-md mx-auto bg-white rounded-3xl border border-[#ECEBF4] p-6 sm:p-8 shadow-sm space-y-5 font-mono text-xs">
    <!-- Header -->
    <div class="text-center space-y-1 border-b border-dashed border-gray-300 pb-4">
      <h2 class="font-display font-black text-lg text-sidebar tracking-tight uppercase">PasarHub Store</h2>
      <p class="text-[11px] text-textMuted">{{ $sale->location->name }}</p>
      <p class="text-[10px] text-textMuted font-sans">Sistem Operasional Pasar Terpadu Modern</p>
      <p class="text-[10px] text-textMuted">Telp: 021-8899-7700 • NPWP: 01.234.567.8-012.000</p>
    </div>

    <!-- Metadata Info -->
    <div class="space-y-1 text-[11px] border-b border-dashed border-gray-300 pb-3">
      <div class="flex justify-between">
        <span class="text-textMuted">No. Struk:</span>
        <span class="font-bold text-sidebar">{{ $sale->sale_number }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-textMuted">Tanggal:</span>
        <span>{{ $sale->posted_at?->format('d/m/Y H:i:s') }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-textMuted">Kasir:</span>
        <span class="font-bold">{{ $sale->cashier->name }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-textMuted">Pelanggan:</span>
        <span>{{ $sale->customer_name }} {{ $sale->member ? '(' . $sale->member->member_code . ')' : '' }}</span>
      </div>
    </div>

    <!-- Line Items Table -->
    <div class="space-y-2 border-b border-dashed border-gray-300 pb-3 text-[11px]">
      @foreach($sale->items as $item)
        <div class="space-y-0.5">
          <div class="font-bold text-textMain line-clamp-1">
            {{ $item->product->name }}
          </div>
          <div class="flex justify-between text-textMuted">
            <span>
              {{ number_format($item->quantity_input, 1) }} {{ $item->productUom?->uom?->symbol }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}
            </span>
            <span class="font-bold text-textMain">
              Rp {{ number_format($item->subtotal, 0, ',', '.') }}
            </span>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Totals -->
    <div class="space-y-1.5 text-xs border-b border-dashed border-gray-300 pb-3">
      <div class="flex justify-between text-textMuted text-[11px]">
        <span>Subtotal:</span>
        <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
      </div>
      @if($sale->discount_amount > 0)
        <div class="flex justify-between text-rose-600 text-[11px]">
          <span>Diskon Nota:</span>
          <span>-Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
        </div>
      @endif
      @if($sale->tax_amount > 0)
        <div class="flex justify-between text-textMuted text-[11px]">
          <span>PPN (11%):</span>
          <span>Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
        </div>
      @endif
      <div class="flex justify-between font-black text-sm pt-1 border-t border-gray-200 text-sidebar">
        <span>TOTAL BELANJA:</span>
        <span>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
      </div>
    </div>

    <!-- Payment & Change -->
    <div class="space-y-1 text-[11px] border-b border-dashed border-gray-300 pb-3">
      <div class="flex justify-between">
        <span class="text-textMuted">Metode Bayar:</span>
        <span class="font-extrabold uppercase text-sidebar">{{ $sale->payment_method }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-textMuted">Uang Diterima:</span>
        <span class="font-bold">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
      </div>
      <div class="flex justify-between font-bold text-emerald-700">
        <span>Kembalian:</span>
        <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
      </div>
    </div>

    <!-- Footer Note & Verification -->
    <div class="text-center space-y-2 pt-2 text-[10px] text-textMuted">
      <p class="leading-relaxed">
        Terima kasih atas kunjungan Anda di PasarHub!<br>
        Barang yang sudah dibeli tidak dapat ditukar kecuali kemasan cacat fisik saat serah terima.
      </p>
      <div class="font-mono text-[9px] text-gray-400">
        *** HASH: {{ substr(md5($sale->sale_number . $sale->created_at), 0, 16) }} ***
      </div>
    </div>
  </div>
</main>
@endsection

@push('styles')
<style>
@media print {
  /* 1. Page margin reset for Thermal 80mm printers */
  @page {
    margin: 0;
    size: 80mm auto;
  }

  /* 2. Reset full viewport & layout overflow traps */
  html, body {
    width: 100% !important;
    height: auto !important;
    min-height: 100% !important;
    overflow: visible !important;
    background: #FFFFFF !important;
    margin: 0 !important;
    padding: 0 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  body > div {
    height: auto !important;
    overflow: visible !important;
    padding: 0 !important;
    margin: 0 !important;
    display: block !important;
  }

  /* 3. Hide all surrounding layout chrome, headers, navigation, buttons & modals */
  #sidebarNav,
  #mobileNav,
  header,
  #topProgressBar,
  aside,
  .print\:hidden,
  .no-print,
  .swal2-container {
    display: none !important;
    visibility: hidden !important;
  }

  #scrollableWorkspace {
    overflow: visible !important;
    height: auto !important;
    padding: 0 !important;
    margin: 0 !important;
    display: block !important;
  }

  #workspaceContentWrapper,
  main {
    display: block !important;
    width: 100% !important;
    height: auto !important;
    padding: 0 !important;
    margin: 0 !important;
  }

  /* Hide everything by default, reveal only the thermal receipt */
  body * {
    visibility: hidden;
  }

  #thermalReceipt,
  #thermalReceipt * {
    visibility: visible !important;
  }

  /* 4. Format Thermal Receipt Specially for 80mm Paper */
  #thermalReceipt {
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
    width: 76mm !important;
    max-width: 80mm !important;
    margin: 0 auto !important;
    padding: 4mm 3mm !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    background: #FFFFFF !important;
    color: #000000 !important;
    font-family: 'Courier New', Courier, monospace, monospace !important;
    font-size: 11px !important;
    line-height: 1.35 !important;
  }

  /* High Contrast Monochrome for Thermal Heads */
  #thermalReceipt * {
    color: #000000 !important;
    border-color: #000000 !important;
    box-shadow: none !important;
    text-shadow: none !important;
  }

  #thermalReceipt h2 {
    font-size: 15px !important;
    font-weight: 900 !important;
    color: #000000 !important;
    letter-spacing: 0.5px !important;
  }

  #thermalReceipt .border-dashed {
    border-bottom: 1px dashed #000000 !important;
  }

  #thermalReceipt .border-gray-200,
  #thermalReceipt .border-gray-300 {
    border-color: #000000 !important;
  }
}
</style>
@endpush
