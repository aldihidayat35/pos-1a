@extends('layouts.app')

@section('title', 'Operasional Toko & Kasir POS — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-7">
  <!-- RBAC Access Warning -->
  @if(!$user->hasPermission('pos.access') && !$user->isOwner() && !$user->isSuperAdmin())
    <div class="p-4 rounded-2xl bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E] text-xs font-semibold flex items-center gap-3">
      <svg class="w-5 h-5 text-[#F59E0B] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <div>
        <p class="font-bold">Akses Dibatasi — Khusus Pengelola Toko & Kasir</p>
        <p class="text-[11px] text-[#92400E]/90 mt-0.5">Akun Anda ({{ $user->primaryRole()?->label ?? 'User' }}) tidak memiliki izin transaksi kasir POS. Otorisasi penjualan dan shift laci uang dilindungi di server.</p>
      </div>
    </div>
  @endif

  <!-- Header Banner -->
  <div class="bg-panelBg p-6 sm:p-7 rounded-[26px] border border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sidebar text-white uppercase tracking-wider">GERAI TOKO PASAR</span>
        <span class="text-xs text-textMuted">• {{ $activeLocation?->name ?? 'Toko Pasar Raya' }} ({{ $activeLocation?->code ?? 'STR-PASAR-1' }})</span>
      </div>
      <h1 class="font-display text-2xl font-bold text-textMain mt-1.5">Operasional Toko & Kasir POS</h1>
      <p class="text-xs text-textMuted mt-1 max-w-xl">
        Pusat penjualan ritel cepat, checkout multi-metode (Tunai/QRIS), shift laci kasir, dan belanja emergency toko berbukti nota.
      </p>
    </div>

    <div class="flex items-center gap-2 shrink-0">
      <button onclick="openToast('Terminal POS Kasir Ritel: Modul lengkap aktif pada Fase 5!')" class="px-5 py-3 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-2xl transition flex items-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span>Buka Kasir POS Ritel</span>
      </button>
    </div>
  </div>

  <!-- Quick Category Folders -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="bg-brandOrange text-white p-5 rounded-[22px]">
      <p class="text-xs opacity-85 uppercase font-bold">Kategori</p>
      <h3 class="font-display font-bold text-lg mt-1">Beras & Biji</h3>
      <p class="text-2xl font-extrabold mt-3">{{ \App\Models\Product::where('category_id', 1)->count() }} <span class="text-xs font-normal">SKU</span></p>
    </div>
    <div class="bg-brandCoral text-white p-5 rounded-[22px]">
      <p class="text-xs opacity-85 uppercase font-bold">Kategori</p>
      <h3 class="font-display font-bold text-lg mt-1">Minyak & Lemak</h3>
      <p class="text-2xl font-extrabold mt-3">{{ \App\Models\Product::where('category_id', 2)->count() }} <span class="text-xs font-normal">SKU</span></p>
    </div>
    <div class="bg-brandCyan text-sidebar p-5 rounded-[22px]">
      <p class="text-xs opacity-75 uppercase font-bold">Kategori</p>
      <h3 class="font-display font-bold text-lg mt-1">Gula & Manis</h3>
      <p class="text-2xl font-extrabold mt-3">{{ \App\Models\Product::where('category_id', 3)->count() }} <span class="text-xs font-normal">SKU</span></p>
    </div>
    <div class="bg-sidebar text-white p-5 rounded-[22px]">
      <p class="text-xs opacity-85 uppercase font-bold">Kategori</p>
      <h3 class="font-display font-bold text-lg mt-1">Susu & Olahan</h3>
      <p class="text-2xl font-extrabold mt-3">{{ \App\Models\Product::where('category_id', 5)->count() }} <span class="text-xs font-normal">SKU</span></p>
    </div>
  </div>

  <!-- Store Inventory Snapshot -->
  <div class="bg-white rounded-3xl p-6 border border-[#ECEBF4] shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-4">
      <div>
        <h3 class="font-bold text-base text-textMain">Ketersediaan Stok di Toko Aktif</h3>
        <p class="text-xs text-textMuted mt-0.5">Daftar produk siap jual di kasir ritel.</p>
      </div>
      <a href="{{ route('master.products.index') }}" class="text-xs font-bold text-sidebar hover:underline">
        Lihat Seluruh Katalog Master →
      </a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-panelBg/80 text-textMuted font-bold uppercase tracking-wider text-[11px]">
          <tr>
            <th class="py-3 px-4">SKU</th>
            <th class="py-3 px-4">Nama Barang</th>
            <th class="py-3 px-4">Kategori</th>
            <th class="py-3 px-4">Satuan Dasar</th>
            <th class="py-3 px-4 text-center">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @foreach(\App\Models\Product::with(['category', 'baseUom'])->take(5)->get() as $p)
            <tr class="hover:bg-panelBg/30 transition">
              <td class="py-3 px-4 font-mono font-bold text-sidebar">{{ $p->sku }}</td>
              <td class="py-3 px-4 font-bold text-textMain">{{ $p->name }}</td>
              <td class="py-3 px-4 text-textMuted">{{ $p->category?->name }}</td>
              <td class="py-3 px-4">
                <span class="px-2 py-0.5 bg-sidebar/5 text-sidebar rounded font-bold">
                  {{ $p->baseUom?->name }} ({{ $p->baseUom?->symbol }})
                </span>
              </td>
              <td class="py-3 px-4 text-center">
                <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold text-[10px]">
                  Siap Jual
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</main>
@endsection

@section('right_panel')
  @include('partials.right-panel')
@endsection
