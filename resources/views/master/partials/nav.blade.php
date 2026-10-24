<!-- Master Data Header Context (Clean UI with Mobile Tooltip) -->
<div class="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-[#ECEBF4] pb-4 relative">
    <div class="min-w-0 flex-1">
        <div class="flex items-center gap-2 flex-wrap">
            <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
                @yield('page_title', 'Master Data Pasar')
            </h1>

            @hasSection('page_subtitle')
                <!-- Mobile-Only Info Popover Trigger (ℹ️) -->
                <div class="relative md:hidden inline-flex items-center">
                    <button type="button" 
                            onclick="toggleHeaderTooltip('masterHeaderPopover', event)" 
                            class="w-6 h-6 rounded-full bg-sidebar/10 hover:bg-sidebar text-sidebar hover:text-white flex items-center justify-center transition p-1 shadow-xs"
                            title="Lihat Keterangan Halaman"
                            aria-label="Info Halaman">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                        </svg>
                    </button>

                    <!-- Mobile Popover Floating Bubble -->
                    <div id="masterHeaderPopover" class="header-popover hidden fixed inset-x-4 top-20 z-50 bg-white rounded-2xl p-4 border border-[#ECEBF4] shadow-2xl animate-in fade-in zoom-in-95 duration-150 text-xs">
                        <div class="flex items-start justify-between gap-2 pb-2 mb-2 border-b border-[#ECEBF4]">
                            <div class="flex items-center gap-1.5 font-bold text-sidebar text-xs">
                                <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                                </svg>
                                <span>Informasi Halaman</span>
                            </div>
                            <button type="button" onclick="closeHeaderTooltip('masterHeaderPopover')" class="p-1 text-textMuted hover:text-textMain rounded-lg font-bold">✕</button>
                        </div>
                        <p class="text-[11px] text-textMuted leading-relaxed">
                            @yield('page_subtitle')
                        </p>
                    </div>
                </div>
            @endif
        </div>

        @hasSection('page_subtitle')
            <!-- Desktop Subtitle (Hidden on Mobile) -->
            <p class="hidden md:block text-xs text-textMuted mt-1 max-w-3xl leading-normal">
                @yield('page_subtitle')
            </p>
        @endif
    </div>

    @hasSection('page_actions')
        <div class="flex items-center gap-2 sm:gap-3 shrink-0 flex-wrap">
            @yield('page_actions')
        </div>
    @endif
</div>

<!-- Global Flash Alert Messages -->
@if(session('success'))
    <div class="mb-5 flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-medium animate-fadeIn">
        <div class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-5 flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs font-medium animate-fadeIn">
        <div class="w-6 h-6 rounded-lg bg-red-500 text-white flex items-center justify-center shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="mb-5 p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl text-xs font-medium animate-fadeIn">
        <div class="flex items-center gap-2 font-bold mb-1">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Terdapat beberapa kesalahan input:</span>
        </div>
        <ul class="list-disc pl-5 space-y-0.5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif
