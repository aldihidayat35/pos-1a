@extends('layouts.app')

@section('title', 'Tambah Satuan UOM — PasarHub')
@section('page_title', 'Tambah Satuan UOM')
@section('page_subtitle', 'Daftarkan unit kuantitas fisik komoditas pasar dan aturan presisi desimal.')

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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-textMain">Formulir Satuan UOM Baru</h2>
                    <p class="text-xs text-textMuted mt-0.5">Daftarkan unit kuantitas fisik komoditas pasar dan aturan presisi desimal.</p>
                </div>
            </div>
            <a href="{{ route('master.uoms.index') }}" class="text-xs font-semibold text-textMuted hover:text-sidebar px-3 py-2 rounded-xl hover:bg-panelBg transition">
                ← Kembali ke Daftar Satuan
            </a>
        </div>

        <form method="POST" action="{{ route('master.uoms.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Kode Satuan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="Mis. KG, LTR, PCS, DUS"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('code') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-mono font-bold text-sidebar uppercase focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('code')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Nama Lengkap Satuan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Mis. Kilogram, Liter, Piece..."
                           class="w-full px-4 py-2.5 bg-panelBg border @error('name') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('name')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Simbol Satuan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="symbol" value="{{ old('symbol') }}" required placeholder="Mis. kg, L, pcs"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('symbol') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('symbol')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Presisi Desimal <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="precision" value="{{ old('precision', 0) }}" min="0" max="4" required
                           class="w-full px-4 py-2.5 bg-panelBg border @error('precision') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('precision')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Dimensi Fisik <span class="text-red-500">*</span>
                    </label>
                    <select name="dimension" required class="w-full px-4 py-2.5 bg-panelBg border @error('dimension') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition cursor-pointer">
                        <option value="COUNT" {{ old('dimension') === 'COUNT' ? 'selected' : '' }}>COUNT (Hitungan Unit / Buah / Dus / Pcs)</option>
                        <option value="WEIGHT" {{ old('dimension') === 'WEIGHT' ? 'selected' : '' }}>WEIGHT (Berat / Massa / Kg / Gram / Ton)</option>
                        <option value="VOLUME" {{ old('dimension') === 'VOLUME' ? 'selected' : '' }}>VOLUME (Kapasitas Cair / Liter / Milliliter)</option>
                    </select>
                    @error('dimension')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-textMuted mt-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Konversi multi-satuan nantinya hanya diizinkan dalam dimensi fisik yang sejenis.</span>
                    </p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-[#ECEBF4] flex items-center justify-end gap-3">
                <a href="{{ route('master.uoms.index') }}" class="px-5 py-2.5 bg-panelBg hover:bg-gray-200 text-textMuted text-xs font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Satuan UOM</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
