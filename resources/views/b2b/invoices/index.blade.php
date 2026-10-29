@extends('layouts.app')

@section('title', 'Faktur & Piutang Grosir B2B — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Bar & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('b2b.index') }}" class="hover:text-sidebar transition">Kemitraan B2B</a>
        <span>/</span>
        <span class="text-sidebar font-bold">Faktur &amp; Piutang Tempo</span>
      </div>
      <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
        Faktur Penjualan &amp; Buku Piutang Grosir B2B
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Pemantauan tagihan tempo rekanan, status jatuh tempo (overdue), dan pencatatan pelunasan piutang.
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('b2b.orders.create') }}" class="px-4 py-2.5 bg-brandOrange hover:bg-orange-600 text-white text-xs font-bold rounded-2xl transition flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        <span>+ Order Grosir B2B</span>
      </a>
    </div>
  </div>



  <!-- 4 SUMMARY CARDS -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Total Nilai Faktur</span>
      <h3 class="font-display font-extrabold text-2xl text-textMain mt-2">Rp {{ number_format($stats['total_invoiced'], 0, ',', '.') }}</h3>
      <p class="text-xs text-textMuted mt-1">Seluruh tagihan yang diterbitkan</p>
    </div>

    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <span class="text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full inline-block">Total Pelunasan</span>
      <h3 class="font-display font-extrabold text-2xl text-emerald-600 mt-2">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</h3>
      <p class="text-xs text-textMuted mt-1">Dana piutang yang telah masuk</p>
    </div>

    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Sisa Piutang Berjalan</span>
      <h3 class="font-display font-extrabold text-2xl text-sidebar mt-2">Rp {{ number_format($stats['total_outstanding'], 0, ',', '.') }}</h3>
      <p class="text-xs text-textMuted mt-1">Mengurangi plafon mitra real-time</p>
    </div>

    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      @if($stats['overdue_count'] > 0)
        <span class="text-[11px] font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full inline-block">Piutang Overdue!</span>
        <h3 class="font-display font-extrabold text-2xl text-red-600 mt-2">{{ $stats['overdue_count'] }} Faktur</h3>
        <p class="text-xs text-red-500 font-semibold mt-1">Transaksi tempo mitra terkunci</p>
      @else
        <span class="text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full inline-block">Kolektibilitas Bersih</span>
        <h3 class="font-display font-extrabold text-2xl text-emerald-600 mt-2">0 Overdue</h3>
        <p class="text-xs text-textMuted mt-1">Tidak ada tunggakan jatuh tempo</p>
      @endif
    </div>
  </div>

  <!-- Filter & Search Bar -->
  <div class="bg-white rounded-2xl p-4 border border-[#ECEBF4] flex flex-col md:flex-row md:items-center justify-between gap-3">
    <form method="GET" action="{{ route('b2b.invoices.index') }}" class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
      <div class="flex items-center gap-1.5 bg-panelBg px-3 py-2 rounded-xl border border-[#ECEBF4] shrink-0">
        <span class="text-[11px] font-semibold text-textMuted whitespace-nowrap">Baris:</span>
        <select data-table-perpage="b2b-invoices-table" class="bg-transparent text-xs font-bold text-textMain focus:outline-none cursor-pointer">
          <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
          <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
          <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
          <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        </select>
      </div>

      <div class="relative flex-1 md:w-64">
        <input type="text" name="search" value="{{ request('search') }}"
               data-table-search="b2b-invoices-table"
               placeholder="Cari No. Faktur / Nama Mitra..." class="w-full text-xs rounded-xl border border-[#ECEBF4] pl-8 pr-3 py-2 focus:outline-none focus:border-sidebar">
        <svg class="w-4 h-4 text-textMuted absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </div>

      <select name="status" class="text-xs rounded-xl border border-[#ECEBF4] px-3 py-2 bg-white focus:outline-none focus:border-sidebar" onchange="this.form.submit()">
        <option value="">Semua Status Tagihan</option>
        <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>Terbuka (Belum Bayar)</option>
        <option value="PARTIALLY_PAID" {{ request('status') === 'PARTIALLY_PAID' ? 'selected' : '' }}>Sebagian (Cicil)</option>
        <option value="PAID" {{ request('status') === 'PAID' ? 'selected' : '' }}>Lunas (PAID)</option>
        <option value="OVERDUE" {{ request('status') === 'OVERDUE' ? 'selected' : '' }}>Jatuh Tempo (OVERDUE)</option>
      </select>

      <button type="submit" class="px-3.5 py-2 bg-sidebar text-white text-xs font-bold rounded-xl transition">
        Filter
      </button>

      @if(request()->hasAny(['search', 'status', 'per_page']))
        <a href="{{ route('b2b.invoices.index') }}" class="px-3 py-2 text-xs font-semibold text-textMuted hover:text-sidebar transition">
          Reset
        </a>
      @endif
    </form>
  </div>

  <!-- Invoices Table Container -->
  <div class="bg-white rounded-[26px] p-5 sm:p-6 border border-[#ECEBF4] space-y-4" data-async-table="b2b-invoices-table">
    <!-- Desktop View: Table -->
    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted">
            <th class="py-3 px-3 font-bold uppercase text-[10px]">No. Faktur &amp; Tanggal</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Mitra Rekanan</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Pesanan Asal</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Jatuh Tempo</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Total Tagihan</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Sisa Piutang</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Status</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px] text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @forelse($invoices as $inv)
            <tr class="hover:bg-panelBg/60 transition">
              <td class="py-3.5 px-3">
                <a href="{{ route('b2b.invoices.show', $inv) }}" class="font-mono font-bold text-sidebar hover:underline">
                  {{ $inv->invoice_number }}
                </a>
                <span class="block text-[10px] text-textMuted">{{ $inv->invoice_date->format('d/m/Y') }}</span>
              </td>
              <td class="py-3.5 px-3">
                <span class="font-bold text-textMain block">{{ $inv->member->name }}</span>
                <span class="text-[10px] font-mono text-textMuted">{{ $inv->member->member_code }}</span>
              </td>
              <td class="py-3.5 px-3 font-mono">
                @if($inv->order)
                  <a href="{{ route('b2b.orders.show', $inv->order) }}" class="text-purple-700 font-semibold hover:underline">
                    {{ $inv->order->order_number }}
                  </a>
                @else
                  <span class="text-textMuted">-</span>
                @endif
              </td>
              <td class="py-3.5 px-3">
                <span class="font-semibold {{ $inv->isOverdue() ? 'text-red-600 font-bold' : 'text-textMain' }}">
                  {{ $inv->due_date->format('d/m/Y') }}
                </span>
                @if($inv->isOverdue())
                  <span class="block text-[10px] text-red-500 font-bold">Terlambat!</span>
                @endif
              </td>
              <td class="py-3.5 px-3 font-mono font-bold text-textMain">
                Rp {{ number_format($inv->total_amount, 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-3 font-mono font-bold {{ $inv->outstanding_amount > 0 ? 'text-sidebar' : 'text-emerald-700' }}">
                Rp {{ number_format($inv->outstanding_amount, 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-3">
                @if($inv->status === 'PAID')
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                    LUNAS
                  </span>
                @elseif($inv->isOverdue())
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">
                    JATUH TEMPO
                  </span>
                @elseif($inv->status === 'PARTIALLY_PAID')
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                    SEBAGIAN
                  </span>
                @else
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">
                    TERBUKA (TEMPO)
                  </span>
                @endif
              </td>
              <td class="py-3.5 px-3 text-right">
                <div class="inline-flex items-center gap-1.5">
                  @if($inv->outstanding_amount > 0)
                    <button type="button" onclick="openPaymentModal({{ $inv->id }}, '{{ $inv->invoice_number }}', {{ $inv->outstanding_amount }})" class="px-3 py-1.5 rounded-xl bg-sidebar hover:bg-sidebarDark text-white font-bold text-[11px] transition shadow-sm">
                      Bayar
                    </button>
                  @endif
                  <a href="{{ route('b2b.invoices.show', $inv) }}" class="p-1.5 rounded-xl bg-panelBg hover:bg-sidebar hover:text-white text-sidebar border border-[#ECEBF4] transition" title="Rincian Faktur">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="py-10 text-center text-textMuted text-xs">
                Tidak ada data faktur atau piutang yang ditemukan.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Mobile View: Card Stack -->
    <div class="block md:hidden divide-y divide-[#ECEBF4]">
      @forelse($invoices as $inv)
        <div class="py-4 space-y-3">
          <div class="flex items-start justify-between gap-2">
            <div>
              <a href="{{ route('b2b.invoices.show', $inv) }}" class="font-mono font-bold text-sidebar hover:underline text-sm block">
                {{ $inv->invoice_number }}
              </a>
              <span class="text-[10px] text-textMuted">Tgl: {{ $inv->invoice_date->format('d/m/Y') }}</span>
            </div>
            <div>
              @if($inv->status === 'PAID')
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                  LUNAS
                </span>
              @elseif($inv->isOverdue())
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">
                  OVERDUE
                </span>
              @elseif($inv->status === 'PARTIALLY_PAID')
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                  SEBAGIAN
                </span>
              @else
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">
                  TEMPO
                </span>
              @endif
            </div>
          </div>

          <div class="grid grid-cols-2 gap-2 text-xs bg-panelBg/60 p-2.5 rounded-xl border border-[#ECEBF4]">
            <div>
              <span class="text-[10px] uppercase font-bold text-textMuted block">Mitra Rekanan</span>
              <span class="font-bold text-textMain text-xs block truncate">{{ $inv->member->name }}</span>
              <span class="text-[10px] font-mono text-textMuted">{{ $inv->member->member_code }}</span>
            </div>
            <div>
              <span class="text-[10px] uppercase font-bold text-textMuted block">Sisa Piutang</span>
              <span class="font-mono font-black text-sm block {{ $inv->outstanding_amount > 0 ? 'text-sidebar' : 'text-emerald-700' }}">
                Rp {{ number_format($inv->outstanding_amount, 0, ',', '.') }}
              </span>
              <span class="text-[10px] text-textMuted">dari Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="col-span-2 pt-1 border-t border-[#ECEBF4]/60 flex items-center justify-between">
              <div>
                <span class="text-[10px] uppercase font-bold text-textMuted block">Jatuh Tempo</span>
                <span class="text-[11px] font-semibold {{ $inv->isOverdue() ? 'text-red-600 font-bold' : 'text-textMain' }}">
                  {{ $inv->due_date->format('d/m/Y') }} {{ $inv->isOverdue() ? '(Terlambat!)' : '' }}
                </span>
              </div>
              @if($inv->order)
                <div class="text-right">
                  <span class="text-[10px] uppercase font-bold text-textMuted block">Pesanan</span>
                  <span class="text-[11px] font-mono font-semibold text-purple-700">{{ $inv->order->order_number }}</span>
                </div>
              @endif
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-1">
            @if($inv->outstanding_amount > 0)
              <button type="button" onclick="openPaymentModal({{ $inv->id }}, '{{ $inv->invoice_number }}', {{ $inv->outstanding_amount }})" class="px-3.5 py-1.5 rounded-xl bg-sidebar hover:bg-sidebarDark text-white font-bold text-xs transition shadow-sm">
                Catat Bayar
              </button>
            @endif
            <a href="{{ route('b2b.invoices.show', $inv) }}" class="px-3 py-1.5 rounded-xl bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs border border-[#ECEBF4] transition">
              Detail Faktur
            </a>
          </div>
        </div>
      @empty
        <div class="py-10 text-center text-textMuted text-xs">
          Tidak ada data faktur atau piutang yang ditemukan.
        </div>
      @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-3 border-t border-[#ECEBF4]">
      {{ $invoices->links() }}
    </div>
  </div>

  <!-- PAYMENT ALLOCATION MODAL -->
  <div id="paymentModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl space-y-4">
      <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
        <div>
          <span class="text-[10px] text-textMuted font-bold uppercase">Catat Pelunasan Piutang</span>
          <h3 class="font-display font-bold text-base text-textMain" id="modalInvoiceNum">Faktur #</h3>
        </div>
        <button type="button" onclick="closePaymentModal()" class="text-textMuted hover:text-textMain">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <form id="paymentForm" method="POST" action="" class="space-y-4 text-xs">
        @csrf
        <div>
          <label class="block font-bold text-textMain mb-1">Sisa Tagihan yang Harus Dilunasi</label>
          <p class="font-mono font-extrabold text-base text-sidebar" id="modalMaxAmount">Rp 0</p>
        </div>

        <div>
          <label class="block font-bold text-textMain mb-1">Nominal Pembayaran (Rp) <span class="text-red-500">*</span></label>
          <input type="number" id="payAmountInput" name="amount" min="1" step="0.01" required class="w-full font-mono text-sm rounded-xl border border-[#ECEBF4] p-2.5 focus:outline-none focus:border-sidebar">
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
          <button type="button" onclick="closePaymentModal()" class="px-4 py-2 font-bold text-textMuted hover:text-sidebar transition">
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

<script>
  function openPaymentModal(invId, invNumber, maxOutstanding) {
    const modal = document.getElementById('paymentModal');
    const form = document.getElementById('paymentForm');
    form.action = `/b2b/invoices/${invId}/pay`;

    document.getElementById('modalInvoiceNum').innerText = invNumber;
    document.getElementById('modalMaxAmount').innerText = 'Rp ' + Number(maxOutstanding).toLocaleString('id-ID');
    document.getElementById('payAmountInput').max = maxOutstanding;
    document.getElementById('payAmountInput').value = maxOutstanding;

    modal.classList.remove('hidden');
  }

  function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
  }
</script>
@endsection
