@extends('layouts.app')

@section('title', 'Tambah Member & Kemitraan — PasarHub')
@section('page_title', 'Tambah Member / Mitra')
@section('page_subtitle', 'Daftarkan pelanggan ritel loyalitas atau mitra grosir/B2B dengan fasilitas plafon kredit.')

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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-textMain">Formulir Member / Mitra Baru</h2>
                    <p class="text-xs text-textMuted mt-0.5">Daftarkan pelanggan ritel loyalitas atau mitra grosir/B2B dengan plafon piutang.</p>
                </div>
            </div>
            <a href="{{ route('master.members.index') }}" class="text-xs font-semibold text-textMuted hover:text-sidebar px-3 py-2 rounded-xl hover:bg-panelBg transition">
                ← Kembali ke Daftar Member
            </a>
        </div>

        <form method="POST" action="{{ route('master.members.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Tipe Kemitraan <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="memberTypeSelect" required
                            class="w-full px-4 py-2.5 bg-panelBg border @error('type') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition cursor-pointer">
                        <option value="RETAIL" {{ old('type') === 'RETAIL' ? 'selected' : '' }}>Pelanggan Ritel (Umum / Loyalitas)</option>
                        <option value="B2B" {{ old('type') === 'B2B' ? 'selected' : '' }}>Mitra Grosir / Toko B2B (Fasilitas TOP & Plafon)</option>
                    </select>
                    @error('type')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Kode Member <span class="text-[11px] font-normal text-textMuted">(Otomatis jika kosong)</span>
                    </label>
                    <input type="text" name="member_code" value="{{ old('member_code') }}" placeholder="Mis. MBR-B2B-0005"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('member_code') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-mono font-bold text-sidebar uppercase focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('member_code')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Nama Lengkap / Nama Toko Mitra <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Mis. Toko Sembako Berkah / Bpk. Rudi Hartono"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('name') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('name')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Tier Diskon Khusus
                    </label>
                    <select name="tier_id" class="w-full px-4 py-2.5 bg-panelBg border @error('tier_id') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition cursor-pointer">
                        <option value="">Pilih Tier (Default)</option>
                        @foreach($tiers as $tier)
                            <option value="{{ $tier->id }}" {{ old('tier_id') == $tier->id ? 'selected' : '' }}>
                                {{ $tier->name }} ({{ (float)$tier->discount_percent }}%)
                            </option>
                        @endforeach
                    </select>
                    @error('tier_id')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div id="creditLimitWrapper">
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Plafon Kredit (IDR)
                    </label>
                    <input type="number" step="1000" min="0" name="credit_limit" value="{{ old('credit_limit', 0) }}" placeholder="0"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('credit_limit') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-semibold text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('credit_limit')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                    <span class="block text-[11px] text-textMuted mt-1">Batas maksimum piutang jika belanja menggunakan sistem TOP / Invoice Tempo.</span>
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

                <div>
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="member@email.com"
                           class="w-full px-4 py-2.5 bg-panelBg border @error('email') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">
                    @error('email')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-textMain uppercase tracking-wider mb-2">
                        Alamat Lengkap Domisili / Toko
                    </label>
                    <textarea name="address" rows="3" placeholder="Alamat toko atau domisili pelanggan..."
                              class="w-full px-4 py-2.5 bg-panelBg border @error('address') border-red-500 @else border-[#E6E4F0] @enderror rounded-xl text-xs font-medium text-textMain focus:bg-white focus:outline-none focus:border-sidebar transition">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-[#ECEBF4] flex items-center justify-end gap-3">
                <a href="{{ route('master.members.index') }}" class="px-5 py-2.5 bg-panelBg hover:bg-gray-200 text-textMuted text-xs font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Daftarkan Member / Mitra</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const typeSelect = document.getElementById('memberTypeSelect');
    const creditWrapper = document.getElementById('creditLimitWrapper');
    if (typeSelect && creditWrapper) {
        function syncCreditState() {
            if (typeSelect.value === 'RETAIL') {
                creditWrapper.classList.add('opacity-50');
            } else {
                creditWrapper.classList.remove('opacity-50');
            }
        }
        typeSelect.addEventListener('change', syncCreditState);
        syncCreditState();
    }
</script>
@endpush
