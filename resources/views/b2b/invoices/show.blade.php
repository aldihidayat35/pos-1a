@extends('layouts.app')

@section('title', 'Faktur B2B #' . $invoice->invoice_number . ' — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Bar & Breadcrumb -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('b2b.index') }}" class="hover:text-sidebar transition">Kemitraan B2B</a>
        <span>/</span>
        <a href="{{ route('b2b.invoices.index') }}" class="hover:text-sidebar transition">Faktur &amp; Piutang</a>
        <span>/</span>
        <span class="text-sidebar font-bold">{{ $invoice->invoice_number }}</span>
      </div>
      <div class="flex items-center gap-3">
        <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
          Faktur Penjualan {{ $invoice->invoice_number }}
        </h1>
        @if($invoice->status === 'PAID')
          <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
            LUNAS (PAID)
          </span>
        @elseif($invoice->isOverdue())
          <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">
            JATUH TEMPO (OVERDUE)
          </span>
        @elseif($invoice->status === 'PARTIALLY_PAID')
          <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
            DIBAYAR SEBAGIAN
          </span>
        @else
          <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
            TERBUKA (TEMPO)
          </span>
        @endif
      </div>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('b2b.invoices.index') }}" class="px-4 py-2 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition inline-flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Buku Piutang</span>
      </a>
      <button onclick="window.print()" class="px-4 py-2 bg-panelBg hover:bg-[#E6E4F0] text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        <span>Cetak Faktur</span>
      </button>
    </div>
  </div>

  @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-3">
      <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <div>{{ session('success') }}</div>
    </div>
  @endif

  <!-- INVOICE SHEET (Printable Clean Layout) -->
  <div class="bg-white rounded-[26px] p-6 sm:p-8 border border-[#ECEBF4] space-y-6">
    <!-- Invoice Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start gap-4 border-b border-[#ECEBF4] pb-6">
      <div>
        <div class="inline-flex items-center gap-2 bg-[#342A6E] text-white px-3 py-1 rounded-full text-xs font-bold mb-2">
          <span>PasarHub B2B Commerce</span>
        </div>
        <h2 class="font-display font-extrabold text-2xl text-sidebar">FAKTUR PENJUALAN GROSIR</h2>
        <p class="text-xs text-textMuted mt-0.5">Sistem Manajemen Operasional Pasar Terpadu</p>
      </div>

      <div class="text-left sm:text-right space-y-1 text-xs">
        <p class="font-mono font-extrabold text-base text-sidebar">{{ $invoice->invoice_number }}</p>
        <p class="text-textMuted">Tanggal Terbit: <span class="font-bold text-textMain">{{ $invoice->invoice_date->format('d F Y') }}</span></p>
        <p class="text-textMuted">Jatuh Tempo: <span class="font-bold {{ $invoice->isOverdue() ? 'text-red-600 font-extrabold' : 'text-textMain' }}">{{ $invoice->due_date->format('d F Y') }}</span></p>
      </div>
    </div>

    <!-- Bill To & Order Reference -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
      <div class="p-4 rounded-2xl bg-panelBg border border-[#ECEBF4] space-y-1.5">
        <span class="text-[10px] text-textMuted font-bold uppercase tracking-wider">Tertagih Kepada (Mitra B2B):</span>
        <h4 class="font-display font-bold text-base text-textMain">{{ $invoice->member->name }}</h4>
        <p class="text-textMuted font-mono">{{ $invoice->member->member_code }} • Tier: {{ $invoice->member->tier?->name ?? 'NON-TIER' }}</p>
        <p class="text-textMuted">{{ $invoice->member->address ?? 'Alamat terdaftar pada master member' }}</p>
        <p class="text-textMuted">Telp: {{ $invoice->member->phone ?? '-' }}</p>
      </div>

      <div class="p-4 rounded-2xl bg-panelBg border border-[#ECEBF4] space-y-1.5">
        <span class="text-[10px] text-textMuted font-bold uppercase tracking-wider">Referensi Transaksi:</span>
        @if($invoice->order)
          <p class="font-mono font-bold text-sm text-sidebar">
            <a href="{{ route('b2b.orders.show', $invoice->order) }}" class="hover:underline">
              Pesanan #{{ $invoice->order->order_number }}
            </a>
          </p>
          <p class="text-textMuted">Lokasi Serah Terima: <span class="font-bold text-textMain">{{ $invoice->order->fulfillmentLocation->name }}</span></p>
          <p class="text-textMuted">Ketentuan Bayar: <span class="font-bold text-textMain">{{ $invoice->order->payment_term }}</span></p>
        @else
          <p class="text-textMuted italic">Faktur mandiri tanpa referensi pesanan B2B.</p>
        @endif
      </div>
    </div>

    <!-- Items Table (if order exists) -->
    @if($invoice->order && $invoice->order->items->isNotEmpty())
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted">
              <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Barang</th>
              <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Satuan</th>
              <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Kuantitas</th>
              <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Harga Satuan</th>
              <th class="py-2.5 px-3 font-bold uppercase text-[10px] text-right">Subtotal</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @foreach($invoice->order->items as $item)
              <tr>
                <td class="py-3 px-3 font-bold text-textMain">{{ $item->product->name }}</td>
                <td class="py-3 px-3">{{ $item->productUom?->uom?->name ?? 'UOM' }}</td>
                <td class="py-3 px-3 font-mono">{{ (float)$item->quantity_input }}</td>
                <td class="py-3 px-3 font-mono">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="py-3 px-3 font-mono font-bold text-sidebar text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    <!-- Totals & Balances -->
    <div class="border-t-2 border-[#ECEBF4] pt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div class="text-xs text-textMuted">
        <p class="font-semibold text-textMain">Kebijakan Jatuh Tempo:</p>
        <p class="text-[11px]">Pembayaran setelah tanggal jatuh tempo otomatis mengunci order tempo baru untuk mitra ini.</p>
      </div>

      <div class="w-full sm:w-72 space-y-2 text-right text-xs">
        <div class="flex justify-between">
          <span class="text-textMuted">Total Tagihan:</span>
          <span class="font-mono font-bold text-textMain">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-emerald-700">
          <span>Sudah Dibayar:</span>
          <span class="font-mono font-bold">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
        </div>
        <div class="border-t border-[#ECEBF4] pt-2 flex justify-between items-baseline font-bold">
          <span>Sisa Piutang:</span>
          <span class="font-display font-extrabold text-xl {{ $invoice->outstanding_amount > 0 ? 'text-sidebar' : 'text-emerald-700' }}">
            Rp {{ number_format($invoice->outstanding_amount, 0, ',', '.') }}
          </span>
        </div>
      </div>
    </div>

    <!-- PAYMENT HISTORY -->
    <div class="border-t border-[#ECEBF4] pt-6 space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="font-display font-bold text-base text-textMain">Riwayat Alokasi Pelunasan Piutang</h3>
        @if($invoice->outstanding_amount > 0)
          <button type="button" onclick="document.getElementById('payInvoiceModal').classList.remove('hidden')" class="px-4 py-2 bg-sidebar hover:bg-sidebarDark text-white font-bold text-xs rounded-xl transition shadow-sm">
            + Catat Pembayaran
          </button>
        @endif
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted">
              <th class="py-2 px-3 font-bold uppercase text-[10px]">Waktu Pembayaran</th>
              <th class="py-2 px-3 font-bold uppercase text-[10px]">Metode</th>
              <th class="py-2 px-3 font-bold uppercase text-[10px]">No. Referensi / Bukti</th>
              <th class="py-2 px-3 font-bold uppercase text-[10px]">Dicatat Oleh</th>
              <th class="py-2 px-3 font-bold uppercase text-[10px] text-right">Nominal Masuk</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @forelse($invoice->payments as $pay)
              <tr>
                <td class="py-2.5 px-3 font-mono text-textMuted">{{ $pay->paid_at->format('d/m/Y H:i') }}</td>
                <td class="py-2.5 px-3 font-bold text-textMain">{{ $pay->payment_method }}</td>
                <td class="py-2.5 px-3 font-mono text-sidebar">{{ $pay->reference_number ?: '-' }}</td>
                <td class="py-2.5 px-3 text-textMain">{{ $pay->recordedBy->name }}</td>
                <td class="py-2.5 px-3 font-mono font-bold text-emerald-700 text-right">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-6 text-center text-textMuted text-xs">
                  Belum ada catatan pembayaran untuk faktur ini.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- PAYMENT MODAL -->
  <div id="payInvoiceModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl space-y-4">
      <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
        <div>
          <span class="text-[10px] text-textMuted font-bold uppercase">Pelunasan Piutang</span>
          <h3 class="font-display font-bold text-base text-textMain">{{ $invoice->invoice_number }}</h3>
        </div>
        <button type="button" onclick="document.getElementById('payInvoiceModal').classList.add('hidden')" class="text-textMuted hover:text-textMain">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <form method="POST" action="{{ route('b2b.invoices.pay', $invoice) }}" class="space-y-4 text-xs">
        @csrf
        <div>
          <label class="block font-bold text-textMain mb-1">Maksimal Pelunasan</label>
          <p class="font-mono font-extrabold text-base text-sidebar">Rp {{ number_format($invoice->outstanding_amount, 0, ',', '.') }}</p>
        </div>

        <div>
          <label class="block font-bold text-textMain mb-1">Nominal Pembayaran (Rp) <span class="text-red-500">*</span></label>
          <input type="number" name="amount" value="{{ $invoice->outstanding_amount }}" max="{{ $invoice->outstanding_amount }}" min="1" step="0.01" required class="w-full font-mono text-sm rounded-xl border border-[#ECEBF4] p-2.5 focus:outline-none focus:border-sidebar">
        </div>

        <div>
          <label class="block font-bold text-textMain mb-1">Metode Pembayaran <span class="text-red-500">*</span></label>
          <select name="payment_method" class="w-full rounded-xl border border-[#ECEBF4] p-2.5 bg-white focus:outline-none focus:border-sidebar" required>
            <option value="TRANSFER">Transfer Bank</option>
            <option value="CASH">Tunai (Cash)</option>
            <option value="GIRO">Bilyet Giro / Cek</option>
            <option value="OTHER">Lainnya</option>
          </select>
        </div>

        <div>
          <label class="block font-bold text-textMain mb-1">Nomor Referensi Transaksi / Bukti Transfer</label>
          <input type="text" name="reference_number" placeholder="Mis. TRF-BCA-992144" class="w-full rounded-xl border border-[#ECEBF4] p-2.5 focus:outline-none focus:border-sidebar">
        </div>

        <div>
          <label class="block font-bold text-textMain mb-1">Catatan Tambahan</label>
          <input type="text" name="notes" placeholder="Catatan opsional..." class="w-full rounded-xl border border-[#ECEBF4] p-2.5 focus:outline-none focus:border-sidebar">
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-2">
          <button type="button" onclick="document.getElementById('payInvoiceModal').classList.add('hidden')" class="px-4 py-2 font-bold text-textMuted hover:text-sidebar transition">
            Batal
          </button>
          <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-sm">
            Simpan Pelunasan
          </button>
        </div>
      </form>
    </div>
  </div>
</main>
@endsection
