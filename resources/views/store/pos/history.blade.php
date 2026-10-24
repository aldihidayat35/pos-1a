@extends('layouts.app')

@section('title', 'Riwayat Transaksi Kasir POS — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Flash Success / Error -->
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

  <!-- Top Breadcrumb & Header (Clean UI with Mobile Tooltip) -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('store.index') }}" class="hover:underline">Operasional Toko</a>
        <span>/</span>
        <a href="{{ route('store.pos.terminal') }}" class="hover:underline">Kasir POS</a>
        <span>/</span>
        <span class="text-textMain font-bold">Riwayat Transaksi</span>
      </div>
      <div class="flex items-center gap-2 flex-wrap">
        <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
          Riwayat Struk & Void
        </h1>

        <!-- Mobile-Only Info Tooltip Trigger (ℹ️) -->
        <div class="relative md:hidden inline-flex items-center">
          <button type="button" 
                  onclick="toggleHeaderTooltip('posHistoryHeaderPopover', event)" 
                  class="w-6 h-6 rounded-full bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white flex items-center justify-center transition p-1 shadow-xs"
                  title="Lihat Keterangan Halaman"
                  aria-label="Info Halaman">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
            </svg>
          </button>

          <!-- Mobile Popover Floating Bubble -->
          <div id="posHistoryHeaderPopover" class="header-popover hidden fixed inset-x-4 top-20 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs">
            <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
              <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
                <span>Informasi Riwayat Transaksi</span>
              </div>
              <button type="button" onclick="closeHeaderTooltip('posHistoryHeaderPopover')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
            </div>
            <p class="text-[11px] text-textMuted leading-relaxed">
              Audit seluruh transaksi penjualan kasir di {{ $activeLocation->name }} ({{ $activeLocation->code }}).
            </p>
          </div>
        </div>
      </div>

      <!-- Desktop Subtitle (Hidden on Mobile) -->
      <p class="hidden md:block text-xs text-textMuted mt-0.5">
        Audit seluruh transaksi penjualan kasir di {{ $activeLocation->name }} ({{ $activeLocation->code }}).
      </p>
    </div>

    <div class="flex items-center gap-2 shrink-0">
      <a href="{{ route('store.pos.terminal') }}" class="px-3.5 py-2 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl shadow-sm transition inline-flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span>Terminal POS</span>
      </a>
    </div>
  </div>

  <!-- Search & Filter Bar -->
  <div class="bg-white rounded-3xl p-4 border border-[#ECEBF4] shadow-sm">
    <form action="{{ route('store.pos.history') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3 text-xs items-center">
      <div class="sm:col-span-2">
        <input type="text" name="search" value="{{ request('search') }}"
               data-table-search="pos-history-table"
               placeholder="Cari No. Struk (misal POS-...) atau Nama Pelanggan..."
               class="w-full px-3.5 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none">
      </div>

      <div>
        <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none">
          <option value="">-- Semua Status --</option>
          <option value="POSTED" {{ request('status') === 'POSTED' ? 'selected' : '' }}>POSTED (Berhasil)</option>
          <option value="VOIDED" {{ request('status') === 'VOIDED' ? 'selected' : '' }}>VOIDED (Dibatalkan)</option>
        </select>
      </div>

      <div class="flex items-center gap-1.5 bg-panelBg px-3 py-2 rounded-xl border border-[#ECEBF4]">
        <span class="text-[11px] font-semibold text-textMuted whitespace-nowrap">Baris:</span>
        <select data-table-perpage="pos-history-table" class="bg-transparent text-xs font-bold text-textMain focus:outline-none cursor-pointer">
          <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
          <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12</option>
          <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
          <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        </select>
      </div>

      <div class="flex items-center gap-2">
        <button type="submit" class="flex-1 py-2 bg-sidebar text-white font-bold text-xs rounded-xl hover:bg-sidebar/90 transition shadow-sm">
          Filter
        </button>
        @if(request()->anyFilled(['search', 'status', 'per_page']))
          <a href="{{ route('store.pos.history') }}" class="px-2.5 py-2 text-xs font-bold text-brandCoral hover:underline text-center">
            Reset
          </a>
        @endif
      </div>
    </form>
  </div>

  <!-- Sales Table Container -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 sm:p-6 shadow-sm" data-async-table="pos-history-table">
    @if($sales->isEmpty())
      <div class="py-12 text-center text-textMuted text-xs">
        Belum ada riwayat transaksi kasir yang ditemukan.
      </div>
    @else
      <!-- Desktop View: Table -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3 px-3">No. Struk</th>
              <th class="py-3 px-3">Waktu & Kasir</th>
              <th class="py-3 px-3">Pelanggan</th>
              <th class="py-3 px-3">Rincian Item</th>
              <th class="py-3 px-3 text-right">Total Transaksi</th>
              <th class="py-3 px-3 text-center">Metode</th>
              <th class="py-3 px-3 text-center">Status</th>
              <th class="py-3 px-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @foreach($sales as $s)
              <tr class="hover:bg-panelBg/50 transition">
                <td class="py-3.5 px-3 font-mono font-extrabold text-sidebar">
                  <a href="{{ route('store.pos.receipt', $s->id) }}" class="hover:text-brandOrange hover:underline">
                    {{ $s->sale_number }}
                  </a>
                </td>
                <td class="py-3.5 px-3 text-textMuted text-[11px]">
                  <span>{{ $s->posted_at?->format('d/m/Y H:i') }}</span>
                  <span class="block text-[10px] text-textMain font-semibold">Kasir: {{ $s->cashier->name }}</span>
                </td>
                <td class="py-3.5 px-3">
                  <span class="font-bold text-textMain block">{{ $s->customer_name }}</span>
                  @if($s->member)
                    <span class="text-[10px] text-brandOrange font-mono font-bold">{{ $s->member->member_code }}</span>
                  @endif
                </td>
                <td class="py-3.5 px-3 text-[11px]">
                  @foreach($s->items->take(2) as $it)
                    <div class="truncate max-w-[180px]">
                      {{ $it->product->name }} ({{ number_format($it->quantity_input, 1) }})
                    </div>
                  @endforeach
                  @if($s->items->count() > 2)
                    <span class="text-[10px] text-textMuted">+{{ $s->items->count() - 2 }} item lainnya</span>
                  @endif
                </td>
                <td class="py-3.5 px-3 text-right font-mono font-black text-sidebar text-xs">
                  Rp {{ number_format($s->total_amount, 0, ',', '.') }}
                </td>
                <td class="py-3.5 px-3 text-center">
                  <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-panelBg text-sidebar uppercase">
                    {{ $s->payment_method }}
                  </span>
                </td>
                <td class="py-3.5 px-3 text-center">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $s->status_badge_class }}">
                    {{ $s->status_label }}
                  </span>
                </td>
                <td class="py-3.5 px-3 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <a href="{{ route('store.pos.receipt', $s->id) }}" class="px-2.5 py-1 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-[11px] rounded-lg transition border border-[#ECEBF4]">
                      Struk
                    </a>

                    @if($s->isPosted() && $canVoid)
                      <button type="button" onclick="openVoidModal({{ $s->id }}, '{{ $s->sale_number }}', 'Rp {{ number_format($s->total_amount, 0, ',', '.') }}')"
                              class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] rounded-lg transition border border-rose-200">
                        Void
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Mobile View: Card Stack -->
      <div class="block md:hidden divide-y divide-[#ECEBF4]">
        @foreach($sales as $s)
          <div class="py-4 space-y-3">
            <div class="flex items-start justify-between gap-2">
              <div>
                <a href="{{ route('store.pos.receipt', $s->id) }}" class="font-extrabold text-sidebar hover:text-brandOrange hover:underline text-sm font-mono block">
                  {{ $s->sale_number }}
                </a>
                <span class="text-[10px] text-textMuted">{{ $s->posted_at?->format('d/m/Y H:i') }} • Kasir: {{ $s->cashier->name }}</span>
              </div>
              <div class="text-right shrink-0">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border block {{ $s->status_badge_class }}">
                  {{ $s->status_label }}
                </span>
                <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-panelBg text-sidebar uppercase mt-1 inline-block">
                  {{ $s->payment_method }}
                </span>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs bg-panelBg/60 p-2.5 rounded-xl border border-[#ECEBF4]">
              <div>
                <span class="text-[10px] uppercase font-bold text-textMuted block">Pelanggan</span>
                <span class="font-bold text-textMain text-xs block truncate">{{ $s->customer_name }}</span>
                @if($s->member)
                  <span class="text-[10px] text-brandOrange font-mono font-bold">{{ $s->member->member_code }}</span>
                @endif
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-textMuted block">Total Belanja</span>
                <span class="font-mono font-black text-sidebar text-sm">
                  Rp {{ number_format($s->total_amount, 0, ',', '.') }}
                </span>
              </div>
              <div class="col-span-2 pt-1.5 border-t border-[#ECEBF4]/60">
                <span class="text-[10px] uppercase font-bold text-textMuted block">Daftar Item</span>
                <div class="text-[11px] text-textMain space-y-0.5 mt-0.5">
                  @foreach($s->items->take(2) as $it)
                    <div class="truncate">
                      • {{ $it->product->name }} ({{ number_format($it->quantity_input, 1) }})
                    </div>
                  @endforeach
                  @if($s->items->count() > 2)
                    <span class="text-[10px] text-textMuted italic">+{{ $s->items->count() - 2 }} item lainnya</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
              <a href="{{ route('store.pos.receipt', $s->id) }}" class="px-3 py-1.5 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
                Lihat Struk
              </a>
              @if($s->isPosted() && $canVoid)
                <button type="button" onclick="openVoidModal({{ $s->id }}, '{{ $s->sale_number }}', 'Rp {{ number_format($s->total_amount, 0, ',', '.') }}')"
                        class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-xl transition border border-rose-200">
                  Batalkan (Void)
                </button>
              @endif
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4 pt-4 border-t border-[#ECEBF4]">
        {{ $sales->links() }}
      </div>
    @endif
  </div>
</main>

<!-- Void Confirmation Modal -->
<div id="voidModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl border border-[#ECEBF4] max-w-md w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-200">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <h3 class="font-display font-bold text-base text-rose-700 flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
        <span>Batalkan Transaksi (Void Struk)</span>
      </h3>
      <button type="button" onclick="closeVoidModal()" class="text-textMuted hover:text-textMain p-1 rounded-lg">✕</button>
    </div>

    <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-900 space-y-1">
      <p class="font-bold">Konsekuensi Pembatalan Atomik (Fase 5):</p>
      <p class="text-[11px] leading-relaxed text-rose-800">
        Pembatalan akan <strong>mengembalikan saldo fisik</strong> barang ke rak toko, membukukan mutasi pembalik dengan <strong>Kode Warna Merah (#EF4444)</strong>, serta menerbitkan notifikasi ke <strong>Audit Trail Anomali</strong>.
      </p>
    </div>

    <form id="voidForm" action="" method="POST" class="space-y-4">
      @csrf
      <div>
        <span class="text-textMuted text-[10px] uppercase font-bold tracking-wider block mb-1">Struk yang Dibatalkan:</span>
        <div class="p-3 bg-panelBg rounded-xl font-mono text-xs font-bold text-sidebar flex justify-between">
          <span id="voidSaleNumberDisplay">POS-...</span>
          <span id="voidSaleAmountDisplay" class="text-rose-700">Rp 0</span>
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-textMain mb-1.5">Alasan Pembatalan Wajib <span class="text-rose-500">*</span></label>
        <textarea name="void_reason" required rows="3" placeholder="Contoh: Pembeli salah memilih varian barang dan minta refund tunai langsung di kasir..."
                  class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar"></textarea>
      </div>

      <div class="flex items-center justify-end gap-2 pt-2">
        <button type="button" onclick="closeVoidModal()" class="px-3.5 py-2 bg-panelBg text-sidebar font-bold text-xs rounded-xl hover:bg-gray-200 transition">
          Batal
        </button>
        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
          Konfirmasi Void Transaksi
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function openVoidModal(saleId, saleNumber, saleAmount) {
    document.getElementById('voidForm').action = `/store/pos/${saleId}/void`;
    document.getElementById('voidSaleNumberDisplay').textContent = saleNumber;
    document.getElementById('voidSaleAmountDisplay').textContent = saleAmount;
    document.getElementById('voidModal').classList.remove('hidden');
  }

  function closeVoidModal() {
    document.getElementById('voidModal').classList.add('hidden');
  }
</script>
@endsection

@section('right_panel')
  @include('store.partials.right-panel')
@endsection
