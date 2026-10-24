@extends('layouts.app')

@section('title', 'Stok Opname & Blind Count — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Bar & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <span>Operasional Pasar</span>
        <span>/</span>
        <span class="text-sidebar font-bold">Stok Opname Mandiri</span>
      </div>
      <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
        Sesi Stok Opname &amp; Rekonsiliasi Audit
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Manajemen sesi hitung fisik buta (Blind Count), rekonsiliasi selisih, alasan variansi, dan koreksi saldo fisik atomik.
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('audit.index', ['severity' => 'RED']) }}" class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-2xl border border-rose-200 transition flex items-center gap-2 shadow-sm">
        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
        <span>Log Selisih Opname (Merah)</span>
      </a>
      @if($user->hasAnyPermission(['opname.reconcile', 'opname.finalize']) || $user->isSuperAdmin())
        <a href="{{ route('opname.create') }}" class="px-4 py-2.5 bg-brandOrange hover:bg-orange-600 text-white text-xs font-bold rounded-2xl transition flex items-center gap-2 shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          <span>+ Buka Sesi Opname</span>
        </a>
      @endif
    </div>
  </div>

  <!-- Metric Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1: Total Sesi -->
    <div class="bg-white rounded-2xl p-5 border border-[#ECEBF4] shadow-sm flex items-center justify-between">
      <div>
        <p class="text-xs font-bold text-textMuted uppercase tracking-wider">Total Sesi Opname</p>
        <h3 class="text-2xl font-extrabold text-sidebar mt-1">{{ number_format($stats['total']) }}</h3>
        <p class="text-[11px] text-textMuted mt-0.5">Di lokasi aktif terpilih</p>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-sidebar">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      </div>
    </div>

    <!-- Card 2: Sesi Aktif Berjalan -->
    <div class="bg-white rounded-2xl p-5 border border-[#ECEBF4] shadow-sm flex items-center justify-between">
      <div>
        <p class="text-xs font-bold text-textMuted uppercase tracking-wider">Sesi Aktif (Hitung/Review)</p>
        <h3 class="text-2xl font-extrabold text-brandOrange mt-1">{{ number_format($stats['active']) }}</h3>
        <p class="text-[11px] text-textMuted mt-0.5">Siap dihitung atau direkonsiliasi</p>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-brandOrange">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
    </div>

    <!-- Card 3: Sesi Final / Terposting -->
    <div class="bg-white rounded-2xl p-5 border border-[#ECEBF4] shadow-sm flex items-center justify-between">
      <div>
        <p class="text-xs font-bold text-textMuted uppercase tracking-wider">Sesi Final / Selesai</p>
        <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($stats['finalized']) }}</h3>
        <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Saldo telah disesuaikan</p>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
    </div>

    <!-- Card 4: Total Kerugian Susut / Selisih Negatif -->
    <div class="bg-white rounded-2xl p-5 border border-[#ECEBF4] shadow-sm flex items-center justify-between">
      <div>
        <p class="text-xs font-bold text-textMuted uppercase tracking-wider">Total Susut / Selisih Kurang</p>
        <h3 class="text-2xl font-extrabold text-rose-600 mt-1">Rp {{ number_format($stats['total_loss'], 0, ',', '.') }}</h3>
        <p class="text-[11px] text-rose-500 font-semibold mt-0.5">Kerugian tercatat ke Anomaly Stream</p>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
    </div>
  </div>

  <!-- Filter & Search Bar -->
  <div class="bg-white rounded-2xl p-4 border border-[#ECEBF4] flex flex-col md:flex-row md:items-center justify-between gap-3">
    <form method="GET" action="{{ route('opname.index') }}" class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
      <div class="flex items-center gap-2">
        <span class="text-xs font-bold text-textMuted">Lokasi:</span>
        <select name="location_id" class="text-xs rounded-xl border border-[#ECEBF4] px-3 py-2 bg-white focus:outline-none focus:border-sidebar" onchange="this.form.submit()">
          @foreach($locations as $loc)
            <option value="{{ $loc->id }}" {{ (string) request('location_id', $activeLocation?->id) === (string) $loc->id ? 'selected' : '' }}>
              {{ $loc->name }} ({{ $loc->type }})
            </option>
          @endforeach
        </select>
      </div>

      <div class="flex items-center gap-2">
        <span class="text-xs font-bold text-textMuted">Status:</span>
        <select name="status" class="text-xs rounded-xl border border-[#ECEBF4] px-3 py-2 bg-white focus:outline-none focus:border-sidebar" onchange="this.form.submit()">
          <option value="">Semua Status</option>
          <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>Terbuka (OPEN)</option>
          <option value="COUNTING" {{ request('status') === 'COUNTING' ? 'selected' : '' }}>Sedang Dihitung (COUNTING)</option>
          <option value="RECONCILIATION" {{ request('status') === 'RECONCILIATION' ? 'selected' : '' }}>Rekonsiliasi (RECONCILIATION)</option>
          <option value="FINALIZED" {{ request('status') === 'FINALIZED' ? 'selected' : '' }}>Final / Selesai (FINALIZED)</option>
          <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Dibatalkan (CANCELLED)</option>
        </select>
      </div>

      <button type="submit" class="px-3.5 py-2 bg-sidebar text-white text-xs font-bold rounded-xl transition">
        Filter
      </button>

      @if(request()->hasAny(['status', 'location_id']))
        <a href="{{ route('opname.index') }}" class="text-xs text-textMuted hover:text-rose-600 transition underline">
          Reset Filter
        </a>
      @endif
    </form>
  </div>

  <!-- Table of Stock Opname Sessions -->
  <div class="bg-white rounded-2xl border border-[#ECEBF4] overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-sidebar text-white text-[11px] font-bold uppercase tracking-wider">
            <th class="py-3 px-4">No. Sesi Opname</th>
            <th class="py-3 px-4">Lokasi</th>
            <th class="py-3 px-4">Cakupan Hitung</th>
            <th class="py-3 px-4">Status Alur</th>
            <th class="py-3 px-4 text-center">Hitung / Item</th>
            <th class="py-3 px-4 text-center">Selisih Item</th>
            <th class="py-3 px-4 text-right">Nilai Selisih (Rp)</th>
            <th class="py-3 px-4">Dibuat Oleh</th>
            <th class="py-3 px-4 text-center">Aksi Operasional</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4] text-xs text-textMain">
          @forelse($opnames as $opn)
            <tr class="hover:bg-panelBg/60 transition">
              <td class="py-3.5 px-4 font-bold text-sidebar whitespace-nowrap">
                <a href="{{ route('opname.show', $opn->id) }}" class="hover:underline flex items-center gap-1.5">
                  <svg class="w-4 h-4 text-sidebar/60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  <span>{{ $opn->opname_number }}</span>
                </a>
                <span class="block text-[10px] text-textMuted font-normal mt-0.5">
                  {{ $opn->started_at->format('d M Y, H:i') }}
                </span>
              </td>
              <td class="py-3.5 px-4 font-semibold text-sidebar whitespace-nowrap">
                {{ $opn->location->name }}
                <span class="block text-[10px] text-textMuted font-normal">{{ $opn->location->type }}</span>
              </td>
              <td class="py-3.5 px-4 whitespace-nowrap">
                @if($opn->scope_type === 'ALL')
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-sidebar border border-indigo-100">
                    Semua Produk
                  </span>
                @elseif($opn->scope_type === 'CATEGORY')
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    Kategori: {{ $opn->category?->name ?? 'Kategori' }}
                  </span>
                @else
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                    Pilihan Produk Khusus
                  </span>
                @endif
              </td>
              <td class="py-3.5 px-4 whitespace-nowrap">
                @php $badge = $opn->status_badge; @endphp
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badge['class'] }}">
                  @if($opn->status === 'OPEN' || $opn->status === 'COUNTING')
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                  @elseif($opn->status === 'RECONCILIATION')
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                  @elseif($opn->status === 'FINALIZED')
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  @endif
                  <span>{{ $badge['label'] }}</span>
                </span>
              </td>
              <td class="py-3.5 px-4 text-center font-bold">
                {{ $opn->total_items_counted }}
              </td>
              <td class="py-3.5 px-4 text-center">
                @if($opn->total_items_discrepancy > 0)
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                    {{ $opn->total_items_discrepancy }} Item Berselisih
                  </span>
                @else
                  <span class="text-textMuted font-semibold">0</span>
                @endif
              </td>
              <td class="py-3.5 px-4 text-right font-mono font-bold whitespace-nowrap">
                @if($opn->total_variance_value < 0)
                  <span class="text-rose-600">-Rp {{ number_format(abs($opn->total_variance_value), 0, ',', '.') }}</span>
                @elseif($opn->total_variance_value > 0)
                  <span class="text-emerald-600">+Rp {{ number_format($opn->total_variance_value, 0, ',', '.') }}</span>
                @else
                  <span class="text-textMuted">Rp 0</span>
                @endif
              </td>
              <td class="py-3.5 px-4 whitespace-nowrap text-textMuted text-[11px]">
                {{ $opn->openedBy->name }}
              </td>
              <td class="py-3.5 px-4 text-center whitespace-nowrap">
                <div class="flex items-center justify-center gap-1.5">
                  @if($opn->isOpen() && ($user->hasPermission('opname.blind_count') || $user->isSuperAdmin()))
                    <a href="{{ route('opname.count', $opn->id) }}" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold rounded-xl border border-amber-200 transition text-[11px] flex items-center gap-1" title="Input Hasil Hitung Fisik (Blind Count)">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                      <span>Hitung Blind</span>
                    </a>
                  @endif

                  @if($opn->isReconciliation() && ($user->hasPermission('opname.reconcile') || $user->isSuperAdmin()))
                    <a href="{{ route('opname.reconcile', $opn->id) }}" class="px-2.5 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold rounded-xl border border-purple-200 transition text-[11px] flex items-center gap-1" title="Rekonsiliasi & Input Alasan Selisih">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                      <span>Rekonsiliasi</span>
                    </a>
                  @endif

                  <a href="{{ route('opname.show', $opn->id) }}" class="px-2.5 py-1.5 bg-panelBg hover:bg-white text-sidebar font-bold rounded-xl border border-[#ECEBF4] transition text-[11px]">
                    Detail
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="py-12 text-center text-textMuted">
                <svg class="w-12 h-12 mx-auto text-textMuted/40 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="font-bold text-sm text-textMain">Belum ada sesi Stock Opname</p>
                <p class="text-xs text-textMuted mt-1">Buka sesi opname mandiri untuk memulai penghitungan fisik buta dan audit stok berkala.</p>
                @if($user->hasAnyPermission(['opname.reconcile', 'opname.finalize']) || $user->isSuperAdmin())
                  <a href="{{ route('opname.create') }}" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 bg-brandOrange text-white text-xs font-bold rounded-xl">
                    + Buka Sesi Opname Baru
                  </a>
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($opnames->hasPages())
      <div class="p-4 border-t border-[#ECEBF4]">
        {{ $opnames->links() }}
      </div>
    @endif
  </div>
</main>
@endsection
