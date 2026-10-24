@extends('layouts.app')

@section('title', 'Kartu Stok & Audit Ledger — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Breadcrumb & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('warehouse.index') }}" class="hover:underline">Operasional Gudang</a>
        <span>/</span>
        <span class="text-textMain font-bold">Kartu Stok & Audit Ledger</span>
      </div>
      <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
        Kartu Stok & Riwayat Mutasi 5-Warna
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Buku besar mutasi fisik append-only yang tidak dapat dihapus, lengkap dengan snapshot Moving Average HPP.
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('warehouse.index') }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
        ← Kembali ke Gudang
      </a>
      <a href="{{ route('warehouse.po.index') }}" class="px-3.5 py-2 bg-sidebar text-white font-bold text-xs rounded-xl hover:bg-sidebar/95 transition shadow-sm">
        Daftar PO
      </a>
    </div>
  </div>

  <!-- Real-time Balances & Valuation Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm">
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Lokasi Aktif</span>
      <h3 class="font-bold text-base text-textMain mt-0.5">{{ $activeLocation?->name ?? 'Semua Lokasi' }}</h3>
      <p class="text-xs text-textMuted mt-1">{{ $totalOnHandSku }} SKU memiliki saldo fisik aktif.</p>
    </div>

    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm">
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Total Mutasi Tercatat</span>
      <h3 class="font-bold text-xl text-sidebar mt-0.5">{{ $movements->total() }} Peristiwa</h3>
      <p class="text-xs text-textMuted mt-1">Audit transaksi append-only terikat ke user & dokumen.</p>
    </div>

    <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm">
      <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Valuasi Persediaan (HPP)</span>
      <h3 class="font-black text-xl text-emerald-700 mt-0.5">
        @if($canViewHpp)
          Rp {{ number_format($totalValuation, 0, ',', '.') }}
        @else
          *** [Terkunci]
        @endif
      </h3>
      <p class="text-xs text-textMuted mt-1">Berdasarkan moving average harga beli aktual.</p>
    </div>
  </div>

  <!-- 5-Color Semantic Filter Bar (PRD Section 4.5) -->
  <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold">
    <a href="{{ route('warehouse.ledger.index', array_merge(request()->except('semantic_status', 'page'), [])) }}"
       class="px-3.5 py-1.5 rounded-full transition whitespace-nowrap {{ !request()->filled('semantic_status') ? 'bg-sidebar text-white' : 'bg-white text-textMuted hover:text-textMain border border-[#ECEBF4]' }}">
      Semua Mutasi
    </a>
    <a href="{{ route('warehouse.ledger.index', array_merge(request()->except('semantic_status', 'page'), ['semantic_status' => 'GREEN'])) }}"
       class="px-3.5 py-1.5 rounded-full transition whitespace-nowrap flex items-center gap-1.5 {{ request('semantic_status') === 'GREEN' ? 'bg-[#10B981] text-white' : 'bg-white text-[#10B981] hover:bg-emerald-50 border border-emerald-200' }}">
      <span class="w-2 h-2 rounded-full bg-[#10B981] {{ request('semantic_status') === 'GREEN' ? 'bg-white' : '' }}"></span>
      <span>Masuk / Inbound (Hijau)</span>
    </a>
    <a href="{{ route('warehouse.ledger.index', array_merge(request()->except('semantic_status', 'page'), ['semantic_status' => 'BLUE'])) }}"
       class="px-3.5 py-1.5 rounded-full transition whitespace-nowrap flex items-center gap-1.5 {{ request('semantic_status') === 'BLUE' ? 'bg-[#3B82F6] text-white' : 'bg-white text-[#3B82F6] hover:bg-blue-50 border border-blue-200' }}">
      <span class="w-2 h-2 rounded-full bg-[#3B82F6] {{ request('semantic_status') === 'BLUE' ? 'bg-white' : '' }}"></span>
      <span>Transfer / Internal (Biru)</span>
    </a>
    <a href="{{ route('warehouse.ledger.index', array_merge(request()->except('semantic_status', 'page'), ['semantic_status' => 'PURPLE'])) }}"
       class="px-3.5 py-1.5 rounded-full transition whitespace-nowrap flex items-center gap-1.5 {{ request('semantic_status') === 'PURPLE' ? 'bg-[#8B5CF6] text-white' : 'bg-white text-[#8B5CF6] hover:bg-purple-50 border border-purple-200' }}">
      <span class="w-2 h-2 rounded-full bg-[#8B5CF6] {{ request('semantic_status') === 'PURPLE' ? 'bg-white' : '' }}"></span>
      <span>Penjualan POS / B2B (Ungu)</span>
    </a>
    <a href="{{ route('warehouse.ledger.index', array_merge(request()->except('semantic_status', 'page'), ['semantic_status' => 'ORANGE'])) }}"
       class="px-3.5 py-1.5 rounded-full transition whitespace-nowrap flex items-center gap-1.5 {{ request('semantic_status') === 'ORANGE' ? 'bg-[#F59E0B] text-white' : 'bg-white text-[#F59E0B] hover:bg-amber-50 border border-amber-200' }}">
      <span class="w-2 h-2 rounded-full bg-[#F59E0B] {{ request('semantic_status') === 'ORANGE' ? 'bg-white' : '' }}"></span>
      <span>Peringatan / Emergency (Oranye)</span>
    </a>
    <a href="{{ route('warehouse.ledger.index', array_merge(request()->except('semantic_status', 'page'), ['semantic_status' => 'RED'])) }}"
       class="px-3.5 py-1.5 rounded-full transition whitespace-nowrap flex items-center gap-1.5 {{ request('semantic_status') === 'RED' ? 'bg-[#EF4444] text-white' : 'bg-white text-[#EF4444] hover:bg-rose-50 border border-rose-200' }}">
      <span class="w-2 h-2 rounded-full bg-[#EF4444] {{ request('semantic_status') === 'RED' ? 'bg-white' : '' }}"></span>
      <span>Kritis / Selisih Opname (Merah)</span>
    </a>
  </div>

  <!-- Search & Filter Form -->
  <div class="bg-white rounded-2xl border border-[#ECEBF4] p-4 shadow-sm">
    <form action="{{ route('warehouse.ledger.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 text-xs">
      @if(request('semantic_status'))
        <input type="hidden" name="semantic_status" value="{{ request('semantic_status') }}">
      @endif

      <!-- Search -->
      <div>
        <label class="block font-bold text-textMain mb-1">Cari Produk / SKU</label>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Nama produk atau SKU..."
               class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">
      </div>

      <!-- Product Filter -->
      <div>
        <label class="block font-bold text-textMain mb-1">Pilih Produk</label>
        <select name="product_id" class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">
          <option value="">-- Semua Produk --</option>
          @foreach($products as $p)
            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
              {{ $p->name }} ({{ $p->sku }})
            </option>
          @endforeach
        </select>
      </div>

      <!-- Location Filter -->
      <div>
        <label class="block font-bold text-textMain mb-1">Lokasi</label>
        <select name="location_id" class="w-full px-3 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">
          <option value="">-- Semua Lokasi --</option>
          @foreach($locations as $loc)
            <option value="{{ $loc->id }}" {{ (request('location_id') ?? $activeLocation?->id) == $loc->id ? 'selected' : '' }}>
              {{ $loc->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Filter Buttons -->
      <div class="flex items-end gap-2">
        <button type="submit" class="px-4 py-2 bg-sidebar text-white text-xs font-bold rounded-xl hover:bg-sidebar/90 transition flex-1">
          Filter
        </button>
        @if(request()->anyFilled(['search', 'product_id', 'location_id', 'semantic_status']))
          <a href="{{ route('warehouse.ledger.index') }}" class="px-3 py-2 text-xs font-bold text-brandCoral hover:underline text-center">
            Reset
          </a>
        @endif
      </div>
    </form>
  </div>

  <!-- Ledger Movement Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <h3 class="font-bold text-sm text-textMain">Buku Jurnal Mutasi Barang (Append-Only)</h3>
      <span class="text-xs text-textMuted font-semibold">{{ $movements->total() }} Baris Ledger</span>
    </div>

    @if($movements->isEmpty())
      <div class="py-12 text-center text-textMuted text-xs">
        Tidak ada data pergerakan stok yang cocok dengan kriteria filter.
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3 px-3 min-w-[130px]">Waktu Kejadian</th>
              <th class="py-3 px-3 min-w-[120px]">Arus Status</th>
              <th class="py-3 px-3 min-w-[180px]">Produk & SKU</th>
              <th class="py-3 px-3 min-w-[130px]">Dokumen Rujukan</th>
              <th class="py-3 px-3 text-right min-w-[110px]">Perubahan Qty</th>
              <th class="py-3 px-3 text-right min-w-[110px]">Saldo Setelah</th>
              @if($canViewHpp)
                <th class="py-3 px-3 text-right min-w-[120px]">Snapshot HPP</th>
              @endif
              <th class="py-3 px-3 min-w-[110px]">Aktor Pencatat</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @foreach($movements as $m)
              <tr class="hover:bg-panelBg/50 transition">
                <td class="py-3 px-3 text-textMuted font-mono text-[11px]">
                  {{ $m->occurred_at?->format('d/m/Y H:i:s') }}
                </td>

                <!-- Semantic Status Dot & Label -->
                <td class="py-3 px-3">
                  <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $m->semantic_color }}"></span>
                    <span class="font-bold text-[11px] text-textMain">
                      {{ $m->movement_type }}
                    </span>
                  </div>
                  <span class="text-[10px] text-textMuted block mt-0.5">
                    {{ $m->location->name }}
                  </span>
                </td>

                <td class="py-3 px-3">
                  <span class="font-bold text-textMain block">{{ $m->product->name }}</span>
                  <span class="text-[10px] text-brandOrange font-mono">{{ $m->product->sku }}</span>
                </td>

                <td class="py-3 px-3 font-mono text-[11px]">
                  @if($m->operation?->document_number)
                    <span class="font-bold text-sidebar">{{ $m->operation->document_number }}</span>
                  @else
                    <span class="text-textMuted">-</span>
                  @endif
                </td>

                <!-- Delta Qty -->
                <td class="py-3 px-3 text-right font-extrabold text-xs {{ $m->delta_qty_base >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                  {{ $m->delta_qty_base >= 0 ? '+' : '' }}{{ number_format($m->delta_qty_base, 1) }}
                  <span class="text-[10px] font-normal text-textMuted">{{ $m->product->baseUom?->symbol }}</span>
                </td>

                <!-- Balance After -->
                <td class="py-3 px-3 text-right font-black text-sidebar text-xs">
                  {{ number_format($m->balance_after_qty, 1) }}
                  <span class="text-[10px] font-normal text-textMuted">{{ $m->product->baseUom?->symbol }}</span>
                </td>

                <!-- Snapshot HPP -->
                @if($canViewHpp)
                  <td class="py-3 px-3 text-right font-bold text-textMain">
                    Rp {{ number_format($m->unit_cost_snapshot, 0, ',', '.') }}
                  </td>
                @endif

                <td class="py-3 px-3 text-textMuted text-[11px]">
                  {{ $m->operation?->actor?->name ?? 'Sistem' }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-4 pt-4 border-t border-[#ECEBF4]">
        {{ $movements->links() }}
      </div>
    @endif
  </div>
</main>
@endsection

@section('right_panel')
  @include('partials.right-panel')
@endsection
