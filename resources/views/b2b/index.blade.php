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

  <!-- 1. SOLID HERO BANNER (Clean UI with Mobile Tooltip) -->
  <div class="relative bg-sidebar rounded-[26px] p-6 sm:p-8 text-white overflow-hidden shadow-sm">
    <div class="relative z-10 max-w-2xl">
      <div class="flex items-center gap-2 flex-wrap">
        <h1 class="font-display text-lg sm:text-2xl md:text-[30px] font-bold tracking-tight leading-tight">
          Kemitraan B2B & Plafon Piutang
        </h1>

        <!-- Mobile-Only Info Tooltip Trigger (ℹ️) -->
        <div class="relative md:hidden inline-flex items-center">
          <button type="button" 
                  onclick="toggleHeaderTooltip('b2bHeroPopover', event)" 
                  class="w-6 h-6 rounded-full bg-white/20 hover:bg-white text-white hover:text-sidebar flex items-center justify-center transition p-1 shadow-xs"
                  title="Lihat Keterangan Halaman"
                  aria-label="Info Halaman">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
            </svg>
          </button>

          <!-- Mobile Popover Floating Bubble -->
          <div id="b2bHeroPopover" class="header-popover hidden fixed inset-x-4 top-20 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs text-textMain">
            <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
              <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
                <span>Informasi B2B</span>
              </div>
              <button type="button" onclick="closeHeaderTooltip('b2bHeroPopover')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
            </div>
            <p class="text-[11px] text-textMuted leading-relaxed">
              Pusat kendali pesanan grosir, reservasi fisik stok otomatis, penguncian plafon kredit server-side, tiket serah terima QR, dan pembukuan piutang tempo.
            </p>
          </div>
        </div>
      </div>

      <!-- Desktop Subtitle (Hidden on Mobile) -->
      <p class="hidden md:block text-white/85 text-[11px] sm:text-xs md:text-sm mt-1.5 font-medium leading-relaxed">
        Pusat kendali pesanan grosir, reservasi fisik stok otomatis, penguncian plafon kredit server-side, tiket serah terima QR, dan pembukuan piutang tempo.
      </p>
    </div>
  </div>

  <!-- 2. 4 KEY METRIC CARDS -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
    <div class="p-4 sm:p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <span class="text-[10px] sm:text-[11px] font-bold text-textMuted uppercase tracking-wider">Mitra B2B Aktif</span>
      <h3 class="font-display font-extrabold text-base sm:text-xl md:text-2xl text-textMain mt-2 truncate">{{ $stats['total_b2b_members'] }}</h3>
      <p class="text-[10px] sm:text-xs text-textMuted mt-1 truncate">Akun grosir resmi berbadan usaha</p>
    </div>

    <div class="p-4 sm:p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <span class="text-[10px] sm:text-[11px] font-bold text-textMuted uppercase tracking-wider">Pesanan Aktif (Reserved)</span>
      <h3 class="font-display font-extrabold text-base sm:text-xl md:text-2xl text-purple-600 mt-2 truncate">{{ $stats['active_orders_count'] }} Order</h3>
      <p class="text-[10px] sm:text-xs text-textMuted mt-1 truncate">Stok fisik teralokasi &amp; terkunci</p>
    </div>

    <div class="p-4 sm:p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <span class="text-[10px] sm:text-[11px] font-bold text-textMuted uppercase tracking-wider">Piutang B2B Berjalan</span>
      <h3 class="font-display font-extrabold text-base sm:text-xl md:text-2xl text-sidebar mt-2 truncate">Rp {{ number_format($stats['total_outstanding_debt'], 0, ',', '.') }}</h3>
      <p class="text-[10px] sm:text-xs text-textMuted mt-1 truncate">Faktur tempo belum terbayar</p>
    </div>

    <div class="p-4 sm:p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      @if($stats['overdue_count'] > 0)
        <span class="text-[9px] sm:text-[11px] font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full inline-block">Piutang Overdue!</span>
        <h3 class="font-display font-extrabold text-base sm:text-xl md:text-2xl text-red-600 mt-2 truncate">{{ $stats['overdue_count'] }} Faktur</h3>
        <p class="text-[10px] sm:text-xs text-red-500 font-semibold mt-1 truncate">Rp {{ number_format($stats['total_overdue_debt'], 0, ',', '.') }} melewati jatuh tempo</p>
      @else
        <span class="text-[9px] sm:text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full inline-block">Kolektibilitas Bersih</span>
        <h3 class="font-display font-extrabold text-base sm:text-xl md:text-2xl text-emerald-600 mt-2 truncate">0 Overdue</h3>
        <p class="text-[10px] sm:text-xs text-textMuted mt-1 truncate">Seluruh tagihan tempo lancar</p>
      @endif
    </div>
  </div>

  <!-- 3. RECENT B2B ORDERS & FULFILLMENT STATUS -->
  <div class="bg-white rounded-[26px] p-5 sm:p-6 border border-[#ECEBF4] space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h3 class="font-display font-bold text-sm sm:text-base md:text-lg text-textMain">Pesanan Grosir B2B Terbaru</h3>
        <p class="text-[11px] sm:text-xs text-textMuted mt-0.5">Monitoring pemesanan, alokasi stok, tiket QR pickup, dan penerbitan faktur</p>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('b2b.orders.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sidebar/5 hover:bg-sidebar text-sidebar hover:text-white text-xs font-semibold transition">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
          </svg>
          <span>Semua Pesanan</span>
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
