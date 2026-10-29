@extends('layouts.app')

@section('title', 'Rekonsiliasi & Alasan Selisih Opname — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Bar & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('opname.index') }}" class="hover:text-sidebar transition">Stock Opname</a>
        <span>/</span>
        <a href="{{ route('opname.show', $opname->id) }}" class="hover:text-sidebar transition">{{ $opname->opname_number }}</a>
        <span>/</span>
        <span class="text-sidebar font-bold">Rekonsiliasi Selisih</span>
      </div>
      <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
        Rekonsiliasi &amp; Validasi Alasan Selisih Opname
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Perbandingan angka sistem vs fisik riil, perhitungan nilai variansi rupiah, dan penetapan kode alasan wajib sebelum pengesahan final.
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('opname.count', $opname->id) }}" class="px-4 py-2 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition flex items-center gap-1.5 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
        <span>Edit Hitung Fisik</span>
      </a>
      <a href="{{ route('opname.show', $opname->id) }}" class="px-4 py-2 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition shadow-sm">
        Detail Sesi
      </a>
    </div>
  </div>

  <!-- Discrepancy KPI Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <!-- Card 1: Items Discrepancy -->
    <div class="bg-white rounded-2xl p-5 border border-[#ECEBF4] shadow-sm">
      <p class="text-xs font-bold text-textMuted uppercase tracking-wider">Barang Berselisih</p>
      <div class="flex items-baseline gap-2 mt-1">
        <h3 class="text-2xl font-extrabold {{ $opname->total_items_discrepancy > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
          {{ $opname->total_items_discrepancy }}
        </h3>
        <span class="text-xs text-textMuted font-semibold">dari {{ $opname->items->count() }} produk</span>
      </div>
      <p class="text-[11px] text-textMuted mt-1">Item yang memiliki selisih fisik vs sistem</p>
    </div>

    <!-- Card 2: Net Variance Value -->
    <div class="bg-white rounded-2xl p-5 border border-[#ECEBF4] shadow-sm">
      <p class="text-xs font-bold text-textMuted uppercase tracking-wider">Total Nilai Variansi Bersih</p>
      <h3 class="text-2xl font-extrabold mt-1 {{ $opname->total_variance_value < 0 ? 'text-rose-600' : ($opname->total_variance_value > 0 ? 'text-emerald-600' : 'text-sidebar') }}">
        {{ $opname->total_variance_value < 0 ? '-Rp ' . number_format(abs($opname->total_variance_value), 0, ',', '.') : 'Rp ' . number_format($opname->total_variance_value, 0, ',', '.') }}
      </h3>
      <p class="text-[11px] text-textMuted mt-1">Nilai rupiah berdasarkan HPP snapshot</p>
    </div>

    <!-- Card 3: Potential Loss -->
    <div class="bg-white rounded-2xl p-5 border border-[#ECEBF4] shadow-sm">
      <p class="text-xs font-bold text-textMuted uppercase tracking-wider">Total Kerugian Susut (Merah)</p>
      <h3 class="text-2xl font-extrabold text-rose-600 mt-1">
        Rp {{ number_format($opname->total_loss_value, 0, ',', '.') }}
      </h3>
      <p class="text-[11px] text-rose-500 font-semibold mt-1">Akan diposting ke Anomaly Stream</p>
    </div>
  </div>



  <!-- Reconciliation Form -->
  <form method="POST" action="{{ route('opname.reconcile.save', $opname->id) }}" id="reconcileForm" class="space-y-4">
    @csrf

    <div class="bg-white rounded-3xl border border-[#ECEBF4] overflow-hidden shadow-sm">
      <div class="p-4 border-b border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-panelBg/30">
        <span class="text-xs font-bold text-sidebar uppercase tracking-wider">
          Tabel Rekonsiliasi &amp; Penetapan Alasan Selisih
        </span>
        <span class="text-xs text-textMuted">
          Status Sesi: <strong class="text-purple-700">RECONCILIATION</strong>
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-sidebar text-white text-[11px] font-bold uppercase tracking-wider">
              <th class="py-3 px-4 w-10 text-center">No</th>
              <th class="py-3 px-4">Produk</th>
              <th class="py-3 px-4 text-center">Satuan</th>
              <th class="py-3 px-4 text-center">Snapshot Sistem</th>
              <th class="py-3 px-4 text-center">Hitung Fisik</th>
              <th class="py-3 px-4 text-center">Selisih Unit</th>
              <th class="py-3 px-4 text-right">HPP Snapshot</th>
              <th class="py-3 px-4 text-right">Nilai Selisih</th>
              <th class="py-3 px-4 w-64">Alasan Selisih <span class="text-rose-400">*</span></th>
              <th class="py-3 px-4 w-48">Catatan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4] text-xs text-textMain">
            @foreach($opname->items as $index => $item)
              @php
                $hasDiscrepancy = abs((float) $item->variance_qty) > 0.0001;
                $isNegative = (float) $item->variance_qty < 0;
              @endphp
              <tr class="hover:bg-panelBg/60 transition {{ $hasDiscrepancy ? ($isNegative ? 'bg-rose-50/25' : 'bg-blue-50/25') : '' }}">
                <td class="py-3.5 px-4 text-center text-textMuted font-bold">
                  {{ $index + 1 }}
                </td>
                <td class="py-3.5 px-4 font-semibold text-sidebar">
                  <div class="flex items-center gap-2.5">
                    <x-product-thumbnail :product="$item->product" size="sm" />
                    <div>
                      <div class="font-bold text-textMain text-sm">{{ $item->product->name }}</div>
                      <span class="text-[10px] text-textMuted font-mono">SKU: {{ $item->product->sku }}</span>
                    </div>
                  </div>
                </td>
                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                  <span class="text-[11px] font-bold text-textMuted">{{ $item->product->baseUom?->code ?? 'PCS' }}</span>
                </td>
                <td class="py-3.5 px-4 text-center font-mono font-bold text-textMain">
                  {{ number_format($item->system_qty_snapshot, 2) }}
                </td>
                <td class="py-3.5 px-4 text-center font-mono font-bold text-sidebar bg-panelBg/40">
                  {{ $item->physical_qty_counted !== null ? number_format($item->physical_qty_counted, 2) : '-' }}
                </td>
                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                  @if($hasDiscrepancy)
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
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      Tepat (0)
                    </span>
                  @endif
                </td>
                <td class="py-3.5 px-4 text-right font-mono text-[11px] text-textMuted whitespace-nowrap">
                  Rp {{ number_format($item->unit_cost_snapshot, 0, ',', '.') }}
                </td>
                <td class="py-3.5 px-4 text-right font-mono font-bold whitespace-nowrap">
                  @if($hasDiscrepancy)
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
                  @if($hasDiscrepancy)
                    <select 
                      name="reasons[{{ $item->id }}][reason_code]" 
                      required 
                      class="w-full text-xs rounded-xl border border-rose-300 p-2 bg-white focus:outline-none focus:border-sidebar font-semibold {{ empty($item->reason_code) || $item->reason_code === 'NONE' ? 'ring-2 ring-rose-400' : '' }}"
                    >
                      @foreach($reasonLabels as $code => $lbl)
                        <option value="{{ $code }}" {{ (string) $item->reason_code === (string) $code ? 'selected' : '' }}>
                          {{ $lbl }}
                        </option>
                      @endforeach
                    </select>
                  @else
                    <span class="text-textMuted text-[11px] italic">Tidak ada selisih</span>
                    <input type="hidden" name="reasons[{{ $item->id }}][reason_code]" value="NONE">
                  @endif
                </td>
                <td class="py-3.5 px-4">
                  @if($hasDiscrepancy)
                    <input 
                      type="text" 
                      name="reasons[{{ $item->id }}][notes]" 
                      value="{{ $item->notes }}" 
                      placeholder="Catatan..." 
                      class="w-full text-xs rounded-xl border border-[#ECEBF4] px-2.5 py-1.5 bg-white focus:outline-none focus:border-sidebar"
                    >
                  @else
                    <span class="text-gray-300">-</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Action Footer -->
      <div class="p-4 bg-panelBg border-t border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <p class="text-xs text-textMuted">
          Setiap baris yang memiliki selisih unit wajib memiliki alasan sebelum finalisasi.
        </p>

        <div class="flex items-center gap-2.5">
          <button type="submit" class="px-5 py-2.5 bg-sidebar hover:bg-slate-800 text-white text-xs font-bold rounded-2xl transition flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            <span>Simpan Alasan Selisih</span>
          </button>

          @if($user->hasPermission('opname.finalize') && !$user->isOwner())
            <button type="button" onclick="openFinalizeModal()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-2xl transition flex items-center gap-2 shadow-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span>Finalisasi Stok Opname</span>
            </button>
          @endif
        </div>
      </div>
    </div>
  </form>
