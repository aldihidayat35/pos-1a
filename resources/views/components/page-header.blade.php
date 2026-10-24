@props([
    'title',
    'subtitle' => null,
    'badge' => null,
    'badgeColor' => 'bg-sidebar text-white',
    'id' => null,
])

@php
    $popoverId = $id ?: 'header_popover_' . substr(md5($title . $subtitle), 0, 8);
@endphp

<div class="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-[#ECEBF4] pb-4 relative">
    <div class="min-w-0 flex-1">
        <!-- Title & Mobile Info Row -->
        <div class="flex items-center gap-2 flex-wrap">
            <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
                {{ $title }}
            </h1>

            @if($badge)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase shrink-0 {{ $badgeColor }}">
                    {{ $badge }}
                </span>
            @endif

            @if($subtitle)
                <!-- Mobile-Only Info Tooltip Trigger (ℹ️) -->
                <div class="relative md:hidden inline-flex items-center">
                    <button type="button" 
                            onclick="toggleHeaderTooltip('{{ $popoverId }}', event)" 
                            class="w-6 h-6 rounded-full bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white flex items-center justify-center transition p-1 shadow-xs"
                            title="Lihat Keterangan Halaman"
                            aria-label="Info Halaman">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                        </svg>
                    </button>

                    <!-- Mobile Popover Floating Bubble -->
                    <div id="{{ $popoverId }}" class="header-popover hidden fixed inset-x-4 top-20 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs">
                        <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
                            <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                                <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                                </svg>
                                <span>Informasi Halaman</span>
                            </div>
                            <button type="button" onclick="closeHeaderTooltip('{{ $popoverId }}')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
                        </div>
                        <p class="text-[11px] text-textMuted leading-relaxed">
                            {{ $subtitle }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Desktop Subtitle (Hidden on Mobile) -->
        @if($subtitle)
            <p class="hidden md:block text-xs text-textMuted mt-1 max-w-3xl leading-normal">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    <!-- Actions Slot (Shortened Icon Buttons) -->
    @if(isset($actions) && $actions->isNotEmpty())
        <div class="flex items-center gap-2 sm:gap-3 shrink-0 flex-wrap">
            {{ $actions }}
        </div>
    @endif
</div>
