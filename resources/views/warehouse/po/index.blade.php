@extends('layouts.app')

@section('title', 'Daftar Purchase Order — PasarHub')

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
        <span class="text-textMain font-bold">Purchase Orders (PO)</span>
      </div>
      <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
        Manajemen Pesanan Pembelian Suplayer
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Penerbitan PO, pemantauan pengiriman vendor, dan penerimaan fisik barang ke saldo gudang.
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('warehouse.index') }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
        ← Kembali ke Gudang
      </a>
      @if($user->hasPermission('purchasing.po') || $user->hasPermission('inventory.manage') || $user->isSuperAdmin())
        <a href="{{ route('warehouse.po.create') }}" class="px-4 py-2 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-sm">
          <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Buat PO Baru</span>
        </a>
      @endif
    </div>
  </div>

  <!-- Status Filter Pills -->
  <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold">
    <a href="{{ route('warehouse.po.index', array_merge(request()->except('status', 'page'), [])) }}"
       class="px-3.5 py-1.5 rounded-full transition whitespace-nowrap {{ !request()->filled('status') ? 'bg-sidebar text-white' : 'bg-white text-textMuted hover:text-textMain border border-[#ECEBF4]' }}">
      Semua Status ({{ $statusCounts['ALL'] }})
    </a>
    <a href="{{ route('warehouse.po.index', array_merge(request()->except('status', 'page'), ['status' => 'ISSUED'])) }}"
       class="px-3.5 py-1.5 rounded-full transition whitespace-nowrap {{ request('status') === 'ISSUED' ? 'bg-blue-600 text-white' : 'bg-white text-blue-700 hover:bg-blue-50 border border-blue-200' }}">
      On Order / Issued ({{ $statusCounts['ISSUED'] }})
    </a>
    <a href="{{ route('warehouse.po.index', array_merge(request()->except('status', 'page'), ['status' => 'PARTIALLY_RECEIVED'])) }}"
       class="px-3.5 py-1.5 rounded-full transition whitespace-nowrap {{ request('status') === 'PARTIALLY_RECEIVED' ? 'bg-amber-600 text-white' : 'bg-white text-amber-700 hover:bg-amber-50 border border-amber-200' }}">
      Diterima Sebagian ({{ $statusCounts['PARTIALLY_RECEIVED'] }})
    </a>
    <a href="{{ route('warehouse.po.index', array_merge(request()->except('status', 'page'), ['status' => 'RECEIVED'])) }}"
       class="px-3.5 py-1.5 rounded-full transition whitespace-nowrap {{ request('status') === 'RECEIVED' ? 'bg-emerald-600 text-white' : 'bg-white text-emerald-700 hover:bg-emerald-50 border border-emerald-200' }}">
      Lengkap Diterima ({{ $statusCounts['RECEIVED'] }})
    </a>
  </div>

  <!-- Search & Filter Bar -->
  <div class="bg-white rounded-2xl border border-[#ECEBF4] p-4 shadow-sm">
    <form action="{{ route('warehouse.po.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
      @if(request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
      @endif
      <div class="flex items-center gap-1.5 bg-panelBg px-3 py-2 rounded-xl border border-[#ECEBF4] shrink-0">
        <span class="text-[11px] font-semibold text-textMuted whitespace-nowrap">Baris:</span>
        <select data-table-perpage="po-table"
                class="bg-transparent text-xs font-bold text-textMain focus:outline-none cursor-pointer">
          <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
          <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12</option>
          <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
          <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        </select>
      </div>

      <div class="relative flex-1 w-full">
        <svg class="w-4 h-4 text-textMuted absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" name="search" value="{{ request('search') }}"
               data-table-search="po-table"
               placeholder="Cari nomor PO atau nama suplayer..."
               class="w-full pl-10 pr-4 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">
      </div>
      <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-sidebar text-white text-xs font-bold rounded-xl hover:bg-sidebar/90 transition">
        Cari PO
      </button>
      @if(request()->anyFilled(['search', 'status']))
        <a href="{{ route('warehouse.po.index') }}" class="w-full sm:w-auto px-3 py-2 text-xs font-bold text-brandCoral hover:underline text-center">
          Reset Filter
        </a>
      @endif
    </form>
  </div>

  <!-- Orders Table Container -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 sm:p-6 shadow-sm" data-async-table="po-table">
    @if($orders->isEmpty())
      <div class="py-12 text-center text-textMuted text-xs">
        Tidak ditemukan data Purchase Order yang sesuai dengan pencarian atau filter.
      </div>
    @else
      <!-- Desktop View: Table -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3 px-3">No. PO</th>
              <th class="py-3 px-3">Pemasok / Suplayer</th>
              <th class="py-3 px-3">Gudang Tujuan</th>
              <th class="py-3 px-3">Tanggal Pesan</th>
              <th class="py-3 px-3">Status</th>
              <th class="py-3 px-3 text-right">Nilai Pesanan</th>
              <th class="py-3 px-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @foreach($orders as $po)
              <tr class="hover:bg-panelBg/60 transition">
                <td class="py-3.5 px-3 font-extrabold text-sidebar">
                  <a href="{{ route('warehouse.po.show', $po->id) }}" class="text-brandOrange hover:underline">
                    {{ $po->po_number }}
                  </a>
                  <span class="block text-[10px] text-textMuted font-normal mt-0.5">
                    Oleh: {{ $po->creator?->name ?? 'Admin' }}
                  </span>
                </td>
                <td class="py-3.5 px-3">
                  <span class="font-bold text-textMain block">{{ $po->supplier->name }}</span>
                  <span class="text-[10px] text-textMuted font-mono">{{ $po->supplier->supplier_code }}</span>
                </td>
                <td class="py-3.5 px-3 font-medium text-textMain">
                  {{ $po->destinationLocation->name }}
                </td>
                <td class="py-3.5 px-3">
                  <span class="text-textMain font-medium block">{{ $po->ordered_at?->format('d/m/Y') }}</span>
                  @if($po->expected_delivery_at)
                    <span class="text-[10px] text-textMuted block">Est: {{ $po->expected_delivery_at?->format('d/m/Y') }}</span>
                  @endif
                </td>
                <td class="py-3.5 px-3">
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-block {{ $po->status_badge_class }}">
                    {{ $po->status_label }}
                  </span>
                </td>
                <td class="py-3.5 px-3 text-right font-extrabold text-sidebar">
                  @if($user->hasPermission('inventory.hpp_view') || $user->isSuperAdmin() || $user->isOwner())
                    Rp {{ number_format($po->grand_total, 0, ',', '.') }}
                  @else
                    <span class="text-textMuted italic font-normal">*** [Terkunci]</span>
                  @endif
                </td>
                <td class="py-3.5 px-3 text-center">
                  <div class="flex items-center justify-center gap-1.5">
                    <a href="{{ route('warehouse.po.show', $po->id) }}" class="px-2.5 py-1 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-[11px] rounded-lg transition border border-[#ECEBF4]">
                      Detail
                    </a>
                    @if(($user->hasPermission('purchasing.receive') || $user->isSuperAdmin()) && in_array($po->status, ['ISSUED', 'PARTIALLY_RECEIVED']))
                      <a href="{{ route('warehouse.receipts.create', $po->id) }}" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded-lg transition inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                        <span>Terima</span>
                      </a>
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
        @foreach($orders as $po)
          <div class="py-4 space-y-3">
            <div class="flex items-start justify-between gap-2">
              <div>
                <a href="{{ route('warehouse.po.show', $po->id) }}" class="font-extrabold text-brandOrange hover:underline text-sm font-mono block">
                  {{ $po->po_number }}
                </a>
                <span class="text-[11px] text-textMuted">Oleh: {{ $po->creator?->name ?? 'Admin' }}</span>
              </div>
              <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border shrink-0 {{ $po->status_badge_class }}">
                {{ $po->status_label }}
              </span>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs bg-panelBg/60 p-2.5 rounded-xl border border-[#ECEBF4]">
              <div>
                <span class="text-[10px] uppercase font-bold text-textMuted block">Pemasok / Suplayer</span>
                <span class="font-bold text-textMain text-xs block truncate">{{ $po->supplier->name }}</span>
                <span class="text-[10px] text-textMuted font-mono">{{ $po->supplier->supplier_code }}</span>
              </div>
              <div>
                <span class="text-[10px] uppercase font-bold text-textMuted block">Gudang Tujuan</span>
                <span class="font-medium text-textMain text-xs block truncate">{{ $po->destinationLocation->name }}</span>
              </div>
              <div class="pt-1.5 border-t border-[#ECEBF4]/60">
                <span class="text-[10px] uppercase font-bold text-textMuted block">Tanggal Pesan</span>
                <span class="text-xs text-textMain font-medium">{{ $po->ordered_at?->format('d/m/Y') }}</span>
              </div>
              <div class="pt-1.5 border-t border-[#ECEBF4]/60">
                <span class="text-[10px] uppercase font-bold text-textMuted block">Nilai Pesanan</span>
                @if($user->hasPermission('inventory.hpp_view') || $user->isSuperAdmin() || $user->isOwner())
                  <span class="font-mono font-bold text-sidebar text-xs">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</span>
                @else
                  <span class="text-textMuted italic text-[11px]">*** [Terkunci]</span>
                @endif
              </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
              <a href="{{ route('warehouse.po.show', $po->id) }}" class="px-3 py-1.5 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
                Detail PO
              </a>
              @if(($user->hasPermission('purchasing.receive') || $user->isSuperAdmin()) && in_array($po->status, ['ISSUED', 'PARTIALLY_RECEIVED']))
                <a href="{{ route('warehouse.receipts.create', $po->id) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition inline-flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                  </svg>
                  <span>Terima Barang</span>
                </a>
              @endif
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4 pt-4 border-t border-[#ECEBF4]">
        {{ $orders->links() }}
      </div>
    @endif
  </div>
</main>
@endsection

@section('right_panel')
  @include('warehouse.partials.right-panel')
@endsection
