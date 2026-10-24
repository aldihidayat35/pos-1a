<!-- Master Data Header Context (Sub-menus now live in Fixed Header) -->
<div class="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-[#ECEBF4] pb-4">
    <div>
        <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">@yield('page_title', 'Master Data Pasar')</h1>
        <p class="text-xs text-textMuted mt-0.5">@yield('page_subtitle', 'Pusat pengelolaan katalog produk, konversi satuan, suplayer, member, dan lokasi operasional.')</p>
    </div>
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
