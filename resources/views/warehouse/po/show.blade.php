@extends('layouts.app')

@section('title', "PO {$purchaseOrder->po_number} — PasarHub")

@section('content')
<main class="flex-1 w-full space-y-6">
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

  <!-- Top Breadcrumb & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('warehouse.index') }}" class="hover:underline">Operasional Gudang</a>
        <span>/</span>
        <a href="{{ route('warehouse.po.index') }}" class="hover:underline">Purchase Orders</a>
        <span>/</span>
        <span class="text-textMain font-mono font-bold">{{ $purchaseOrder->po_number }}</span>
      </div>
      <div class="flex items-center gap-3">
        <h1 class="font-display text-xl sm:text-2xl font-extrabold text-sidebar tracking-tight font-mono">
          {{ $purchaseOrder->po_number }}
        </h1>
        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $purchaseOrder->status_badge_class }}">
          {{ $purchaseOrder->status_label }}
        </span>
      </div>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('warehouse.po.index') }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
        ← Daftar PO
      </a>
      @if($canReceive)
        <a href="{{ route('warehouse.receipts.create', $purchaseOrder->id) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
          </svg>
          <span>Catat Penerimaan Barang (GRN)</span>
        </a>
      @endif
    </div>
  </div>

  <!-- PO Info & Supplier Grid -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- Supplier Card -->
    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm space-y-2">
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Pemasok / Suplayer</span>
      <h3 class="font-bold text-base text-textMain">{{ $purchaseOrder->supplier->name }}</h3>
      <p class="text-xs text-textMuted font-mono">Kode: {{ $purchaseOrder->supplier->supplier_code }}</p>
      <div class="pt-2 border-t border-[#ECEBF4] text-xs space-y-1">
        <p class="text-textMain"><span class="text-textMuted">PIC:</span> {{ $purchaseOrder->supplier->contact_person ?? '-' }}</p>
        <p class="text-textMain"><span class="text-textMuted">Telp:</span> {{ $purchaseOrder->supplier->phone ?? '-' }}</p>
        <p class="text-textMuted text-[11px]">{{ $purchaseOrder->supplier->address ?? '-' }}</p>
      </div>
    </div>

    <!-- Logistics & Destination -->
    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm space-y-2">
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Tujuan & Pengiriman</span>
      <h3 class="font-bold text-base text-textMain">{{ $purchaseOrder->destinationLocation->name }}</h3>
      <p class="text-xs text-textMuted">Tipe: <span class="font-semibold text-sidebar">{{ $purchaseOrder->destinationLocation->type }}</span></p>
      <div class="pt-2 border-t border-[#ECEBF4] text-xs space-y-1">
        <p class="text-textMain"><span class="text-textMuted">Tgl Pesan:</span> {{ $purchaseOrder->ordered_at?->format('d/m/Y H:i') }}</p>
        <p class="text-textMain"><span class="text-textMuted">Estimasi Tiba:</span> {{ $purchaseOrder->expected_delivery_at?->format('d/m/Y') ?? 'Sesuai Jadwal' }}</p>
        <p class="text-textMain"><span class="text-textMuted">Dibuat Oleh:</span> {{ $purchaseOrder->creator?->name }}</p>
      </div>
    </div>

    <!-- Receiving Progress -->
    @php
      $totalOrderedBase = $purchaseOrder->items->sum('ordered_base_qty');
      $totalReceivedBase = $purchaseOrder->items->sum('quantity_received_base');
      $percent = $totalOrderedBase > 0 ? min(100, round(($totalReceivedBase / $totalOrderedBase) * 100)) : 0;
    @endphp
    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm flex flex-col justify-between">
      <div>
        <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Progres Pemenuhan Fisik</span>
        <div class="flex items-baseline gap-2 mt-1">
          <span class="text-2xl font-black text-sidebar">{{ $percent }}%</span>
          <span class="text-xs text-textMuted font-bold">Terpenuhi</span>
        </div>
        <div class="w-full bg-[#EAE8F2] rounded-full h-2.5 mt-2 overflow-hidden">
          <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ $percent }}%"></div>
        </div>
        <p class="text-[11px] text-textMuted mt-2">
          {{ number_format($totalReceivedBase, 1) }} dari {{ number_format($totalOrderedBase, 1) }} total satuan dasar diterima.
        </p>
      </div>

      <div class="pt-2 border-t border-[#ECEBF4] flex items-center justify-between text-xs">
        <span class="text-textMuted font-medium">Nilai Total:</span>
        <span class="font-extrabold text-sidebar">
          @if($canViewHpp)
            Rp {{ number_format($purchaseOrder->grand_total, 0, ',', '.') }}
          @else
            *** [Terkunci]
          @endif
        </span>
      </div>
    </div>
  </div>

  <!-- Items Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <h3 class="font-bold text-sm text-textMain">Rincian Barang yang Dipesan</h3>
      <span class="text-xs text-textMuted font-semibold">{{ $purchaseOrder->items->count() }} Baris Item</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
            <th class="py-3 px-3">Produk & SKU</th>
            <th class="py-3 px-3">Satuan Pesan</th>
            <th class="py-3 px-3 text-right">Dipesan</th>
            <th class="py-3 px-3 text-right">Diterima (Base)</th>
            <th class="py-3 px-3 text-right">Sisa Diterima</th>
            <th class="py-3 px-3 text-right">Harga Beli Satuan</th>
            <th class="py-3 px-3 text-right">Subtotal</th>
            <th class="py-3 px-3 text-center">Status Item</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @foreach($purchaseOrder->items as $item)
            <tr class="hover:bg-panelBg/40 transition">
              <td class="py-3 px-3">
                <span class="font-bold text-textMain block">{{ $item->product->name }}</span>
                <span class="text-[10px] text-brandOrange font-mono">{{ $item->product->sku }}</span>
              </td>
              <td class="py-3 px-3">
                <span class="font-semibold text-textMain">{{ $item->productUom?->uom?->name }} [{{ $item->productUom?->uom?->symbol }}]</span>
                @if(parseFloat($item->factor_to_base_snapshot) > 1)
                  <span class="block text-[10px] text-textMuted font-mono">1 {{ $item->productUom?->uom?->symbol }} = {{ parseFloat($item->factor_to_base_snapshot) }} {{ $item->product->baseUom?->symbol }}</span>
                @endif
              </td>
              <td class="py-3 px-3 text-right font-extrabold text-sidebar">
                {{ number_format($item->quantity_ordered, 1) }}
                <span class="text-[10px] font-normal text-textMuted">{{ $item->productUom?->uom?->symbol }}</span>
              </td>
              <td class="py-3 px-3 text-right font-bold text-emerald-700">
                {{ number_format($item->quantity_received_base, 1) }}
                <span class="text-[10px] font-normal text-textMuted">{{ $item->product->baseUom?->symbol }}</span>
              </td>
              <td class="py-3 px-3 text-right font-bold {{ $item->remaining_base_qty > 0 ? 'text-amber-700' : 'text-gray-400' }}">
                {{ number_format($item->remaining_input_qty, 1) }}
                <span class="text-[10px] font-normal text-textMuted">{{ $item->productUom?->uom?->symbol }}</span>
              </td>
              <td class="py-3 px-3 text-right font-semibold text-textMain">
                @if($canViewHpp)
                  Rp {{ number_format($item->unit_purchase_price, 0, ',', '.') }}
                @else
                  ***
                @endif
              </td>
              <td class="py-3 px-3 text-right font-extrabold text-sidebar">
                @if($canViewHpp)
                  Rp {{ number_format($item->line_total, 0, ',', '.') }}
                @else
                  ***
                @endif
              </td>
              <td class="py-3 px-3 text-center">
                @if($item->isFullyReceived())
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Lengkap
                  </span>
                @elseif($item->quantity_received_base > 0)
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    Parsial
                  </span>
                @else
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                    Menunggu
                  </span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Related Goods Receipts (Surat Jalan Masuk) -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <div>
        <h3 class="font-bold text-sm text-textMain">Histori Surat Jalan Masuk (Goods Receipts)</h3>
        <p class="text-xs text-textMuted mt-0.5">Semua pencatatan fisik barang yang masuk dari PO ini.</p>
      </div>
      @if($canReceive)
        <a href="{{ route('warehouse.receipts.create', $purchaseOrder->id) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-sm">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Terima Barang</span>
        </a>
      @endif
    </div>

    @if($purchaseOrder->goodsReceipts->isEmpty())
      <div class="py-6 text-center text-textMuted text-xs">
        Belum ada barang yang diterima untuk Purchase Order ini.
      </div>
    @else
      <div class="divide-y divide-[#ECEBF4]">
        @foreach($purchaseOrder->goodsReceipts as $grn)
          <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <div class="flex items-center gap-2">
                  <a href="{{ route('warehouse.receipts.show', $grn->id) }}" class="font-extrabold text-xs text-sidebar hover:text-brandOrange font-mono">
                    {{ $grn->receipt_number }}
                  </a>
                  <span class="text-[10px] px-2 py-0.5 bg-gray-100 rounded text-textMuted font-bold">
                    DO: {{ $grn->supplier_do_number ?? '-' }}
                  </span>
                </div>
                <p class="text-[11px] text-textMuted mt-0.5">
                  Diterima oleh <span class="font-semibold text-textMain">{{ $grn->creator?->name }}</span> pada {{ $grn->posted_at?->format('d/m/Y H:i') }}
                  @if($grn->notes)
                    • <span class="italic">{{ $grn->notes }}</span>
                  @endif
                </p>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <span class="text-xs font-bold text-emerald-700">
                {{ $grn->items->count() }} item masuk
              </span>
              <a href="{{ route('warehouse.receipts.show', $grn->id) }}" class="px-2.5 py-1 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-[11px] rounded-lg transition border border-[#ECEBF4]">
                Lihat Bukti Masuk →
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
  @include('partials.right-panel')
@endsection
