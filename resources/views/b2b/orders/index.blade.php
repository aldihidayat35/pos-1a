@extends('layouts.app')

@section('title', 'Daftar Pesanan & Reservasi B2B — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Bar & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('b2b.index') }}" class="hover:text-sidebar transition">Kemitraan B2B</a>
        <span>/</span>
        <span class="text-sidebar font-bold">Pesanan &amp; Reservasi</span>
      </div>
      <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
        Daftar Pesanan Grosir &amp; Reservasi Stok B2B
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Riwayat pesanan mitra grosir, alokasi stok fisik, tiket serah terima QR, dan status faktur piutang.
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('b2b.pickup.scan') }}" class="px-4 py-2.5 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
        <span>Scan Tiket Pickup</span>
      </a>
      <a href="{{ route('b2b.orders.create') }}" class="px-4 py-2.5 bg-brandOrange hover:bg-orange-600 text-white text-xs font-bold rounded-2xl transition flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        <span>+ Order Grosir B2B</span>
      </a>
    </div>
  </div>

  <!-- Filter & Search Bar -->
  <div class="bg-white rounded-2xl p-4 border border-[#ECEBF4] flex flex-col md:flex-row md:items-center justify-between gap-3">
    <form method="GET" action="{{ route('b2b.orders.index') }}" class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
      <div class="flex items-center gap-1.5 bg-panelBg px-3 py-2 rounded-xl border border-[#ECEBF4] shrink-0">
        <span class="text-[11px] font-semibold text-textMuted whitespace-nowrap">Baris:</span>
        <select data-table-perpage="b2b-orders-table" class="bg-transparent text-xs font-bold text-textMain focus:outline-none cursor-pointer">
          <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
          <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
          <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
          <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        </select>
      </div>

      <div class="relative flex-1 md:w-64">
        <input type="text" name="search" value="{{ request('search') }}"
               data-table-search="b2b-orders-table"
               placeholder="Cari No. Pesanan / Nama Mitra..." class="w-full text-xs rounded-xl border border-[#ECEBF4] pl-8 pr-3 py-2 focus:outline-none focus:border-sidebar">
        <svg class="w-4 h-4 text-textMuted absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </div>

      <select name="status" class="text-xs rounded-xl border border-[#ECEBF4] px-3 py-2 bg-white focus:outline-none focus:border-sidebar" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="RESERVED" {{ request('status') === 'RESERVED' ? 'selected' : '' }}>Stok Teralokasi (RESERVED)</option>
        <option value="HANDED_OVER" {{ request('status') === 'HANDED_OVER' ? 'selected' : '' }}>Diserahkan (HANDED_OVER)</option>
        <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Dibatalkan (CANCELLED)</option>
      </select>

      <select name="payment_term" class="text-xs rounded-xl border border-[#ECEBF4] px-3 py-2 bg-white focus:outline-none focus:border-sidebar" onchange="this.form.submit()">
        <option value="">Semua Ketentuan Bayar</option>
        <option value="CASH" {{ request('payment_term') === 'CASH' ? 'selected' : '' }}>Tunai (CASH)</option>
        <option value="NET_7" {{ request('payment_term') === 'NET_7' ? 'selected' : '' }}>Tempo 7 Hari (NET_7)</option>
        <option value="NET_14" {{ request('payment_term') === 'NET_14' ? 'selected' : '' }}>Tempo 14 Hari (NET_14)</option>
        <option value="NET_30" {{ request('payment_term') === 'NET_30' ? 'selected' : '' }}>Tempo 30 Hari (NET_30)</option>
      </select>

      <button type="submit" class="px-3.5 py-2 bg-sidebar text-white text-xs font-bold rounded-xl transition">
        Filter
      </button>

      @if(request()->hasAny(['search', 'status', 'payment_term', 'per_page']))
        <a href="{{ route('b2b.orders.index') }}" class="px-3 py-2 text-xs font-semibold text-textMuted hover:text-sidebar transition">
          Reset
        </a>
      @endif
    </form>
  </div>

  <!-- Orders Table Container -->
  <div class="bg-white rounded-[26px] p-5 sm:p-6 border border-[#ECEBF4] space-y-4" data-async-table="b2b-orders-table">
    <!-- Desktop View: Table -->
    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted">
            <th class="py-3 px-3 font-bold uppercase text-[10px]">No. Pesanan &amp; Tanggal</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Mitra Rekanan</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Lokasi Serah Terima</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Ketentuan Bayar</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Total Nilai</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Status Pesanan</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Tiket QR</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px] text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @forelse($orders as $ord)
            <tr class="hover:bg-panelBg/60 transition">
              <td class="py-3.5 px-3">
                <a href="{{ route('b2b.orders.show', $ord) }}" class="font-mono font-bold text-sidebar hover:underline">
                  {{ $ord->order_number }}
                </a>
                <span class="block text-[10px] text-textMuted">{{ $ord->created_at->format('d/m/Y H:i') }}</span>
              </td>
              <td class="py-3.5 px-3">
                <span class="font-bold text-textMain block">{{ $ord->member->name }}</span>
                <span class="text-[10px] text-purple-700 bg-purple-100 px-1.5 py-0.5 rounded font-semibold">{{ $ord->member->tier?->name ?? 'NON-TIER' }}</span>
              </td>
              <td class="py-3.5 px-3 font-medium text-textMain">
                {{ $ord->fulfillmentLocation->name }}
              </td>
              <td class="py-3.5 px-3">
                @if($ord->payment_term === 'CASH')
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">TUNAI (CBD)</span>
                @else
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">{{ $ord->payment_term }}</span>
                  @if($ord->due_date)
                    <span class="block text-[10px] text-textMuted mt-0.5">Jatuh tempo: {{ $ord->due_date->format('d/m/Y') }}</span>
                  @endif
                @endif
              </td>
              <td class="py-3.5 px-3 font-mono font-bold text-textMain">
                Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-3">
                @if($ord->status === 'RESERVED' || $ord->status === 'READY_FOR_PICKUP')
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200 inline-flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-600 animate-pulse"></span>
                    Stok Teralokasi
                  </span>
                @elseif($ord->status === 'HANDED_OVER')
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Diserahkan
                  </span>
                @elseif($ord->status === 'CANCELLED')
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Dibatalkan</span>
                @else
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-800">{{ $ord->status }}</span>
                @endif
              </td>
              <td class="py-3.5 px-3 font-mono text-[11px]">
                @if($ord->pickupTicket)
                  @if($ord->pickupTicket->status === 'ISSUED')
                    <span class="text-purple-700 font-bold bg-purple-50 px-2 py-0.5 rounded border border-purple-200">
                      {{ $ord->pickupTicket->ticket_number }}
                    </span>
                  @elseif($ord->pickupTicket->status === 'REDEEMED')
                    <span class="text-emerald-700 font-semibold line-through">
                      {{ $ord->pickupTicket->ticket_number }}
                    </span>
                  @else
                    <span class="text-gray-400">
                      {{ $ord->pickupTicket->ticket_number }} ({{ $ord->pickupTicket->status }})
                    </span>
                  @endif
                @else
                  <span class="text-textMuted">-</span>
                @endif
              </td>
              <td class="py-3.5 px-3 text-right">
                <a href="{{ route('b2b.orders.show', $ord) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-panelBg hover:bg-sidebar hover:text-white text-sidebar font-bold text-[11px] border border-[#E6E4F0] transition">
                  <span>Lihat Rincian</span>
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="py-10 text-center text-textMuted text-xs">
                Tidak ada pesanan B2B yang sesuai kriteria pencarian.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Mobile View: Card Stack -->
    <div class="block md:hidden divide-y divide-[#ECEBF4]">
      @forelse($orders as $ord)
        <div class="py-4 space-y-3">
          <div class="flex items-start justify-between gap-2">
            <div>
              <a href="{{ route('b2b.orders.show', $ord) }}" class="font-mono font-bold text-sidebar hover:underline text-sm block">
                {{ $ord->order_number }}
              </a>
              <span class="text-[10px] text-textMuted">{{ $ord->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div>
              @if($ord->status === 'RESERVED' || $ord->status === 'READY_FOR_PICKUP')
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200 inline-flex items-center gap-1">
                  <span class="w-1.5 h-1.5 rounded-full bg-purple-600 animate-pulse"></span>
                  Teralokasi
                </span>
              @elseif($ord->status === 'HANDED_OVER')
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                  Diserahkan
                </span>
              @elseif($ord->status === 'CANCELLED')
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Dibatalkan</span>
              @else
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-800">{{ $ord->status }}</span>
              @endif
            </div>
          </div>

          <div class="grid grid-cols-2 gap-2 text-xs bg-panelBg/60 p-2.5 rounded-xl border border-[#ECEBF4]">
            <div>
              <span class="text-[10px] uppercase font-bold text-textMuted block">Mitra Rekanan</span>
              <span class="font-bold text-textMain text-xs block truncate">{{ $ord->member->name }}</span>
              <span class="text-[10px] text-purple-700 bg-purple-100 px-1 py-0.2 rounded font-semibold inline-block mt-0.5">
                {{ $ord->member->tier?->name ?? 'NON-TIER' }}
              </span>
            </div>
            <div>
              <span class="text-[10px] uppercase font-bold text-textMuted block">Total Nilai</span>
              <span class="font-mono font-black text-sidebar text-sm block">
                Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
              </span>
              <span class="text-[10px] font-semibold text-textMuted block mt-0.5">
                {{ $ord->payment_term === 'CASH' ? 'Tunai (CBD)' : $ord->payment_term }}
              </span>
            </div>
            <div class="col-span-2 pt-1 border-t border-[#ECEBF4]/60 flex items-center justify-between">
              <div>
                <span class="text-[10px] uppercase font-bold text-textMuted block">Lokasi Serah</span>
                <span class="text-[11px] text-textMain font-medium">{{ $ord->fulfillmentLocation->name }}</span>
              </div>
              @if($ord->pickupTicket)
                <div class="text-right">
                  <span class="text-[10px] uppercase font-bold text-textMuted block">Tiket QR</span>
                  <span class="text-[11px] font-mono font-bold text-purple-700">{{ $ord->pickupTicket->ticket_number }}</span>
                </div>
              @endif
            </div>
          </div>

          <div class="flex items-center justify-end pt-1">
            <a href="{{ route('b2b.orders.show', $ord) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sidebar text-white hover:bg-sidebar/90 font-bold text-xs transition shadow-sm">
              <span>Lihat Rincian Order</span>
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
          </div>
        </div>
      @empty
        <div class="py-10 text-center text-textMuted text-xs">
          Tidak ada pesanan B2B yang sesuai kriteria pencarian.
        </div>
      @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-3 border-t border-[#ECEBF4]">
      {{ $orders->links() }}
    </div>
  </div>
</main>
@endsection
