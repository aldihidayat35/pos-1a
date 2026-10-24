@extends('layouts.app')

@section('title', 'Detail Sesi Opname #' . $opname->opname_number . ' — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Bar & Breadcrumb -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('opname.index') }}" class="hover:text-sidebar transition">Stock Opname</a>
        <span>/</span>
        <span class="text-sidebar font-bold">{{ $opname->opname_number }}</span>
      </div>
      <div class="flex items-center gap-3">
        <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
          Sesi Opname #{{ $opname->opname_number }}
        </h1>
        @php $badge = $opname->status_badge; @endphp
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $badge['class'] }}">
          <span>{{ $badge['label'] }}</span>
        </span>
      </div>
      <p class="text-xs text-textMuted mt-0.5">
        Lokasi: <span class="font-bold text-sidebar">{{ $opname->location->name }}</span> ({{ $opname->location->type }}) • Dibuka pada {{ $opname->started_at->format('d M Y, H:i') }} oleh {{ $opname->openedBy->name }}
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('opname.index') }}" class="px-4 py-2 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition shadow-sm">
        Kembali ke Daftar
      </a>

      @if($opname->isOpen() && ($user->hasPermission('opname.blind_count') || $user->isSuperAdmin()))
        <a href="{{ route('opname.count', $opname->id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-2xl transition shadow-md flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
          <span>Layar Hitung Blind</span>
        </a>
      @endif

      @if($canReconcile && ($opname->isReconciliation() || $opname->isOpen()))
        <a href="{{ route('opname.reconcile', $opname->id) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-2xl transition shadow-md flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          <span>Layar Rekonsiliasi</span>
        </a>
      @endif

      @if(!$opname->isFinalized() && !$opname->isCancelled() && ($user->hasPermission('opname.finalize') || $user->isSuperAdmin()))
        <button type="button" onclick="openCancelModal()" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-2xl border border-rose-200 transition">
          Batalkan Sesi
        </button>
      @endif
    </div>
  </div>

  @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs font-semibold flex items-center gap-2">
      <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  <!-- 4-Step Visual Progress Bar -->
  <div class="bg-white rounded-3xl p-6 border border-[#ECEBF4] shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
      <!-- Step 1: Open -->
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shrink-0">
          ✓
        </div>
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Langkah 1</span>
          <h4 class="font-bold text-xs text-sidebar">Sesi Dibuka</h4>
          <p class="text-[10px] text-textMuted font-mono">{{ $opname->started_at->format('d/m/Y H:i') }}</p>
        </div>
      </div>

      <!-- Step 2: Counting -->
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl {{ in_array($opname->status, ['COUNTING', 'RECONCILIATION', 'FINALIZED']) ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center font-bold text-sm shrink-0">
          {{ in_array($opname->status, ['RECONCILIATION', 'FINALIZED']) ? '✓' : '2' }}
        </div>
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider {{ $opname->status === 'COUNTING' ? 'text-amber-600' : 'text-textMuted' }}">Langkah 2</span>
          <h4 class="font-bold text-xs text-sidebar">Hitung Fisik Blind</h4>
          <p class="text-[10px] text-textMuted">{{ $opname->total_items_counted }} dari {{ $opname->items->count() }} item</p>
        </div>
      </div>

      <!-- Step 3: Reconciliation -->
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl {{ in_array($opname->status, ['RECONCILIATION', 'FINALIZED']) ? ($opname->isFinalized() ? 'bg-emerald-500 text-white' : 'bg-purple-600 text-white') : 'bg-gray-100 text-gray-400' }} flex items-center justify-center font-bold text-sm shrink-0">
          {{ $opname->isFinalized() ? '✓' : '3' }}
        </div>
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider {{ $opname->status === 'RECONCILIATION' ? 'text-purple-600' : 'text-textMuted' }}">Langkah 3</span>
          <h4 class="font-bold text-xs text-sidebar">Rekonsiliasi Selisih</h4>
          <p class="text-[10px] text-textMuted">{{ $opname->total_items_discrepancy }} item berselisih</p>
        </div>
      </div>

      <!-- Step 4: Finalized -->
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl {{ $opname->isFinalized() ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center font-bold text-sm shrink-0">
          {{ $opname->isFinalized() ? '✓' : '4' }}
        </div>
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider {{ $opname->isFinalized() ? 'text-emerald-600' : 'text-textMuted' }}">Langkah 4</span>
          <h4 class="font-bold text-xs text-sidebar">Final / Terposting</h4>
          <p class="text-[10px] text-textMuted">{{ $opname->finalized_at ? $opname->finalized_at->format('d/m/Y H:i') : 'Menunggu pengesahan' }}</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Summary Metric Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
    <div class="bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-sm">
      <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Cakupan Audit</span>
      <h3 class="text-lg font-bold text-sidebar mt-1">
        {{ $opname->scope_type === 'ALL' ? 'Semua Produk' : ($opname->scope_type === 'CATEGORY' ? 'Kategori ' . ($opname->category?->name ?? '') : 'Pilihan Khusus') }}
      </h3>
      <span class="text-[11px] text-textMuted">{{ $opname->items->count() }} total jenis produk</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-sm">
      <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Status Penghitungan</span>
      <h3 class="text-lg font-bold text-sidebar mt-1">
        {{ $opname->total_items_counted }} / {{ $opname->items->count() }} Selesai
      </h3>
      <span class="text-[11px] text-textMuted">{{ $opname->total_items_discrepancy }} item ada selisih</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-sm">
      <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Nilai Variansi Bersih</span>
      <h3 class="text-lg font-bold mt-1 {{ $opname->total_variance_value < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
        {{ $opname->total_variance_value < 0 ? '-Rp ' . number_format(abs($opname->total_variance_value), 0, ',', '.') : 'Rp ' . number_format($opname->total_variance_value, 0, ',', '.') }}
      </h3>
      <span class="text-[11px] text-textMuted">Akumulasi variansi total</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-sm">
      <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Kerugian Susut (Merah)</span>
      <h3 class="text-lg font-bold text-rose-600 mt-1">
        Rp {{ number_format($opname->total_loss_value, 0, ',', '.') }}
      </h3>
      <span class="text-[11px] text-rose-500 font-semibold">Tercatat ke audit trail</span>
    </div>
  </div>

  <!-- Items Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] overflow-hidden shadow-sm">
    <div class="p-4 border-b border-[#ECEBF4] flex items-center justify-between">
      <span class="text-xs font-bold text-sidebar uppercase tracking-wider">
        Rincian Barang &amp; Hasil Hitung Sesi Ini
      </span>
      @if($opname->notes)
        <span class="text-xs text-textMuted italic">"{{ $opname->notes }}"</span>
      @endif
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-sidebar text-white text-[11px] font-bold uppercase tracking-wider">
            <th class="py-3 px-4 w-10 text-center">No</th>
            <th class="py-3 px-4">Nama Produk</th>
            <th class="py-3 px-4 text-center">Satuan</th>
            @if($canReconcile)
              <th class="py-3 px-4 text-center">Snapshot Sistem</th>
            @endif
            <th class="py-3 px-4 text-center">Hasil Fisik</th>
            @if($canReconcile)
              <th class="py-3 px-4 text-center">Selisih Fisik</th>
              <th class="py-3 px-4 text-right">Nilai Selisih</th>
              <th class="py-3 px-4">Alasan Selisih</th>
            @endif
            <th class="py-3 px-4 text-center">Petugas Hitung</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4] text-xs text-textMain">
          @foreach($opname->items as $index => $item)
            @php
              $hasDiff = abs((float) $item->variance_qty) > 0.0001;
              $isNegative = (float) $item->variance_qty < 0;
            @endphp
            <tr class="hover:bg-panelBg/60 transition {{ $hasDiff && $canReconcile ? ($isNegative ? 'bg-rose-50/20' : 'bg-blue-50/20') : '' }}">
              <td class="py-3.5 px-4 text-center text-textMuted font-bold">
                {{ $index + 1 }}
              </td>
              <td class="py-3.5 px-4 font-semibold text-sidebar">
                <div class="font-bold text-textMain text-sm">{{ $item->product->name }}</div>
                <span class="text-[10px] text-textMuted font-mono">SKU: {{ $item->product->sku }}</span>
              </td>
              <td class="py-3.5 px-4 text-center whitespace-nowrap">
                <span class="text-[11px] font-bold text-textMuted">{{ $item->product->baseUom?->code ?? 'PCS' }}</span>
              </td>
              @if($canReconcile)
                <td class="py-3.5 px-4 text-center font-mono font-bold text-textMain">
                  {{ number_format($item->system_qty_snapshot, 2) }}
                </td>
              @endif
              <td class="py-3.5 px-4 text-center font-mono font-bold text-sidebar bg-panelBg/40">
                {{ $item->physical_qty_counted !== null ? number_format($item->physical_qty_counted, 2) : '-' }}
              </td>
              @if($canReconcile)
                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                  @if($hasDiff)
                    @if($isNegative)
                      <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        {{ number_format($item->variance_qty, 2) }}
                      </span>
                    @else
                      <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        +{{ number_format($item->variance_qty, 2) }}
                      </span>
                    @endif
                  @else
                    <span class="text-emerald-600 font-bold">Tepat (0)</span>
                  @endif
                </td>
                <td class="py-3.5 px-4 text-right font-mono font-bold whitespace-nowrap">
                  @if($hasDiff)
                    @if($isNegative)
                      <span class="text-rose-600">-Rp {{ number_format(abs($item->variance_value), 0, ',', '.') }}</span>
                    @else
                      <span class="text-blue-600">+Rp {{ number_format($item->variance_value, 0, ',', '.') }}</span>
                    @endif
                  @else
                    <span class="text-textMuted">Rp 0</span>
                  @endif
                </td>
                <td class="py-3.5 px-4">
                  @if($hasDiff)
                    <span class="font-bold text-sidebar text-[11px] block">{{ $item->reason_label }}</span>
                    @if($item->notes)
                      <span class="text-[10px] text-textMuted italic">{{ $item->notes }}</span>
                    @endif
                  @else
                    <span class="text-textMuted text-[11px]">-</span>
                  @endif
                </td>
              @endif
              <td class="py-3.5 px-4 text-center text-[11px] text-textMuted whitespace-nowrap">
                {{ $item->counter?->name ?? '-' }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Cancel Modal -->
<div id="cancelModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 border border-[#ECEBF4] shadow-2xl space-y-4">
    <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </div>
    
    <div>
      <h3 class="text-lg font-bold text-sidebar">Batalkan Sesi Stock Opname?</h3>
      <p class="text-xs text-textMuted mt-1 leading-relaxed">
        Sesi opname #{{ $opname->opname_number }} akan dibatalkan. Saldo inventaris tidak akan mengalami perubahan.
      </p>
    </div>

    <form method="POST" action="{{ route('opname.cancel', $opname->id) }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-xs font-bold text-sidebar mb-1">Alasan Pembatalan <span class="text-rose-500">*</span></label>
        <textarea name="cancel_reason" required rows="2" placeholder="Tuliskan alasan pembatalan sesi ini..." class="w-full text-xs rounded-xl border border-[#ECEBF4] p-2.5 bg-panelBg/50 focus:outline-none focus:border-sidebar"></textarea>
      </div>

      <div class="flex items-center justify-end gap-2.5 pt-2">
        <button type="button" onclick="closeCancelModal()" class="px-4 py-2 bg-panelBg hover:bg-white text-textMuted text-xs font-bold rounded-xl border border-[#ECEBF4]">
          Kembali
        </button>
        <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md">
          Ya, Batalkan Sesi
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function openCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
  }
  function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
  }
</script>
@endsection
