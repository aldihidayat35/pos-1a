@extends('layouts.app')

@section('title', 'Satuan Ukuran (UOM) — PasarHub')
@section('page_title', 'Master Satuan Ukuran (UOM)')
@section('page_subtitle', 'Pengelolaan dimensi satuan fisik (Weight, Count, Volume) dan tingkat presisi desimal.')

@section('content')
<div class="space-y-6">
    <!-- Sub-Nav Tabs -->
    @include('master.partials.nav')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Add UOM Form Card (Left Col: 4) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-[#ECEBF4] shadow-sm">
            <div class="border-b border-[#ECEBF4] pb-3 mb-5">
                <h3 class="text-sm font-bold text-textMain uppercase tracking-wider">+ Tambah Satuan UOM</h3>
                <p class="text-[11px] text-textMuted mt-0.5">Definisikan unit pengukuran fisik baru.</p>
            </div>

            <form method="POST" action="{{ route('master.uoms.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase mb-1.5">
                        Kode Satuan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="Mis. KG, LTR, PCS, DUS"
                           class="w-full px-3.5 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-mono font-bold text-sidebar uppercase focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Mis. Kilogram, Liter, Piece..."
                           class="w-full px-3.5 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-textMain uppercase mb-1.5">
                            Simbol <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="symbol" value="{{ old('symbol') }}" required placeholder="kg, L, pcs"
                               class="w-full px-3.5 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-textMain uppercase mb-1.5">
                            Presisi Desimal <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="precision" value="{{ old('precision', 0) }}" min="0" max="4" required
                               class="w-full px-3.5 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase mb-1.5">
                        Dimensi Fisik <span class="text-red-500">*</span>
                    </label>
                    <select name="dimension" required class="w-full px-3.5 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                        <option value="COUNT" {{ old('dimension') === 'COUNT' ? 'selected' : '' }}>COUNT (Hitungan Unit / Buah / Dus)</option>
                        <option value="WEIGHT" {{ old('dimension') === 'WEIGHT' ? 'selected' : '' }}>WEIGHT (Berat / Massa / Kg / Ton)</option>
                        <option value="VOLUME" {{ old('dimension') === 'VOLUME' ? 'selected' : '' }}>VOLUME (Kapasitas Cair / Liter)</option>
                    </select>
                    <span class="block text-[10px] text-textMuted mt-1">Konversi hanya diizinkan dalam dimensi yang sama.</span>
                </div>

                <button type="submit" class="w-full py-2.5 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition shadow-sm">
                    Simpan Satuan UOM
                </button>
            </form>
        </div>

        <!-- UOMs Table Card (Right Col: 8) -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-[#ECEBF4] overflow-hidden shadow-sm">
            <div class="p-5 border-b border-[#ECEBF4] flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-sm text-textMain">Daftar Satuan Fisik Terdaftar</h3>
                    <p class="text-xs text-textMuted">Total {{ $uoms->total() }} satuan pengukuran</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-panelBg/80 border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4">Kode & Simbol</th>
                            <th class="py-3.5 px-4">Nama Satuan</th>
                            <th class="py-3.5 px-4 text-center">Dimensi Fisik</th>
                            <th class="py-3.5 px-4 text-center">Presisi</th>
                            <th class="py-3.5 px-4 text-center">Produk Terkait</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ECEBF4]">
                        @forelse($uoms as $u)
                            <tr class="hover:bg-panelBg/40 transition">
                                <td class="py-3.5 px-4">
                                    <span class="font-mono font-bold text-sidebar bg-sidebar/5 px-2 py-0.5 rounded border border-sidebar/10">{{ $u->code }}</span>
                                    <span class="font-bold text-textMuted ml-1.5">({{ $u->symbol }})</span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-textMain text-sm">{{ $u->name }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($u->dimension === 'WEIGHT')
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            WEIGHT (Berat)
                                        </span>
                                    @elseif($u->dimension === 'COUNT')
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                            COUNT (Hitungan)
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                            VOLUME (Cairan)
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-textMuted">{{ $u->precision }} desimal</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-panelBg text-textMain border border-[#ECEBF4]">
                                        {{ $u->base_products_count }} Base SKU
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <form method="POST" action="{{ route('master.uoms.destroy', $u) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus satuan [{{ $u->name }}]?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-textMuted hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Satuan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-textMuted text-xs">Belum ada satuan ukuran terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($uoms->hasPages())
                <div class="p-4 border-t border-[#ECEBF4] bg-panelBg/30">
                    {{ $uoms->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
