@extends('layouts.app')

@section('title', 'Penghitungan Fisik (Blind Count) — PasarHub')

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
        <span class="text-sidebar font-bold">Blind Count</span>
      </div>
      <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
        Layar Penghitungan Fisik Mandiri (Blind Count)
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Sesi: <span class="font-bold text-sidebar font-mono">{{ $opname->opname_number }}</span> • Lokasi: <span class="font-bold text-sidebar">{{ $opname->location->name }}</span>
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('opname.show', $opname->id) }}" class="px-4 py-2 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition shadow-sm">
        Kembali ke Ringkasan
      </a>
      @if($user->hasPermission('opname.reconcile') || $user->isSuperAdmin())
        <a href="{{ route('opname.reconcile', $opname->id) }}" class="px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold rounded-2xl border border-purple-200 transition shadow-sm flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          <span>Layar Rekonsiliasi</span>
        </a>
      @endif
    </div>
  </div>

  <!-- Blind Count Notice -->
  <div class="bg-amber-50/80 border border-amber-200 rounded-3xl p-5 flex items-start gap-4 text-xs text-amber-900 shadow-sm">
    <div class="w-10 h-10 rounded-2xl bg-amber-100 border border-amber-200 flex items-center justify-center text-amber-800 shrink-0">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
    </div>
    <div>
      <h4 class="font-extrabold text-amber-950 text-sm">Prinsip Blind Count Diaktifkan</h4>
      <p class="text-[11px] text-amber-800 mt-0.5 leading-relaxed">
        Angka stok sistem sengaja <strong>disembunyikan sepenuhnya</strong> pada antarmuka ini. Petugas wajib menghitung fisik barang yang ada di rak, kardus, atau gudang secara riil dan memasukkan angka riil ke kolom kuantitas fisik.
      </p>
    </div>
  </div>

  @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs font-semibold flex items-center gap-2">
      <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-700 text-xs">
      <p class="font-bold mb-1">Terdapat kesalahan:</p>
      <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Counting Table Form -->
  <form method="POST" action="{{ route('opname.count.save', $opname->id) }}" class="space-y-4">
    @csrf

    <div class="bg-white rounded-3xl border border-[#ECEBF4] overflow-hidden shadow-sm">
      <div class="p-4 border-b border-[#ECEBF4] flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="text-xs font-bold text-sidebar uppercase tracking-wider">
            Daftar Barang yang Wajib Dihitung ({{ $opname->items->count() }} Produk)
          </span>
        </div>
        <span class="text-[11px] text-textMuted font-medium">
          Petugas: <strong class="text-sidebar">{{ $user->name }}</strong>
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-sidebar text-white text-[11px] font-bold uppercase tracking-wider">
              <th class="py-3 px-4 w-12 text-center">No</th>
              <th class="py-3 px-4">Nama Produk &amp; Identitas</th>
              <th class="py-3 px-4">Kategori</th>
              <th class="py-3 px-4 text-center">Satuan Fisik</th>
              <th class="py-3 px-4 text-center w-52 bg-amber-600 text-white font-extrabold">
                Kuantitas Fisik Riil (Blind Input)
              </th>
              <th class="py-3 px-4 text-center">Status Hitung</th>
              <th class="py-3 px-4 text-right">Terakhir Dicatat</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4] text-xs text-textMain">
            @foreach($opname->items as $index => $item)
              <tr class="hover:bg-panelBg/60 transition {{ $item->physical_qty_counted !== null ? 'bg-emerald-50/20' : '' }}">
                <td class="py-3.5 px-4 text-center text-textMuted font-bold">
                  {{ $index + 1 }}
                </td>
                <td class="py-3.5 px-4 font-semibold text-sidebar">
                  <div class="flex items-center gap-2.5">
                    <x-product-thumbnail :product="$item->product" size="sm" />
                    <div>
                      <div class="font-bold text-textMain text-sm">{{ $item->product->name }}</div>
                      <div class="flex items-center gap-2 mt-0.5 text-[10px] text-textMuted font-mono">
                        <span>SKU: {{ $item->product->sku }}</span>
                        @if($item->product->primary_barcode)
                          <span>• Barcode: {{ $item->product->primary_barcode }}</span>
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
                <td class="py-3.5 px-4 text-textMuted whitespace-nowrap">
                  {{ $item->product->category?->name ?? '-' }}
                </td>
                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-panelBg text-sidebar border border-[#ECEBF4]">
                    {{ $item->product->baseUom?->name ?? 'Pcs' }} ({{ $item->product->baseUom?->code ?? 'PCS' }})
                  </span>
                </td>
                <td class="py-3.5 px-4 text-center bg-amber-50/40 border-x border-amber-100/70">
                  <div class="flex items-center justify-center gap-1.5">
                    <input 
                      type="number" 
                      step="0.001" 
                      min="0"
                      name="counts[{{ $item->id }}]" 
                      value="{{ old('counts.' . $item->id, $item->physical_qty_counted) }}" 
                      placeholder="0.00" 
                      class="w-32 text-center text-sm font-extrabold text-sidebar rounded-xl border border-amber-300 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-sm"
                      {{ $opname->isFinalized() ? 'disabled' : '' }}
                    >
                    <span class="text-xs font-bold text-sidebar">{{ $item->product->baseUom?->code ?? 'PCS' }}</span>
                  </div>
                </td>
                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                  @if($item->physical_qty_counted !== null)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                      <span>Terhitung</span>
                    </span>
                  @else
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                      Belum Diisi
                    </span>
                  @endif
                </td>
                <td class="py-3.5 px-4 text-right text-[11px] text-textMuted whitespace-nowrap">
                  @if($item->counted_at)
                    <span>{{ $item->counted_at->format('d M H:i') }}</span>
                    <span class="block text-[10px] text-textMuted">Oleh: {{ $item->counter?->name ?? '-' }}</span>
                  @else
                    <span class="text-gray-400">-</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Action Footer -->
      @if(!$opname->isFinalized() && !$opname->isCancelled())
        <div class="p-4 bg-panelBg border-t border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <p class="text-xs text-textMuted">
            Pastikan seluruh baris terisi sebelum beralih ke tahap rekonsiliasi selisih.
          </p>

          <div class="flex items-center gap-2.5">
            <button type="submit" name="save_only" value="1" class="px-5 py-2.5 bg-sidebar hover:bg-slate-800 text-white text-xs font-bold rounded-2xl transition flex items-center gap-2 shadow-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
              <span>Simpan Hasil Hitung Fisik</span>
            </button>

            @if($user->hasPermission('opname.reconcile') || $user->isSuperAdmin())
              <button type="submit" name="proceed_to_reconcile" value="1" class="px-5 py-2.5 bg-brandOrange hover:bg-orange-600 text-white text-xs font-bold rounded-2xl transition flex items-center gap-2 shadow-sm">
                <span>Simpan &amp; Beralih ke Rekonsiliasi</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
              </button>
            @endif
          </div>
        </div>
      @endif
    </div>
  </form>
</main>
@endsection
