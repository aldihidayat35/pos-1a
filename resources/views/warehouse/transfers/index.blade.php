@extends('layouts.app')

@section('title', 'Daftar Transfer Antar Lokasi — PasarHub')

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

  <!-- Top Breadcrumb & Action Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('warehouse.index') }}" class="hover:underline">Operasional Gudang</a>
        <span>/</span>
        <span class="text-textMain font-bold">Transfer Barang (Gudang ↔ Toko)</span>
      </div>
      <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
        Pengiriman Transfer Antar Lokasi
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Distribusi stok dari gudang menuju toko pasar dengan pencatatan saldo transit virtual (anti-stok hilang).
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('warehouse.index') }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
        ← Kembali ke Gudang
      </a>
      @if($user->hasPermission('transfer.dispatch') || $user->hasPermission('inventory.manage') || $user->isSuperAdmin())
        <a href="{{ route('warehouse.transfers.create') }}" class="px-4 py-2 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-sm">
          <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Kirim Transfer Baru</span>
        </a>
      @endif
    </div>
  </div>

  <!-- Status Filter Pills -->
  <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold">
    <a href="{{ route('warehouse.transfers.index') }}"
       class="px-3.5 py-1.5 rounded-full transition whitespace-nowrap {{ !request()->filled('status') ? 'bg-sidebar text-white' : 'bg-white text-textMuted hover:text-textMain border border-[#ECEBF4]' }}">
      Semua Status ({{ $statusCounts['ALL'] }})
    </a>
    <a href="{{ route('warehouse.transfers.index', ['status' => 'DISPATCHED']) }}"
       class="px-3.5 py-1.5 rounded-full transition whitespace-nowrap {{ request('status') === 'DISPATCHED' ? 'bg-blue-600 text-white' : 'bg-white text-blue-700 hover:bg-blue-50 border border-blue-200' }}">
      Dalam Perjalanan / Transit ({{ $statusCounts['DISPATCHED'] }})
    </a>
    <a href="{{ route('warehouse.transfers.index', ['status' => 'RECEIVED']) }}"
       class="px-3.5 py-1.5 rounded-full transition whitespace-nowrap {{ request('status') === 'RECEIVED' ? 'bg-emerald-600 text-white' : 'bg-white text-emerald-700 hover:bg-emerald-50 border border-emerald-200' }}">
      Lengkap Diterima Toko ({{ $statusCounts['RECEIVED'] }})
    </a>
  </div>

  <!-- Transfers Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm">
    @if($transfers->isEmpty())
      <div class="py-12 text-center text-textMuted text-xs">
        Belum ada catatan transfer barang antar lokasi.
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3 px-3">No. Transfer</th>
              <th class="py-3 px-3">Asal → Tujuan</th>
              <th class="py-3 px-3">Item Barang</th>
              <th class="py-3 px-3">Status</th>
              <th class="py-3 px-3">Waktu Dispatch</th>
              <th class="py-3 px-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @foreach($transfers as $trf)
              <tr class="hover:bg-panelBg/60 transition">
                <td class="py-3.5 px-3 font-extrabold text-sidebar">
                  <a href="{{ route('warehouse.transfers.show', $trf->id) }}" class="text-brandOrange hover:underline font-mono">
                    {{ $trf->transfer_number }}
                  </a>
                  <span class="block text-[10px] text-textMuted font-normal">
                    Oleh: {{ $trf->creator?->name }}
                  </span>
                </td>
                <td class="py-3.5 px-3">
                  <span class="font-bold text-textMain">{{ $trf->fromLocation->name }}</span>
                  <span class="text-textMuted block text-[11px]">→ {{ $trf->toLocation->name }}</span>
                </td>
                <td class="py-3.5 px-3">
                  <div class="flex flex-col gap-0.5">
                    @foreach($trf->items->take(2) as $item)
                      <span class="text-textMain text-[11px]">
                        • {{ $item->product->name }} ({{ number_format($item->requested_qty_input, 0) }} {{ $item->productUom?->uom?->symbol }})
                      </span>
                    @endforeach
                    @if($trf->items->count() > 2)
                      <span class="text-[10px] text-textMuted italic">+{{ $trf->items->count() - 2 }} item lainnya</span>
                    @endif
                  </div>
                </td>
                <td class="py-3.5 px-3">
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-block {{ $trf->status_badge_class }}">
                    {{ $trf->status_label }}
                  </span>
                </td>
                <td class="py-3.5 px-3 text-textMuted text-[11px]">
                  {{ $trf->dispatched_at?->format('d/m/Y H:i') ?? '-' }}
                </td>
                <td class="py-3.5 px-3 text-center">
                  <a href="{{ route('warehouse.transfers.show', $trf->id) }}" class="px-2.5 py-1 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-[11px] rounded-lg transition border border-[#ECEBF4]">
                    Detail
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-4 pt-4 border-t border-[#ECEBF4]">
        {{ $transfers->links() }}
      </div>
    @endif
  </div>
</main>
@endsection

@section('right_panel')
  @include('warehouse.partials.right-panel')
@endsection
