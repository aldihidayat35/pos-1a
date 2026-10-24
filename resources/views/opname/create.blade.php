@extends('layouts.app')

@section('title', 'Buka Sesi Stock Opname — PasarHub')

@section('content')
<main class="flex-1 w-full max-w-4xl mx-auto space-y-6">
  <!-- Top Bar & Breadcrumb -->
  <div class="flex items-center justify-between">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('opname.index') }}" class="hover:text-sidebar transition">Stock Opname</a>
        <span>/</span>
        <span class="text-sidebar font-bold">Buka Sesi Baru</span>
      </div>
      <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
        Buka Sesi Stok Opname Mandiri
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Pembekuan snapshot kuantitas sistem untuk penghitungan fisik buta (Blind Count).
      </p>
    </div>

    <a href="{{ route('opname.index') }}" class="px-4 py-2 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition flex items-center gap-1.5 shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      <span>Kembali ke Daftar</span>
    </a>
  </div>

  @if($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-700 text-xs">
      <p class="font-bold mb-1">Terdapat kesalahan pengisian formulir:</p>
      <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Form Card -->
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ECEBF4] shadow-sm">
    <form method="POST" action="{{ route('opname.store') }}" class="space-y-6">
      @csrf

      <!-- Location Selection -->
      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-sidebar uppercase tracking-wider">
          Lokasi Penghitungan Opname <span class="text-rose-500">*</span>
        </label>
        <select name="location_id" required class="w-full text-xs rounded-xl border border-[#ECEBF4] px-3.5 py-2.5 bg-white focus:outline-none focus:border-sidebar font-semibold">
          @foreach($locations as $loc)
            <option value="{{ $loc->id }}" {{ (string) old('location_id', $activeLocation?->id) === (string) $loc->id ? 'selected' : '' }}>
              {{ $loc->name }} — {{ $loc->type }} ({{ $loc->address ?? 'Lokasi Terdaftar' }})
            </option>
          @endforeach
        </select>
        <p class="text-[11px] text-textMuted">
          Sistem akan membekukan kuantitas saldo inventaris saat ini pada lokasi yang dipilih.
        </p>
      </div>

      <!-- Scope Type -->
      <div class="space-y-2">
        <label class="block text-xs font-bold text-sidebar uppercase tracking-wider">
          Cakupan Barang yang Dihitung <span class="text-rose-500">*</span>
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <label class="cursor-pointer border border-[#ECEBF4] rounded-2xl p-4 flex flex-col justify-between hover:bg-panelBg/50 transition has-[:checked]:border-sidebar has-[:checked]:bg-indigo-50/40">
            <div class="flex items-center justify-between">
              <span class="font-bold text-xs text-sidebar">Semua Barang</span>
              <input type="radio" name="scope_type" value="ALL" class="accent-sidebar" {{ old('scope_type', 'ALL') === 'ALL' ? 'checked' : '' }} onchange="toggleScopeInputs()">
            </div>
            <p class="text-[11px] text-textMuted mt-2">
              Opname menyeluruh untuk seluruh produk aktif yang melacak stok di lokasi ini.
            </p>
          </label>

          <label class="cursor-pointer border border-[#ECEBF4] rounded-2xl p-4 flex flex-col justify-between hover:bg-panelBg/50 transition has-[:checked]:border-sidebar has-[:checked]:bg-indigo-50/40">
            <div class="flex items-center justify-between">
              <span class="font-bold text-xs text-sidebar">Per Kategori</span>
              <input type="radio" name="scope_type" value="CATEGORY" class="accent-sidebar" {{ old('scope_type') === 'CATEGORY' ? 'checked' : '' }} onchange="toggleScopeInputs()">
            </div>
            <p class="text-[11px] text-textMuted mt-2">
              Fokus hitung fisik pada kategori tertentu (misal: Sembako, Daging, Sayur Segar).
            </p>
          </label>

          <label class="cursor-pointer border border-[#ECEBF4] rounded-2xl p-4 flex flex-col justify-between hover:bg-panelBg/50 transition has-[:checked]:border-sidebar has-[:checked]:bg-indigo-50/40">
            <div class="flex items-center justify-between">
              <span class="font-bold text-xs text-sidebar">Pilihan Khusus</span>
              <input type="radio" name="scope_type" value="CUSTOM" class="accent-sidebar" {{ old('scope_type') === 'CUSTOM' ? 'checked' : '' }} onchange="toggleScopeInputs()">
            </div>
            <p class="text-[11px] text-textMuted mt-2">
              Pilih daftar produk spesifik yang ingin diaudit secara rolling count.
            </p>
          </label>
        </div>
      </div>

      <!-- Category Selector (Conditional) -->
      <div id="categoryContainer" class="space-y-1.5 {{ old('scope_type') === 'CATEGORY' ? '' : 'hidden' }}">
        <label class="block text-xs font-bold text-sidebar uppercase tracking-wider">
          Pilih Kategori Produk <span class="text-rose-500">*</span>
        </label>
        <select name="category_id" id="categorySelect" class="w-full text-xs rounded-xl border border-[#ECEBF4] px-3.5 py-2.5 bg-white focus:outline-none focus:border-sidebar">
          <option value="">-- Pilih Kategori --</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>
              {{ $cat->name }} ({{ $cat->code }})
            </option>
          @endforeach
        </select>
      </div>

      <!-- Custom Products Selector (Conditional) -->
      <div id="customContainer" class="space-y-1.5 {{ old('scope_type') === 'CUSTOM' ? '' : 'hidden' }}">
        <label class="block text-xs font-bold text-sidebar uppercase tracking-wider">
          Pilih Produk Spesifik <span class="text-rose-500">*</span>
        </label>
        <div class="max-h-56 overflow-y-auto border border-[#ECEBF4] rounded-2xl p-3 divide-y divide-[#ECEBF4]">
          @foreach($products as $prod)
            <label class="py-2 flex items-center justify-between hover:bg-panelBg/50 px-2 rounded-xl cursor-pointer text-xs">
              <div class="flex items-center gap-2.5">
                <input type="checkbox" name="product_ids[]" value="{{ $prod->id }}" class="rounded text-sidebar accent-sidebar" {{ in_array($prod->id, old('product_ids', [])) ? 'checked' : '' }}>
                <div>
                  <span class="font-bold text-sidebar">{{ $prod->name }}</span>
                  <span class="text-[10px] text-textMuted ml-1.5 font-mono">SKU: {{ $prod->sku }}</span>
                </div>
              </div>
              <span class="text-[11px] font-semibold text-textMuted">{{ $prod->baseUom?->name ?? 'Pcs' }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <!-- Notes -->
      <div class="space-y-1.5">
        <label class="block text-xs font-bold text-sidebar uppercase tracking-wider">
          Catatan / Alasan Audit (Opsional)
        </label>
        <textarea name="notes" rows="3" placeholder="Contoh: Opname tutup bulan berkala gudang pusat, fokus pengecekan susut beras dan minyak..." class="w-full text-xs rounded-xl border border-[#ECEBF4] p-3 bg-white focus:outline-none focus:border-sidebar">{{ old('notes') }}</textarea>
      </div>

      <!-- Information Alert -->
      <div class="bg-indigo-50/70 border border-indigo-100 rounded-2xl p-4 flex items-start gap-3 text-xs text-sidebar">
        <svg class="w-5 h-5 text-sidebar shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
          <h5 class="font-bold text-sidebar">Kebijakan Blind Count Terproteksi</h5>
          <p class="text-[11px] text-textMuted mt-0.5 leading-relaxed">
            Saat sesi dibuka, sistem membekukan saldo sistem (snapshot) secara otomatis. Staf yang bertugas menghitung fisik <strong>TIDAK AKAN melihat kuantitas sistem</strong> di layar mereka, menjamin hasil hitungan riil tanpa pengaruh angka sistem.
          </p>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="pt-2 flex items-center justify-end gap-3">
        <a href="{{ route('opname.index') }}" class="px-5 py-2.5 bg-panelBg hover:bg-white text-textMuted text-xs font-bold rounded-2xl border border-[#ECEBF4] transition">
          Batal
        </a>
        <button type="submit" class="px-6 py-2.5 bg-brandOrange hover:bg-orange-600 text-white text-xs font-bold rounded-2xl transition shadow-md flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          <span>Buka Sesi &amp; Bekukan Snapshot</span>
        </button>
      </div>
    </form>
  </div>
</main>

<script>
  function toggleScopeInputs() {
    const scopeType = document.querySelector('input[name="scope_type"]:checked')?.value;
    const catContainer = document.getElementById('categoryContainer');
    const customContainer = document.getElementById('customContainer');

    if (scopeType === 'CATEGORY') {
      catContainer.classList.remove('hidden');
      customContainer.classList.add('hidden');
    } else if (scopeType === 'CUSTOM') {
      catContainer.classList.add('hidden');
      customContainer.classList.remove('hidden');
    } else {
      catContainer.classList.add('hidden');
      customContainer.classList.add('hidden');
    }
  }
</script>
@endsection
