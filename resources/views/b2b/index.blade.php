@extends('layouts.app')

@section('title', 'Kemitraan B2B & Plafon Piutang — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-7">
  <!-- RBAC Access Warning -->
  @if(!$user->hasPermission('b2b.order') && !$user->isOwner() && !$user->isSuperAdmin())
    <div class="p-4 rounded-2xl bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E] text-xs font-semibold flex items-center gap-3">
      <svg class="w-5 h-5 text-[#F59E0B] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <div>
        <p class="font-bold">Akses Dibatasi — Khusus Sales & Otoritas B2B</p>
        <p class="text-[11px] text-[#92400E]/90 mt-0.5">Akun Anda ({{ $user->primaryRole()?->label ?? 'User' }}) memiliki hak pantau. Transaksi grosir dan pelunasan piutang dicatat melalui akun berizin.</p>
      </div>
    </div>
  @endif

  <!-- 1. SOLID HERO BANNER (Flat solid purple #342A6E, no gradient, no glow shadow) -->
  <div class="relative bg-sidebar rounded-[26px] p-6 sm:p-8 text-white overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-sm">
    <div class="relative z-10 max-w-xl">
      <div class="inline-flex items-center gap-2 bg-white/15 px-3 py-1 rounded-full text-xs font-semibold mb-3">
        <span class="w-2 h-2 rounded-full bg-purple-400"></span>
        <span>Fase 6: Ekosistem Penjualan Grosir Tempo & Kemitraan</span>
      </div>
      <h1 class="font-display text-2xl sm:text-[30px] font-bold tracking-tight leading-tight">
        Kemitraan B2B & Plafon Piutang
      </h1>
      <p class="text-white/85 text-xs sm:text-sm mt-1.5 font-medium leading-relaxed">
        Pusat kendali pesanan grosir, reservasi fisik stok otomatis, penguncian plafon kredit server-side, tiket serah terima QR sekali pakai, dan pembukuan piutang tempo.
      </p>

      <div class="mt-4 flex flex-wrap items-center gap-2.5">
        <span class="bg-white/15 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span>{{ $stats['total_b2b_members'] }} Mitra Grosir Aktif</span>
        </span>
        <span class="bg-white/15 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          <span>Total Plafon: Rp {{ number_format($stats['total_credit_limit'] ?? 0, 0, ',', '.') }}</span>
        </span>
      </div>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0 w-full sm:w-auto">
      <a href="{{ route('b2b.orders.create') }}" class="px-5 py-3 bg-brandOrange hover:bg-orange-600 text-white text-xs font-bold rounded-2xl transition flex items-center justify-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Buat Order Grosir B2B</span>
      </a>
      <a href="{{ route('b2b.pickup.scan') }}" class="px-5 py-3 bg-white hover:bg-[#F2F1FA] text-sidebar text-xs font-bold rounded-2xl transition flex items-center justify-center gap-2 shadow-sm">
        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
        </svg>
        <span>Scan Tiket Pickup</span>
      </a>
    </div>
  </div>

  <!-- 2. 4 KEY METRIC CARDS -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Mitra B2B Aktif</span>
      <h3 class="font-display font-extrabold text-2xl text-textMain mt-2">{{ $stats['total_b2b_members'] }}</h3>
      <p class="text-xs text-textMuted mt-1">Akun grosir resmi berbadan usaha</p>
    </div>

    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Pesanan Aktif (Reserved)</span>
      <h3 class="font-display font-extrabold text-2xl text-purple-600 mt-2">{{ $stats['active_orders_count'] }} Order</h3>
      <p class="text-xs text-textMuted mt-1">Stok fisik teralokasi &amp; terkunci</p>
    </div>

    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Piutang B2B Berjalan</span>
      <h3 class="font-display font-extrabold text-2xl text-sidebar mt-2">Rp {{ number_format($stats['total_outstanding_debt'], 0, ',', '.') }}</h3>
      <p class="text-xs text-textMuted mt-1">Faktur tempo belum terbayar</p>
    </div>

    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      @if($stats['overdue_count'] > 0)
        <span class="text-[11px] font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full inline-block">Piutang Overdue!</span>
        <h3 class="font-display font-extrabold text-2xl text-red-600 mt-2">{{ $stats['overdue_count'] }} Faktur</h3>
        <p class="text-xs text-red-500 font-semibold mt-1">Rp {{ number_format($stats['total_overdue_debt'], 0, ',', '.') }} melewati jatuh tempo</p>
      @else
        <span class="text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full inline-block">Kolektibilitas Bersih</span>
        <h3 class="font-display font-extrabold text-2xl text-emerald-600 mt-2">0 Overdue</h3>
        <p class="text-xs text-textMuted mt-1">Seluruh tagihan tempo lancar</p>
      @endif
    </div>
  </div>

  <!-- 3. RECENT B2B ORDERS & FULFILLMENT STATUS -->
  <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h3 class="font-display font-bold text-lg text-textMain">Pesanan Grosir B2B Terbaru</h3>
        <p class="text-xs text-textMuted mt-0.5">Monitoring pemesanan, alokasi stok, tiket QR pickup, dan penerbitan faktur</p>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('b2b.orders.index') }}" class="text-xs font-bold text-sidebar hover:text-sidebarDark underline">
          Lihat Semua Pesanan B2B →
        </a>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted">
            <th class="py-3 px-3 font-bold uppercase text-[10px]">No. Pesanan</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Mitra Rekanan</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Lokasi Serah Terima</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Ketentuan Bayar</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Total Nilai</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Status Pesanan</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px] text-right">Tiket QR &amp; Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @forelse($recentOrders as $ord)
            <tr class="hover:bg-panelBg/60 transition">
              <td class="py-3.5 px-3 font-mono font-bold text-sidebar">
                <a href="{{ route('b2b.orders.show', $ord) }}" class="hover:underline">
                  {{ $ord->order_number }}
                </a>
                <span class="block text-[10px] text-textMuted font-sans">{{ $ord->created_at->format('d/m/Y H:i') }}</span>
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
                    Diserahkan (Lunas/Faktur)
                  </span>
                @elseif($ord->status === 'CANCELLED')
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Dibatalkan</span>
                @else
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-800">{{ $ord->status }}</span>
                @endif
              </td>
              <td class="py-3.5 px-3 text-right">
                <a href="{{ route('b2b.orders.show', $ord) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-panelBg hover:bg-sidebar hover:text-white text-sidebar font-bold text-[11px] border border-[#E6E4F0] transition">
                  <span>Rincian &amp; QR</span>
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-8 text-center text-textMuted text-xs">
                Belum ada transaksi pesanan grosir B2B. Buat pesanan pertama melalui tombol di atas.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- 4. B2B PARTNER CREDIT EXPOSURE & DIRECTORY -->
  <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h3 class="font-display font-bold text-lg text-textMain">Mitra Grosir &amp; Pemantauan Plafon Kredit</h3>
        <p class="text-xs text-textMuted mt-0.5">Kalkulasi eksposur kredit real-time, status jatuh tempo, dan kunci otomatis kasir/sales</p>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('b2b.invoices.index') }}" class="px-4 py-2 bg-panelBg hover:bg-[#E6E4F0] text-sidebar text-xs font-bold rounded-xl transition inline-flex items-center gap-1.5 border border-[#ECEBF4]">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <span>Buku Piutang &amp; Faktur</span>
        </a>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted">
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Mitra Rekanan</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Tier &amp; Diskon</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Plafon Kredit</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Eksposur Terpakai</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Sisa Plafon Aman</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Status Kolektibilitas</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px] text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @forelse($b2bMembers as $m)
            @php
              $exp = $memberExposures[$m->id] ?? null;
              $utilPercent = $exp ? $exp['utilization_percent'] : 0;
            @endphp
            <tr class="hover:bg-panelBg/60 transition">
              <td class="py-3.5 px-3 font-semibold text-textMain">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-xl bg-purple-100 text-sidebar font-bold flex items-center justify-center shrink-0 text-[11px]">
                    B2B
                  </div>
                  <div>
                    <span class="block font-bold">{{ $m->name }}</span>
                    <span class="text-[11px] font-mono text-textMuted">{{ $m->member_code }}</span>
                  </div>
                </div>
              </td>
              <td class="py-3.5 px-3">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-[#F3F0FA] text-sidebar border border-[#ECEBF4]">
                  {{ $m->tier?->name ?? 'NON-TIER' }} ({{ (float)($m->tier?->discount_percent ?? 0) }}%)
                </span>
              </td>
              <td class="py-3.5 px-3 font-mono font-bold text-textMain">
                Rp {{ number_format($m->credit_limit, 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-3 font-mono">
                <span class="font-bold text-sidebar">Rp {{ number_format($exp['total_exposure'] ?? 0, 0, ',', '.') }}</span>
                <!-- Utilization Bar -->
                <div class="w-24 bg-gray-200 rounded-full h-1.5 mt-1 overflow-hidden">
                  <div class="h-1.5 rounded-full {{ $utilPercent > 90 ? 'bg-red-500' : ($utilPercent > 70 ? 'bg-amber-500' : 'bg-purple-600') }}" style="width: {{ min(100, $utilPercent) }}%"></div>
                </div>
                <span class="text-[9px] text-textMuted">{{ $utilPercent }}% limit</span>
              </td>
              <td class="py-3.5 px-3 font-mono font-bold {{ ($exp['available_credit'] ?? 0) < 5000000 ? 'text-amber-600' : 'text-emerald-700' }}">
                Rp {{ number_format($exp['available_credit'] ?? 0, 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-3">
                @if($exp && $exp['has_overdue_invoices'])
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">
                    Kunci: {{ $exp['overdue_count'] }} Overdue
                  </span>
                @elseif($utilPercent >= 100)
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                    Kunci: Plafon Habis
                  </span>
                @else
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                    Lancar (Bisa Tempo)
                  </span>
                @endif
              </td>
              <td class="py-3.5 px-3 text-right">
                <div class="inline-flex items-center gap-1.5">
                  <a href="{{ route('master.members.card', $m) }}" target="_blank" class="p-1.5 rounded-lg bg-panelBg hover:bg-sidebar hover:text-white text-sidebar border border-[#ECEBF4] transition" title="Kartu Member QR">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                  </a>
                  <a href="{{ route('b2b.orders.create', ['member_id' => $m->id]) }}" class="px-2.5 py-1 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold text-[11px] transition">
                    + Order
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-8 text-center text-textMuted text-xs">
                Belum ada mitra B2B terdaftar. Tambahkan di menu Master Member.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</main>
@endsection

@section('right_panel')
  @include('b2b.partials.right-panel')
@endsection
