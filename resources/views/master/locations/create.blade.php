@extends('layouts.app')

@section('title', 'Tambah Lokasi Operasional — PasarHub')
@section('page_title', 'Tambah Lokasi Baru')
@section('page_subtitle', 'Daftarkan gudang pusat, gerai toko ritel, atau pos transit pergerakan stok.')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Sub-Nav Tabs -->
    @include('master.partials.nav')

    <!-- Form Container -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-[#ECEBF4] shadow-sm">
        <div class="border-b border-[#ECEBF4] pb-4 mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-sidebar text-white flex items-center justify-center shadow-xs shrink-0">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-textMain">Formulir Lokasi Operasional Baru</h2>
                    <p class="text-xs text-textMuted mt-0.5">Daftarkan gudang pusat, gerai toko ritel, atau pos transit pergerakan stok.</p>
                </div>
            </div>
            <a href="{{ route('master.locations.index') }}" class="text-xs font-semibold text-textMuted hover:text-sidebar px-3 py-2 rounded-xl hover:bg-panelBg transition">
                ← Kembali ke Daftar Lokasi
            </a>
        </div>

        <form method="POST" action="{{ route('master.locations.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Kode Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="Mis. STR-PASAR-2, WH-TIMUR"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('code') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-mono font-bold text-sidebar uppercase focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('code')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Nama Lokasi / Cabang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Mis. Toko Cabang Pasar Mayestik"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('name') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('name')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Tipe Operasional <span class="text-red-500">*</span>
                    </label>
                    <select name="type" required class="w-full px-4 py-2.5 bg-panelBg border @error('type') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition cursor-pointer">
                        <option value="STORE" {{ old('type') === 'STORE' ? 'selected' : '' }}>STORE (Toko Ritel & Kasir POS)</option>
                        <option value="WAREHOUSE" {{ old('type') === 'WAREHOUSE' ? 'selected' : '' }}>WAREHOUSE (Gudang Pasokan Pusat)</option>
                        <option value="TRANSIT" {{ old('type') === 'TRANSIT' ? 'selected' : '' }}>TRANSIT (Lokasi Logis Barang Bergerak)</option>
                    </select>
                    @error('type')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        No. Telepon Operasional
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="021-xxxxxxxx / 08..."
                           class="w-full px-4 py-2.5 bg-panelBg border @error('phone') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('phone')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Alamat Fisik Cabang
                    </label>
                    <textarea name="address" rows="3" placeholder="Alamat lengkap lokasi, blok ruko, atau patokan gudang..."
                              class="w-full px-4 py-2.5 bg-panelBg border @error('address') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-[#ECEBF4] flex items-center justify-end gap-3">
                <a href="{{ route('master.locations.index') }}" class="px-5 py-2.5 bg-panelBg hover:bg-gray-200 text-textMuted text-xs font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Lokasi Cabang</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
