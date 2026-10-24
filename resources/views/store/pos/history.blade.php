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

  <!-- Top Breadcrumb & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('store.index') }}" class="hover:underline">Operasional Toko</a>
        <span>/</span>
        <a href="{{ route('store.pos.terminal') }}" class="hover:underline">Kasir POS</a>
        <span>/</span>
        <span class="text-textMain font-bold">Riwayat Transaksi</span>
      </div>
      <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
        Riwayat Struk & Pembatalan Transaksi (Void)
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Audit seluruh transaksi penjualan kasir di {{ $activeLocation->name }} ({{ $activeLocation->code }}).
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('store.pos.terminal') }}" class="px-4 py-2 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
        <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span>Buka Terminal Kasir</span>
      </a>
    </div>
  </div>

  <!-- Search & Filter Bar -->
  <div class="bg-white rounded-3xl p-4 border border-[#ECEBF4] shadow-sm">
    <form action="{{ route('store.pos.history') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
      <div class="sm:col-span-2">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari No. Struk (misal POS-...) atau Nama Pelanggan..."
               class="w-full px-3.5 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none">
      </div>

      <div>
        <select name="status" class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none">
          <option value="">-- Semua Status --</option>
          <option value="POSTED" {{ request('status') === 'POSTED' ? 'selected' : '' }}>POSTED (Berhasil)</option>
          <option value="VOIDED" {{ request('status') === 'VOIDED' ? 'selected' : '' }}>VOIDED (Dibatalkan)</option>
        </select>
      </div>

      <div class="flex items-center gap-2">
        <button type="submit" class="flex-1 py-2 bg-sidebar text-white font-bold text-xs rounded-xl hover:bg-sidebar/90 transition shadow-sm">
          Filter
        </button>
        <a href="{{ route('store.pos.history') }}" class="px-3 py-2 bg-panelBg text-sidebar font-bold text-xs rounded-xl hover:bg-gray-200 transition">
          Reset
        </a>
      </div>
    </form>
  </div>

  <!-- Sales Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm">
    @if($sales->isEmpty())
      <div class="py-12 text-center text-textMuted text-xs">
        Belum ada riwayat transaksi kasir yang ditemukan.
      </div>
    @else
      <div class="overflow-x-auto">
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
