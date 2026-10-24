@extends('layouts.app')

@section('title', 'Lokasi Gudang & Toko — PasarHub')
@section('page_title', 'Master Lokasi Operasional')
@section('page_subtitle', 'Pengelolaan cabang toko ritel, gudang pusat pasokan, dan lokasi transit antar cabang.')

@section('content')
<div class="space-y-6">
    <!-- Sub-Nav Tabs -->
    @include('master.partials.nav')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Add Location Form Card (Left Col: 4) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-[#ECEBF4] shadow-sm">
            <div class="border-b border-[#ECEBF4] pb-3 mb-5">
                <h3 class="text-sm font-bold text-textMain uppercase tracking-wider">+ Tambah Lokasi Baru</h3>
                <p class="text-[11px] text-textMuted mt-0.5">Daftarkan gudang pusat, gerai toko, atau pos transit.</p>
            </div>

            <form method="POST" action="{{ route('master.locations.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase mb-1.5">
                        Kode Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="Mis. STR-PASAR-2, WH-TIMUR"
                           class="w-full px-3.5 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-mono font-bold text-sidebar uppercase focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase mb-1.5">
                        Nama Lokasi / Cabang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Mis. Toko Cabang Pasar Mayestik"
                           class="w-full px-3.5 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase mb-1.5">
                        Tipe Operasional <span class="text-red-500">*</span>
                    </label>
                    <select name="type" required class="w-full px-3.5 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                        <option value="STORE" {{ old('type') === 'STORE' ? 'selected' : '' }}>STORE (Toko Ritel & Kasir POS)</option>
                        <option value="WAREHOUSE" {{ old('type') === 'WAREHOUSE' ? 'selected' : '' }}>WAREHOUSE (Gudang Pasokan Pusat)</option>
                        <option value="TRANSIT" {{ old('type') === 'TRANSIT' ? 'selected' : '' }}>TRANSIT (Lokasi Logis Barang Bergerak)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase mb-1.5">No. Telepon Operasional</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="021-..."
                           class="w-full px-3.5 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-textMain uppercase mb-1.5">Alamat Fisik Cabang</label>
                    <textarea name="address" rows="2" placeholder="Alamat lengkap lokasi..."
                              class="w-full px-3.5 py-2.5 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">{{ old('address') }}</textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition shadow-sm">
                    Simpan Lokasi Cabang
                </button>
            </form>
        </div>

        <!-- Locations Table Card (Right Col: 8) -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-[#ECEBF4] overflow-hidden shadow-sm" data-async-table="locations-table">
            <div class="p-5 border-b border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-sm text-textMain">Daftar Lokasi Operasional</h3>
                    <p class="text-xs text-textMuted">Total {{ $locations->total() }} titik operasional aktif</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-1.5 bg-panelBg px-2.5 py-1.5 rounded-xl border border-[#E6E4F0]">
                        <span class="text-[11px] font-semibold text-textMuted whitespace-nowrap">Baris:</span>
                        <select data-table-perpage="locations-table"
                                class="bg-transparent text-xs font-bold text-textMain focus:outline-none cursor-pointer">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>

                    <form method="GET" action="{{ route('master.locations.index') }}" class="flex flex-wrap items-center gap-2">
                        <select name="type" onchange="this.form.submit()" class="px-2.5 py-2 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:outline-none">
                            <option value="">Semua Tipe</option>
                            <option value="STORE" {{ request('type') === 'STORE' ? 'selected' : '' }}>STORE (Toko)</option>
                            <option value="WAREHOUSE" {{ request('type') === 'WAREHOUSE' ? 'selected' : '' }}>WAREHOUSE (Gudang)</option>
                            <option value="TRANSIT" {{ request('type') === 'TRANSIT' ? 'selected' : '' }}>TRANSIT (Pos)</option>
                        </select>

                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, alamat..."
                               data-table-search="locations-table"
                               class="px-3.5 py-2 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-medium text-textMain focus:outline-none focus:border-sidebar w-36 sm:w-48">

                        @if(request('search') || request('type'))
                            <a href="{{ route('master.locations.index') }}" class="text-xs text-textMuted hover:text-red-500 font-semibold px-1.5">Reset</a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Desktop View: Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-panelBg/80 border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4">Kode Lokasi</th>
                            <th class="py-3.5 px-4">Nama Cabang</th>
                            <th class="py-3.5 px-4 text-center">Tipe</th>
                            <th class="py-3.5 px-4">Kontak & Alamat</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ECEBF4]">
                        @forelse($locations as $loc)
                            <tr class="hover:bg-panelBg/40 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-sidebar">{{ $loc->code }}</td>
                                <td class="py-3.5 px-4 font-bold text-textMain text-sm">{{ $loc->name }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($loc->type === 'WAREHOUSE')
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                            GUDANG PUSAT
                                        </span>
                                    @elseif($loc->type === 'STORE')
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            TOKO RITEL
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                            POS TRANSIT
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="text-textMain block font-medium">{{ $loc->phone ?: '—' }}</span>
                                    <span class="text-[11px] text-textMuted block truncate max-w-xs">{{ $loc->address ?: '—' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($loc->is_active)
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if($loc->is_active)
                                        <form method="POST" action="{{ route('master.locations.destroy', $loc) }}" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan lokasi [{{ $loc->name }}]?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-textMuted hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Nonaktifkan Lokasi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-textMuted text-xs">Belum ada lokasi terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: Card Stack -->
            <div class="block md:hidden divide-y divide-[#ECEBF4]">
                @forelse($locations as $loc)
                    <div class="p-4 space-y-3 hover:bg-panelBg/30 transition">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="inline-block px-2 py-0.5 rounded-md font-mono text-[10px] font-bold bg-sidebar/10 text-sidebar border border-sidebar/20">
                                    {{ $loc->code }}
                                </span>
                                <h4 class="font-bold text-sm text-textMain mt-1">{{ $loc->name }}</h4>
                            </div>
                            <div class="text-right">
                                @if($loc->type === 'WAREHOUSE')
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                        Gudang
                                    </span>
                                @elseif($loc->type === 'STORE')
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        Toko Ritel
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                        Transit
                                    </span>
                                @endif
                                <span class="block text-[10px] mt-0.5 font-bold {{ $loc->is_active ? 'text-emerald-600' : 'text-gray-400' }}">
                                    {{ $loc->is_active ? '● Aktif' : '○ Nonaktif' }}
                                </span>
                            </div>
                        </div>

                        <div class="text-xs bg-panelBg/60 p-2.5 rounded-xl border border-[#ECEBF4] space-y-1">
                            @if($loc->phone)
                                <div class="flex items-center gap-1.5 text-textMain">
                                    <span class="text-[10px] uppercase font-bold text-textMuted w-14">Telp:</span>
                                    <a href="tel:{{ $loc->phone }}" class="text-sidebar hover:underline font-semibold">{{ $loc->phone }}</a>
                                </div>
                            @endif
                            <div class="flex items-start gap-1.5 text-textMuted">
                                <span class="text-[10px] uppercase font-bold text-textMuted w-14 shrink-0">Alamat:</span>
                                <span class="text-[11px] leading-tight">{{ $loc->address ?: 'Tanpa alamat lengkap' }}</span>
                            </div>
                        </div>

                        @if($loc->is_active)
                            <div class="flex items-center justify-end pt-1">
                                <form method="POST" action="{{ route('master.locations.destroy', $loc) }}" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan lokasi [{{ $loc->name }}]?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        Nonaktifkan
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-8 px-4 text-center text-textMuted text-xs">Belum ada lokasi terdaftar.</div>
                @endforelse
            </div>

            @if($locations->hasPages())
                <div class="p-4 border-t border-[#ECEBF4] bg-panelBg/30">
                    {{ $locations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
