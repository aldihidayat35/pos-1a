@extends('layouts.app')

@section('title', 'Detail Pembelian Emergency ' . $emergencyPurchase->purchase_number . ' — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Flash Success -->
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
        <a href="{{ route('store.emergency.index') }}" class="hover:underline">Pembelian Emergency</a>
        <span>/</span>
        <span class="text-textMain font-bold">{{ $emergencyPurchase->purchase_number }}</span>
      </div>
      <div class="flex items-center gap-3">
        <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
          {{ $emergencyPurchase->purchase_number }}
        </h1>
        <span class="px-2.5 py-1 rounded-xl text-xs font-extrabold bg-amber-50 text-brandOrange border border-amber-200 flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-brandOrange animate-pulse"></span>
          <span>Darurat Selesai (Stok Aktif)</span>
        </span>
      </div>
      <p class="text-xs text-textMuted mt-0.5">
        Dibukukan pada {{ $emergencyPurchase->posted_at?->format('d F Y, H:i') }} WIB oleh {{ $emergencyPurchase->creator?->name }}
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('store.emergency.index') }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
        ← Kembali ke Daftar
      </a>
      @if($user->hasPermission('emergency.input') || $user->isSuperAdmin())
        <a href="{{ route('store.emergency.create') }}" class="px-3.5 py-2 bg-brandOrange hover:bg-brandOrange/90 text-white font-bold text-xs rounded-xl transition shadow-sm">
          + Input Belanja Lagi
        </a>
      @endif
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left 2 Cols: Details & Stock Impact -->
    <div class="lg:col-span-2 space-y-6">
      <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
        <h3 class="font-bold text-sm text-textMain border-b border-[#ECEBF4] pb-3 flex items-center justify-between">
          <span>Rincian Pengadaan Darurat</span>
          <span class="text-xs text-textMuted font-mono">ID: #{{ $emergencyPurchase->id }}</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
          <div class="p-3 bg-panelBg/70 rounded-2xl border border-[#ECEBF4]">
            <span class="text-textMuted text-[10px] uppercase font-bold tracking-wider block mb-1">Lokasi Toko Bertambah Stok:</span>
            <span class="font-extrabold text-sidebar text-xs">{{ $emergencyPurchase->location->name }}</span>
            <span class="text-[10px] text-textMuted block">Kode: {{ $emergencyPurchase->location->code }}</span>
          </div>

          <div class="p-3 bg-panelBg/70 rounded-2xl border border-[#ECEBF4]">
            <span class="text-textMuted text-[10px] uppercase font-bold tracking-wider block mb-1">Pedagang / Kios Sumber:</span>
            <span class="font-extrabold text-sidebar text-xs">{{ $emergencyPurchase->merchant_name }}</span>
            <span class="text-[10px] text-textMuted block">Pasar Tradisional Sekitar</span>
          </div>

          <div class="p-3 bg-panelBg/70 rounded-2xl border border-[#ECEBF4]">
            <span class="text-textMuted text-[10px] uppercase font-bold tracking-wider block mb-1">Barang yang Dibeli:</span>
            <span class="font-extrabold text-sidebar text-xs">{{ $emergencyPurchase->product->name }}</span>
            <span class="text-[10px] text-textMuted block font-mono">SKU: {{ $emergencyPurchase->product->sku }}</span>
          </div>

          <div class="p-3 bg-panelBg/70 rounded-2xl border border-[#ECEBF4]">
            <span class="text-textMuted text-[10px] uppercase font-bold tracking-wider block mb-1">Kuantitas Masuk:</span>
            <span class="font-black text-emerald-600 text-sm">
              +{{ number_format($emergencyPurchase->qty_input, 2) }} {{ $emergencyPurchase->productUom?->uom?->symbol }}
            </span>
            <span class="text-[10px] text-textMuted block">
              (Konversi: {{ number_format($emergencyPurchase->qty_base, 2) }} {{ $emergencyPurchase->product->baseUom?->symbol }})
            </span>
          </div>

          <div class="p-3 bg-panelBg/70 rounded-2xl border border-[#ECEBF4]">
            <span class="text-textMuted text-[10px] uppercase font-bold tracking-wider block mb-1">Harga Beli Satuan:</span>
            <span class="font-extrabold text-sidebar text-xs">
              Rp {{ number_format($emergencyPurchase->unit_cost_input, 0, ',', '.') }} / {{ $emergencyPurchase->productUom?->uom?->symbol }}
            </span>
          </div>

          <div class="p-3 bg-amber-50/70 rounded-2xl border border-amber-200">
            <span class="text-amber-800 text-[10px] uppercase font-bold tracking-wider block mb-1">Total Biaya Tunai:</span>
            <span class="font-black text-amber-700 text-base font-mono">
              Rp {{ number_format($emergencyPurchase->total_amount, 0, ',', '.') }}
            </span>
          </div>
        </div>

        @if($emergencyPurchase->notes)
          <div class="pt-2">
            <span class="text-xs font-bold text-textMain block mb-1">Catatan Tambahan:</span>
            <div class="p-3 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs text-textMain italic">
              "{{ $emergencyPurchase->notes }}"
            </div>
          </div>
        @endif
      </div>

      <!-- Audit Anomaly Status Card -->
      <div class="p-5 bg-white rounded-3xl border border-[#ECEBF4] shadow-sm space-y-3">
        <h3 class="font-bold text-sm text-textMain flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-brandOrange"></span>
          <span>Status Audit & Transparansi Oranye</span>
        </h3>
        <p class="text-xs text-textMuted leading-relaxed">
          Sesuai SOP Pasar Terpadu (Fase 4), pengadaan darurat tanpa approval bertingkat diizinkan agar kasir toko tidak mengalami <em>out-of-stock</em>. Transaksi ini telah otomatis menghasilkan:
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
          <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 space-y-1">
            <div class="font-extrabold text-amber-900 flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-brandOrange"></span>
              <span>Mutasi Stok Oranye (#F59E0B)</span>
            </div>
            <p class="text-[11px] text-amber-800">
              Buku besar mutasi menandai tipe <code class="bg-white px-1 py-0.5 rounded text-[10px] text-brandOrange font-bold">EMERGENCY_IN</code> sebagai jalur audit khusus non-gudang.
            </p>
          </div>

          <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 space-y-1">
            <div class="font-extrabold text-amber-900 flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-brandOrange"></span>
              <span>Audit Anomaly Event Terbit</span>
            </div>
            <p class="text-[11px] text-amber-800">
              Notifikasi audit prioritas Oranye dipublikasikan ke papan kendali Owner & Auditor untuk review keabsahan nota.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Right 1 Col: Receipt Photo Proof -->
    <div class="space-y-6">
      <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
        <h3 class="font-bold text-sm text-textMain border-b border-[#ECEBF4] pb-3 flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span>Bukti Fisik Nota Pembelian</span>
        </h3>

        @if($emergencyPurchase->receipt_photo_path)
          <div class="space-y-3">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-gray-50 flex items-center justify-center p-2 group">
              <img src="{{ asset('storage/' . $emergencyPurchase->receipt_photo_path) }}"
                   alt="Foto Nota {{ $emergencyPurchase->purchase_number }}"
                   class="max-h-80 w-auto rounded-xl object-contain shadow-sm transition group-hover:scale-105 duration-300">
            </div>

            <div class="text-center">
              <a href="{{ asset('storage/' . $emergencyPurchase->receipt_photo_path) }}" target="_blank"
                 class="inline-flex items-center gap-1.5 text-xs font-bold text-brandOrange hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                <span>Buka Foto Resolusi Penuh</span>
              </a>
            </div>
          </div>
        @else
          <div class="p-6 text-center rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold">
            Peringatan: Dokumen ini tidak memiliki foto fisik nota terlampir!
          </div>
        @endif
      </div>
    </div>
  </div>
</main>
@endsection

@section('right_panel')
  @include('partials.right-panel')
@endsection
