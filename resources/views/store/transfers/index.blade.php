@extends('layouts.app')

@section('title', 'Kiriman Transfer Masuk — PasarHub Toko')

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

  <!-- Top Breadcrumb & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('store.index') }}" class="hover:underline">Operasional Toko</a>
        <span>/</span>
        <span class="text-textMain font-bold">Transfer Masuk</span>
      </div>
      <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
        Kiriman Pasokan Transfer dari Gudang
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Penerimaan barang fisik dari gudang pusat yang telah tiba di toko pasar.
      </p>
    </div>

    <a href="{{ route('store.index') }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
      ← Kembali ke Toko
    </a>
  </div>

  <!-- Transfers List -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm">
    @if($transfers->isEmpty())
      <div class="py-12 text-center text-textMuted text-xs">
        Tidak ada kiriman transfer masuk untuk lokasi toko ini.
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3 px-3">No. Transfer</th>
              <th class="py-3 px-3">Asal Pengirim</th>
              <th class="py-3 px-3">Daftar Barang</th>
              <th class="py-3 px-3">Status</th>
              <th class="py-3 px-3">Waktu Dispatch</th>
              <th class="py-3 px-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @foreach($transfers as $trf)
              <tr class="hover:bg-panelBg/60 transition">
                <td class="py-3.5 px-3 font-extrabold text-sidebar font-mono">
                  <a href="{{ route('store.transfers.show', $trf->id) }}" class="text-brandOrange hover:underline">
                    {{ $trf->transfer_number }}
                  </a>
                </td>
                <td class="py-3.5 px-3">
                  <span class="font-bold text-textMain">{{ $trf->fromLocation->name }}</span>
                </td>
                <td class="py-3.5 px-3">
                  <div class="flex flex-col gap-0.5">
                    @foreach($trf->items as $item)
                      <span class="text-textMain text-[11px]">
                        • {{ $item->product->name }} ({{ number_format($item->requested_qty_input, 0) }} {{ $item->productUom?->uom?->symbol }})
                      </span>
                    @endforeach
                  </div>
                </td>
                <td class="py-3.5 px-3">
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-block {{ $trf->status_badge_class }}">
                    {{ $trf->status_label }}
                  </span>
                </td>
                <td class="py-3.5 px-3 text-textMuted text-[11px]">
                  {{ $trf->dispatched_at?->format('d/m/Y H:i') }}
                </td>
                <td class="py-3.5 px-3 text-center">
                  @if(in_array($trf->status, ['DISPATCHED', 'PARTIALLY_RECEIVED']))
                    <a href="{{ route('store.transfers.show', $trf->id) }}" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded-lg transition inline-flex items-center gap-1 shadow-sm">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                      </svg>
                      <span>Terima Barang</span>
                    </a>
                  @else
                    <a href="{{ route('store.transfers.show', $trf->id) }}" class="px-2.5 py-1 bg-panelBg text-sidebar font-bold text-[11px] rounded-lg transition border border-[#ECEBF4]">
                      Detail
                    </a>
                  @endif
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
  @include('partials.right-panel')
@endsection
