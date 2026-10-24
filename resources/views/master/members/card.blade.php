@extends('layouts.app')

@section('title', 'Kartu Digital Member — ' . $member->name)
@section('page_title', 'Kartu Digital Member & QR Barcode')
@section('page_subtitle', 'Identitas digital pelanggan untuk verifikasi instan di kasir POS dan pengambilan pesanan B2B.')

@section('content')
<div class="space-y-6 max-w-xl mx-auto">
    <!-- Sub-Nav Tabs -->
    @include('master.partials.nav')

    <!-- Action Buttons -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('master.members.index') }}" class="text-xs font-semibold text-textMuted hover:text-sidebar px-3 py-2 rounded-xl hover:bg-panelBg transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar Member</span>
        </a>

        <button onclick="window.print()" class="px-4 py-2 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span>Cetak Kartu Fisik</span>
        </button>
    </div>

    <!-- Digital Card Design (Poppins, Zero AI Slop, Flat Solid Tokens) -->
    <div class="printable-card bg-sidebar text-white p-7 sm:p-9 rounded-[32px] shadow-lg relative overflow-hidden border border-white/10 select-text">
        <!-- Top Ornaments -->
        <div class="flex items-start justify-between relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-white/15 flex items-center justify-center">
                    <svg class="w-6 h-6 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-display font-extrabold text-sm tracking-tight text-white">Pasar<strong class="text-brandOrange">Hub</strong></h3>
                    <span class="text-[10px] text-white/70 block uppercase tracking-wider font-semibold">Digital Membership Card</span>
                </div>
            </div>

            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/15 text-white border border-white/20">
                {{ $member->type === 'B2B' ? 'MITRA B2B' : 'RITEL UMUM' }}
            </span>
        </div>

        <!-- Center: QR Code Vector Box -->
        <div class="my-8 text-center relative z-10 flex flex-col items-center">
            <div class="p-3 bg-white rounded-2xl shadow-sm inline-block">
                {!! $qrSvg !!}
            </div>
            <span class="font-mono text-xs font-bold text-white/90 tracking-widest mt-3.5 block bg-black/20 px-4 py-1.5 rounded-full border border-white/10">
                {{ $member->member_code }}
            </span>
        </div>

        <!-- Bottom: Member Information -->
        <div class="border-t border-white/15 pt-5 flex items-end justify-between relative z-10">
            <div>
                <span class="text-[10px] text-white/60 uppercase font-bold tracking-wider block">Pemegang Kartu</span>
                <h4 class="font-bold text-base text-white tracking-tight mt-0.5">{{ $member->name }}</h4>
                <p class="text-xs text-white/70 mt-0.5">{{ $member->phone ?: 'No. HP belum terdaftar' }}</p>
            </div>

            <div class="text-right">
                <span class="text-[10px] text-white/60 uppercase font-bold tracking-wider block">Tingkat & Plafon</span>
                <span class="text-xs font-extrabold text-brandOrange block mt-0.5">{{ $member->tier?->name ?? 'Reguler' }}</span>
                @if($member->type === 'B2B')
                    <span class="text-[11px] font-mono text-white/80 block mt-0.5">Plafon: Rp {{ number_format($member->credit_limit, 0, ',', '.') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .no-print {
        display: none !important;
    }
    .printable-card, .printable-card * {
        visibility: visible;
    }
    .printable-card {
        position: absolute;
        left: 50%;
        top: 20%;
        transform: translateX(-50%);
        box-shadow: none !important;
        border: 1px solid #342A6E !important;
    }
}
</style>
@endpush
@endsection
