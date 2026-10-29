@extends('layouts.app')

@section('title', 'Tambah Kategori Produk — PasarHub')
@section('page_title', 'Tambah Kategori Produk')
@section('page_subtitle', 'Daftarkan kelompok komoditas atau divisi produk baru dalam katalog.')

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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-textMain">Formulir Kategori Baru</h2>
                    <p class="text-xs text-textMuted mt-0.5">Daftarkan kelompok komoditas atau divisi produk baru dalam katalog.</p>
                </div>
            </div>
            <a href="{{ route('master.categories.index') }}" class="text-xs font-semibold text-textMuted hover:text-sidebar px-3 py-2 rounded-xl hover:bg-panelBg transition">
                ← Kembali ke Daftar Kategori
            </a>
        </div>

        <form method="POST" action="{{ route('master.categories.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Kode Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="Mis. CAT-BERAS, CAT-SAYUR"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('code') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-mono font-bold text-sidebar uppercase focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('code')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Mis. Beras & Biji-bijian"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('name') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('name')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Kategori Induk (Opsional)
                    </label>
                    <select name="parent_id" class="w-full px-4 py-2.5 bg-panelBg border @error('parent_id') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition cursor-pointer">
                        <option value="">Tidak Ada (Jadikan Kategori Utama)</option>
                        @foreach($parentCandidates as $pCat)
                            <option value="{{ $pCat->id }}" {{ old('parent_id') == $pCat->id ? 'selected' : '' }}>
                                {{ $pCat->name }} ({{ $pCat->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Deskripsi Singkat
                    </label>
                    <textarea name="description" rows="3" placeholder="Catatan kelompok komoditas atau ketentuan penyimpanan..."
                              class="w-full px-4 py-2.5 bg-panelBg border @error('description') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-[#ECEBF4] flex items-center justify-end gap-3">
                <a href="{{ route('master.categories.index') }}" class="px-5 py-2.5 bg-panelBg hover:bg-gray-200 text-textMuted text-xs font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Kategori</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
