@extends('layouts.app')

@section('title', 'Pesanan B2B #' . $order->order_number . ' — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Bar & Breadcrumb -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('b2b.index') }}" class="hover:text-sidebar transition">Kemitraan B2B</a>
        <span>/</span>
        <a href="{{ route('b2b.orders.index') }}" class="hover:text-sidebar transition">Pesanan</a>
        <span>/</span>
        <span class="text-sidebar font-bold">{{ $order->order_number }}</span>
      </div>
      <div class="flex items-center gap-3">
        <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
          Pesanan Grosir {{ $order->order_number }}
        </h1>
        @if($order->status === 'RESERVED' || $order->status === 'READY_FOR_PICKUP')
          <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200 inline-flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-purple-600 animate-pulse"></span>
            Stok Teralokasi (Siap Serah Terima)
          </span>
        @elseif($order->status === 'HANDED_OVER')
          <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Selesai / Diserahterimakan
          </span>
        @elseif($order->status === 'CANCELLED')
          <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800">
            Dibatalkan
          </span>
        @else
          <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-800">{{ $order->status }}</span>
        @endif
      </div>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('b2b.orders.index') }}" class="px-4 py-2 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition inline-flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Daftar Pesanan</span>
      </a>

      @if($order->canBeCancelled())
        <button type="button" onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-2xl border border-red-200 transition inline-flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          <span>Batalkan Pesanan</span>
        </button>
      @endif
    </div>
  </div>



  <!-- STATUS STEPPER (Flat Solid Progression) -->
  <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4]">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
      <!-- Step 1: Placed -->
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">
          ✓
        </div>
        <div>
          <span class="text-[10px] text-textMuted uppercase font-bold">Langkah 1</span>
          <p class="text-xs font-bold text-textMain">Pesanan Masuk</p>
          <span class="text-[10px] text-textMuted">{{ $order->placed_at?->format('d/m/Y H:i') }}</span>
        </div>
      </div>

      <!-- Step 2: Reserved -->
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-2xl {{ in_array($order->status, ['RESERVED', 'READY_FOR_PICKUP', 'HANDED_OVER']) ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center font-bold text-xs shrink-0">
          2
        </div>
        <div>
          <span class="text-[10px] text-textMuted uppercase font-bold">Langkah 2</span>
          <p class="text-xs font-bold {{ in_array($order->status, ['RESERVED', 'READY_FOR_PICKUP', 'HANDED_OVER']) ? 'text-purple-700' : 'text-textMuted' }}">Stok Direservasi</p>
          <span class="text-[10px] text-textMuted">Terkunci di saldo fisik</span>
        </div>
      </div>

      <!-- Step 3: Pickup Ticket -->
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-2xl {{ in_array($order->status, ['READY_FOR_PICKUP', 'HANDED_OVER']) ? 'bg-emerald-100 text-emerald-700' : ($order->status === 'RESERVED' ? 'bg-purple-600 text-white animate-pulse' : 'bg-gray-100 text-gray-400') }} flex items-center justify-center font-bold text-xs shrink-0">
          3
        </div>
        <div>
          <span class="text-[10px] text-textMuted uppercase font-bold">Langkah 3</span>
          <p class="text-xs font-bold text-textMain">Tiket QR Diterbitkan</p>
          <span class="text-[10px] text-textMuted font-mono">{{ $order->pickupTicket?->ticket_number ?? 'Tiket Aktif' }}</span>
        </div>
      </div>

      <!-- Step 4: Handed Over -->
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-2xl {{ $order->status === 'HANDED_OVER' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center font-bold text-xs shrink-0">
          {{ $order->status === 'HANDED_OVER' ? '✓' : '4' }}
        </div>
        <div>
          <span class="text-[10px] text-textMuted uppercase font-bold">Langkah 4</span>
          <p class="text-xs font-bold {{ $order->status === 'HANDED_OVER' ? 'text-emerald-700' : 'text-textMuted' }}">Serah Terima &amp; Faktur</p>
          <span class="text-[10px] text-textMuted">{{ $order->handed_over_at ? $order->handed_over_at->format('d/m/Y H:i') : 'Menunggu pengambilan' }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- LEFT 2 COLS: ORDER DETAILS & LINE ITEMS -->
    <div class="lg:col-span-2 space-y-6">
      <!-- Order Info Metadata -->
      <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-4">
        <h3 class="font-display font-bold text-base text-textMain">Rincian Informasi Pesanan</h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
          <div>
            <span class="text-[10px] text-textMuted font-bold uppercase">Mitra Grosir B2B</span>
            <p class="font-bold text-textMain text-sm mt-0.5">{{ $order->member->name }}</p>
            <span class="text-[10px] text-purple-700 font-semibold font-mono">{{ $order->member->member_code }}</span>
          </div>

          <div>
            <span class="text-[10px] text-textMuted font-bold uppercase">Lokasi Serah Terima</span>
            <p class="font-bold text-sidebar text-sm mt-0.5">{{ $order->fulfillmentLocation->name }}</p>
            <span class="text-[10px] text-textMuted font-mono">{{ $order->fulfillmentLocation->code }}</span>
          </div>

          <div>
            <span class="text-[10px] text-textMuted font-bold uppercase">Ketentuan Pembayaran</span>
            <p class="font-bold text-sm mt-0.5 {{ $order->payment_term === 'CASH' ? 'text-emerald-700' : 'text-amber-700' }}">
              {{ $order->payment_term === 'CASH' ? 'TUNAI (CBD)' : $order->payment_term }}
            </p>
            @if($order->due_date)
              <span class="text-[10px] text-textMuted">Jatuh tempo: {{ $order->due_date->format('d/m/Y') }}</span>
            @endif
          </div>

          <div>
            <span class="text-[10px] text-textMuted font-bold uppercase">Dibuat Oleh</span>
            <p class="font-semibold text-textMain mt-0.5">{{ $order->creator->name }}</p>
            <span class="text-[10px] text-textMuted">{{ $order->created_at->format('d/m/Y H:i') }}</span>
          </div>

          <div>
            <span class="text-[10px] text-textMuted font-bold uppercase">Diskon Rekanan</span>
            <p class="font-semibold text-sidebar mt-0.5">{{ $order->member->tier?->name ?? 'NON-TIER' }}</p>
            <span class="text-[10px] text-textMuted">{{ (float)($order->member->tier?->discount_percent ?? 0) }}% tier reward</span>
          </div>

          <div>
            <span class="text-[10px] text-textMuted font-bold uppercase">Catatan</span>
            <p class="text-textMuted mt-0.5 italic">{{ $order->notes ?: '-' }}</p>
          </div>
        </div>
      </div>

      <!-- Line Items Table -->
      <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-4">
        <h3 class="font-display font-bold text-base text-textMain">Daftar Barang yang Direservasi</h3>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="border-b border-[#ECEBF4] text-textMuted">
                <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Produk &amp; SKU</th>
                <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Satuan</th>
                <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Kuantitas</th>
                <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Harga Satuan</th>
                <th class="py-2.5 px-3 font-bold uppercase text-[10px] text-right">Subtotal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#ECEBF4]">
              @foreach($order->items as $it)
                <tr class="hover:bg-panelBg/50 transition">
                  <td class="py-3 px-3">
                    <div class="flex items-center gap-2.5">
                      <x-product-thumbnail :product="$it->product" size="sm" />
                      <div>
                        <span class="font-bold text-textMain block">{{ $it->product->name }}</span>
                        <span class="text-[10px] font-mono text-textMuted">{{ $it->product->sku }}</span>
                      </div>
                    </div>
                  </td>
                  <td class="py-3 px-3">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#F3F0FA] text-sidebar border border-[#ECEBF4]">
                      {{ $it->productUom?->uom?->name ?? $it->product->baseUom?->name ?? 'UOM' }}
                    </span>
                  </td>
                  <td class="py-3 px-3 font-mono font-bold text-textMain">
                    {{ (float)$it->quantity_input }}
                    <span class="text-[10px] text-textMuted font-normal">({{ (float)$it->quantity_base }} {{ $it->product->baseUom?->name }})</span>
                  </td>
                  <td class="py-3 px-3 font-mono text-textMain">
                    Rp {{ number_format($it->unit_price, 0, ',', '.') }}
                  </td>
                  <td class="py-3 px-3 font-mono font-bold text-sidebar text-right">
                    Rp {{ number_format($it->subtotal, 0, ',', '.') }}
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr class="border-t-2 border-[#ECEBF4]">
                <td colspan="4" class="py-3 px-3 font-bold text-right text-xs text-textMain">Total Nilai Pesanan:</td>
                <td class="py-3 px-3 font-display font-extrabold text-base text-sidebar text-right">
                  Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <!-- RIGHT COL: ONE-TIME QR PICKUP TICKET & INVOICE -->
    <div class="space-y-6">
      <!-- ONE-TIME QR TICKET CARD -->
      <div class="bg-white rounded-[26px] p-6 border-2 border-purple-200 shadow-sm space-y-4 text-center">
        <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3 text-left">
          <div>
            <span class="text-[10px] text-purple-700 font-bold uppercase tracking-wider">One-Time Token</span>
            <h3 class="font-display font-bold text-base text-textMain">Tiket Serah Terima QR</h3>
          </div>
          @if($order->pickupTicket)
            @if($order->pickupTicket->status === 'ISSUED')
              <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                AKTIF (Siap Scan)
              </span>
            @elseif($order->pickupTicket->status === 'REDEEMED')
              <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                TERPAKAI
              </span>
            @else
              <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-800">
                {{ $order->pickupTicket->status }}
              </span>
            @endif
          @endif
        </div>

        @if($order->pickupTicket)
          <!-- SVG QR Vector Representation -->
          <div class="py-2">
            {!! $order->pickupTicket->generateQrSvg(190) !!}
          </div>

          <div class="space-y-1">
            <span class="text-[10px] text-textMuted uppercase font-bold">Nomor Tiket Pickup</span>
            <p class="font-mono font-extrabold text-base text-sidebar tracking-wider">
              {{ $order->pickupTicket->ticket_number }}
            </p>
            @if($order->pickupTicket->raw_token_preview)
              <p class="font-mono text-xs text-purple-700 font-bold bg-purple-50 py-1 px-3 rounded-lg border border-purple-100 inline-block">
                Token: {{ $order->pickupTicket->raw_token_preview }}
              </p>
            @endif
          </div>

          <div class="p-3 rounded-xl bg-panelBg text-[11px] text-textMuted text-left space-y-1">
            <div class="flex justify-between">
              <span>Lokasi Penjemputan:</span>
              <span class="font-bold text-textMain">{{ $order->fulfillmentLocation->name }}</span>
            </div>
            <div class="flex justify-between">
              <span>Berlaku Hingga:</span>
              <span class="font-semibold text-textMain">{{ $order->pickupTicket->expires_at?->format('d/m/Y H:i') ?? '-' }}</span>
            </div>
            @if($order->pickupTicket->status === 'REDEEMED')
              <div class="flex justify-between text-emerald-700 font-bold border-t border-[#ECEBF4] pt-1 mt-1">
                <span>Diserahkan Kepada:</span>
                <span>{{ $order->pickupTicket->redeemedBy?->name ?? 'Petugas' }}</span>
              </div>
              <div class="flex justify-between text-emerald-700 text-[10px]">
                <span>Waktu Serah Terima:</span>
                <span>{{ $order->pickupTicket->redeemed_at?->format('d/m/Y H:i') }}</span>
              </div>
            @endif
          </div>

          <!-- Direct Redeem Action if staff is logged in and ticket is ISSUED -->
          @if($order->pickupTicket->status === 'ISSUED')
            <form method="POST" action="{{ route('b2b.pickup.redeem') }}" data-confirm="Konfirmasi serah terima barang untuk pesanan ini? Stok akan otomatis diselesaikan." data-confirm-title="Konfirmasi Serah Terima" data-confirm-btn="Ya, Serah Terima">
              @csrf
              <input type="hidden" name="token" value="{{ $order->pickupTicket->ticket_number }}">
              <button type="submit" class="w-full py-2.5 px-4 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>Serah Terima di Lokasi Ini</span>
              </button>
            </form>
          @endif
        @else
          <p class="py-8 text-xs text-textMuted">Tiket pickup tidak ditemukan untuk pesanan ini.</p>
        @endif
      </div>

      <!-- INVOICE & RECEIVABLE CARD (If Handed Over) -->
      @if($order->invoice)
        <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-4">
          <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
            <div>
              <span class="text-[10px] text-textMuted font-bold uppercase">Buku Piutang</span>
              <h3 class="font-display font-bold text-sm text-textMain">Faktur Penjualan B2B</h3>
            </div>
            <a href="{{ route('b2b.invoices.show', $order->invoice) }}" class="text-xs font-bold text-sidebar hover:underline">
              Lihat Faktur →
            </a>
          </div>

          <div class="space-y-2 text-xs">
            <div class="flex justify-between">
              <span class="text-textMuted">No. Faktur:</span>
              <span class="font-mono font-bold text-sidebar">{{ $order->invoice->invoice_number }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-textMuted">Status Tagihan:</span>
              @if($order->invoice->status === 'PAID')
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">LUNAS</span>
              @elseif($order->invoice->status === 'OVERDUE')
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">JATUH TEMPO</span>
              @else
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">TERBUKA (TEMPO)</span>
              @endif
            </div>
            <div class="flex justify-between">
              <span class="text-textMuted">Sisa Piutang:</span>
              <span class="font-mono font-bold text-textMain">Rp {{ number_format($order->invoice->outstanding_amount, 0, ',', '.') }}</span>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>

  <!-- CANCEL ORDER CONFIRMATION MODAL -->
  <div id="cancelModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl space-y-4">
      <div class="flex items-center gap-3 text-red-600">
        <div class="w-10 h-10 rounded-2xl bg-red-100 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
          <h3 class="font-display font-bold text-base text-textMain">Batalkan Pesanan B2B?</h3>
          <p class="text-xs text-textMuted">Stok yang direservasi akan otomatis dikembalikan ke saldo fisik.</p>
        </div>
      </div>

      <form method="POST" action="{{ route('b2b.orders.cancel', $order) }}" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-bold text-textMain mb-1">Alasan Pembatalan <span class="text-red-500">*</span></label>
          <input type="text" name="reason" required placeholder="Mis. Mitra mengajukan revisi kuantitas..." class="w-full text-xs rounded-xl border border-[#ECEBF4] p-2.5 focus:outline-none focus:border-sidebar">
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-2">
          <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-textMuted hover:text-sidebar transition">
            Kembali
          </button>
          <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
            Konfirmasi Batalkan
          </button>
        </div>
      </form>
    </div>
  </div>
</main>
@endsection
