@extends('layouts.app')

@section('title', 'Member & Kemitraan B2B — PasarHub')
@section('page_title', 'Master Pelanggan, Member & Kemitraan B2B')
@section('page_subtitle', 'Pengelolaan data member ritel, mitra grosir B2B, tier harga, dan plafon kredit.')

@section('content')
<div class="space-y-6">
    <!-- Sub-Nav Tabs -->
    @include('master.partials.nav')    <!-- Members Table Card (Full Width) -->
    <div data-async-table="members-table" class="w-full bg-white rounded-3xl border border-[#ECEBF4] overflow-hidden shadow-sm">
        <div class="p-4 sm:p-5 border-b border-[#ECEBF4] flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white">
            <div>
                <h3 class="font-bold text-sm text-textMain">Daftar Member & Mitra B2B</h3>
                <p class="text-xs text-textMuted">Total {{ $members->total() }} pelanggan terdaftar</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Filter Tipe & Search Form -->
                <form method="GET" action="{{ route('master.members.index') }}" class="flex flex-wrap items-center gap-2">
                    <select name="type" onchange="this.form.submit()" class="px-2.5 py-2 bg-panelBg border border-[#E6E4F0] rounded-xl text-xs font-semibold text-sidebar focus:outline-none focus:border-sidebar transition cursor-pointer">
                        <option value="">Semua Tipe</option>
                        <option value="RETAIL" {{ request('type') === 'RETAIL' ? 'selected' : '' }}>Hanya Ritel</option>
                        <option value="B2B" {{ request('type') === 'B2B' ? 'selected' : '' }}>Hanya B2B</option>
                    </select>

                    <div class="relative">
                        <input type="text" 
                               name="search"
                               data-table-search="members-table" 
                               value="{{ request('search') }}" 
                               placeholder="Cari nama, kode, no. hp..." 
                               class="pl-8 pr-3 py-2 text-xs bg-panelBg border border-[#E6E4F0] rounded-xl focus:bg-white focus:outline-none focus:border-sidebar transition font-medium w-36 sm:w-48">
                        <svg class="w-3.5 h-3.5 text-textMuted absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    @if(request('search') || request('type'))
                        <a href="{{ route('master.members.index') }}" class="text-xs text-textMuted hover:text-red-500 font-semibold px-1">Reset</a>
                    @endif
                </form>

                <!-- Per Page Selector -->
                <select data-table-perpage="members-table" class="text-xs bg-panelBg border border-[#E6E4F0] px-3 py-2 rounded-xl font-bold text-sidebar focus:outline-none focus:border-sidebar transition cursor-pointer">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / hal</option>
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 / hal</option>
                    <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25 / hal</option>
                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50 / hal</option>
                </select>

                <!-- Tambah Member Button -->
                <a href="{{ route('master.members.create') }}" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Member / Mitra</span>
                </a>
            </div>
        </div>

            <!-- Desktop View: Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-panelBg/80 border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-3.5 px-4">Kode Member</th>
                            <th class="py-3.5 px-4">Nama Pelanggan / Toko</th>
                            <th class="py-3.5 px-4 text-center">Tipe & Tier</th>
                            <th class="py-3.5 px-4 text-right">Plafon Kredit</th>
                            <th class="py-3.5 px-4">Kontak</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ECEBF4]">
                        @forelse($members as $m)
                            <tr class="hover:bg-panelBg/40 transition">
                                <td class="py-3.5 px-4 font-mono">
                                    <span class="font-bold text-sidebar bg-sidebar/5 px-2 py-0.5 rounded border border-sidebar/10">{{ $m->member_code }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-textMain text-sm block">{{ $m->name }}</span>
                                    <span class="text-[11px] text-textMuted">{{ $m->address ?: 'Tanpa alamat' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($m->type === 'B2B')
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                            B2B Mitra
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Ritel Umum
                                        </span>
                                    @endif

                                    @if($m->tier)
                                        <span class="block text-[10px] text-brandOrange font-bold mt-0.5">
                                            {{ $m->tier->name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono">
                                    @if($m->type === 'B2B')
                                        <span class="font-bold text-textMain">Rp {{ number_format($m->credit_limit, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-textMuted">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-textMuted">
                                    {{ $m->phone ?: '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Lihat Kartu Digital QR -->
                                        <a href="{{ route('master.members.card', $m) }}" 
                                           class="px-2.5 py-1.5 bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white rounded-xl text-[11px] font-bold transition flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                            </svg>
                                            <span>Kartu QR</span>
                                        </a>

                                        @if($m->is_active)
                                            <form method="POST" action="{{ route('master.members.destroy', $m) }}" 
                                                  data-confirm="Apakah Anda yakin ingin menonaktifkan member [{{ $m->name }}]?" data-confirm-title="Nonaktifkan Member" data-confirm-btn="Ya, Nonaktifkan" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-textMuted hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Nonaktifkan Member">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-textMuted text-xs">Belum ada member terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: Card Stack -->
            <div class="block md:hidden divide-y divide-[#ECEBF4]">
                @forelse($members as $m)
                    <div class="p-4 space-y-3 hover:bg-panelBg/30 transition">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="inline-block px-2 py-0.5 rounded-md font-mono text-[10px] font-bold bg-sidebar/10 text-sidebar border border-sidebar/20">
                                    {{ $m->member_code }}
                                </span>
                                <h4 class="font-bold text-sm text-textMain mt-1">{{ $m->name }}</h4>
                                <p class="text-[11px] text-textMuted">{{ $m->address ?: 'Tanpa alamat domisili' }}</p>
                            </div>
                            <div class="text-right">
                                @if($m->type === 'B2B')
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                        B2B Mitra
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Ritel Umum
                                    </span>
                                @endif
                                @if($m->tier)
                                    <span class="block text-[10px] text-brandOrange font-bold mt-0.5">
                                        {{ $m->tier->name }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs bg-panelBg/60 p-2.5 rounded-xl border border-[#ECEBF4]">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-textMuted block">Telepon / Kontak</span>
                                @if($m->phone)
                                    <a href="tel:{{ $m->phone }}" class="font-semibold text-sidebar hover:underline">{{ $m->phone }}</a>
                                @else
                                    <span class="text-textMuted">—</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-textMuted block">Plafon Kredit</span>
                                @if($m->type === 'B2B')
                                    <span class="font-bold font-mono text-sidebar">Rp {{ number_format($m->credit_limit, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-textMuted">—</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <a href="{{ route('master.members.card', $m) }}" 
                               class="px-3 py-1.5 bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white rounded-xl text-xs font-bold transition inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                                <span>Lihat Kartu QR</span>
                            </a>

                            @if($m->is_active)
                                <form method="POST" action="{{ route('master.members.destroy', $m) }}" 
                                      data-confirm="Apakah Anda yakin ingin menonaktifkan member [{{ $m->name }}]?" data-confirm-title="Nonaktifkan Member" data-confirm-btn="Ya, Nonaktifkan" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        Nonaktifkan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 px-4 text-center text-textMuted text-xs">Belum ada member terdaftar.</div>
                @endforelse
            </div>

            @if($members->hasPages())
                <div class="p-4 border-t border-[#ECEBF4] bg-panelBg/30">
                    {{ $members->links() }}
                </div>
            @endif
        </div>
</div>

@endsection