</main>

<!-- Finalize Confirmation Modal -->
<div id="finalizeModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 border border-[#ECEBF4] shadow-2xl space-y-4">
    <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    
    <div>
      <h3 class="text-lg font-bold text-sidebar">Konfirmasi Pengesahan Final Opname</h3>
      <p class="text-xs text-textMuted mt-1 leading-relaxed">
        Tindakan ini akan mengunci sesi opname secara permanen, menyesuaikan saldo fisik di tabel <span class="font-mono text-sidebar">inventory_balances</span>, serta mencatat mutasi ledger audit (Merah jika minus).
      </p>
    </div>

    @if($opname->total_loss_value > 0)
      <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800">
        <p class="font-bold">Perhatian: Kerugian Selisih Negatif</p>
        <p class="text-[11px] mt-0.5">
          Terdapat selisih kurang fisik sebesar <strong>Rp {{ number_format($opname->total_loss_value, 0, ',', '.') }}</strong> yang akan diposting sebagai anomali Merah pada Accountability Board Owner.
        </p>
      </div>
    @endif

    <form method="POST" action="{{ route('opname.finalize', $opname->id) }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-xs font-bold text-sidebar mb-1">Catatan Pengesahan Final (Opsional)</label>
        <textarea name="final_notes" rows="2" placeholder="Catatan persetujuan akhir..." class="w-full text-xs rounded-xl border border-[#ECEBF4] p-2.5 bg-panelBg/50 focus:outline-none focus:border-sidebar"></textarea>
      </div>

      <div class="flex items-center justify-end gap-2.5 pt-2">
        <button type="button" onclick="closeFinalizeModal()" class="px-4 py-2 bg-panelBg hover:bg-white text-textMuted text-xs font-bold rounded-xl border border-[#ECEBF4]">
          Batal
        </button>
        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md">
          Ya, Sahkan &amp; Sesuaikan Saldo
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function openFinalizeModal() {
    document.getElementById('finalizeModal').classList.remove('hidden');
  }
  function closeFinalizeModal() {
    document.getElementById('finalizeModal').classList.add('hidden');
  }
</script>
@endsection
