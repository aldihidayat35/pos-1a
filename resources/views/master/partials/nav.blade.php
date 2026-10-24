<!-- Master Data Sub-Module Navigation Bar -->
<div class="mb-6 flex flex-wrap items-center justify-between gap-4 border-b border-[#ECEBF4] pb-4">
    <div>
        <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">@yield('page_title', 'Master Data Pasar')</h1>
        <p class="text-xs text-textMuted mt-0.5">@yield('page_subtitle', 'Pusat pengelolaan katalog produk, konversi satuan, suplayer, member, dan lokasi operasional.')</p>
    </div>

    <!-- Sub-Module Tabs Pill Bar -->
    <div class="flex items-center gap-1.5 p-1.5 bg-panelBg rounded-2xl border border-[#ECEBF4] overflow-x-auto max-w-full">
        <!-- Tab 1: Produk & Satuan -->
        <a href="{{ route('master.products.index') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('master.products.*') ? 'bg-sidebar text-white shadow-sm' : 'text-textMain hover:bg-white hover:text-sidebar' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span>Katalog Produk</span>
        </a>

        <!-- Tab 2: Kategori -->
        <a href="{{ route('master.categories.index') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('master.categories.*') ? 'bg-sidebar text-white shadow-sm' : 'text-textMain hover:bg-white hover:text-sidebar' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
            </svg>
            <span>Kategori</span>
        </a>

        <!-- Tab 3: Satuan UOM -->
        <a href="{{ route('master.uoms.index') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('master.uoms.*') ? 'bg-sidebar text-white shadow-sm' : 'text-textMain hover:bg-white hover:text-sidebar' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 18h12l3-18H3z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
            </svg>
            <span>Satuan UOM</span>
        </a>

        <!-- Tab 4: Suplayer -->
        <a href="{{ route('master.suppliers.index') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('master.suppliers.*') ? 'bg-sidebar text-white shadow-sm' : 'text-textMain hover:bg-white hover:text-sidebar' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Suplayer</span>
        </a>

        <!-- Tab 5: Member & Pelanggan -->
        <a href="{{ route('master.members.index') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('master.members.*') ? 'bg-sidebar text-white shadow-sm' : 'text-textMain hover:bg-white hover:text-sidebar' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Member & B2B</span>
        </a>

        <!-- Tab 6: Lokasi (Gudang/Toko) -->
        <a href="{{ route('master.locations.index') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('master.locations.*') ? 'bg-sidebar text-white shadow-sm' : 'text-textMain hover:bg-white hover:text-sidebar' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Gudang & Toko</span>
        </a>
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
