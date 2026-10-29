@extends('layouts.app')

@section('title', 'Tambah Suplayer — PasarHub')
@section('page_title', 'Tambah Suplayer Baru')
@section('page_subtitle', 'Daftarkan vendor mitra, petani produsen, atau distributor resmi pasokan.')

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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-textMain">Formulir Suplayer Baru</h2>
                    <p class="text-xs text-textMuted mt-0.5">Daftarkan vendor mitra, petani produsen, atau distributor resmi pasokan.</p>
                </div>
            </div>
            <a href="{{ route('master.suppliers.index') }}" class="text-xs font-semibold text-textMuted hover:text-sidebar px-3 py-2 rounded-xl hover:bg-panelBg transition">
                ← Kembali ke Daftar Suplayer
            </a>
        </div>

        <form method="POST" action="{{ route('master.suppliers.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Kode Suplayer <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="supplier_code" value="{{ old('supplier_code') }}" required placeholder="Mis. SUP-005, SUP-DIST-01"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('supplier_code') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-mono font-bold text-sidebar uppercase focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('supplier_code')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Nama Perusahaan / Pemasok <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Mis. PT Sumber Pangan Makmur"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('name') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('name')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Kontak Person (PIC)
                    </label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" placeholder="Nama PIC Sales / Operasional"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('contact_person') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('contact_person')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        No. Telepon / WhatsApp
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('phone') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('phone')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Email Resmi
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="sales@perusahaan.com"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('email') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('email')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Alamat Gudang / Kantor Pengiriman
                    </label>
                    <textarea name="address" rows="3" placeholder="Alamat lengkap titik penjemputan barang atau penagihan..."
                              class="w-full px-4 py-2.5 bg-panelBg border @error('address') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-[#ECEBF4] flex items-center justify-end gap-3">
                <a href="{{ route('master.suppliers.index') }}" class="px-5 py-2.5 bg-panelBg hover:bg-gray-200 text-textMuted text-xs font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Data Suplayer</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
