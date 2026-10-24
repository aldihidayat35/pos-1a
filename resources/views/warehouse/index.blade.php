@extends('layouts.app')

@section('title', 'Operasional Gudang Pusat — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-7">
  <!-- RBAC Access Warning if role is not warehouse / owner / superadmin -->
  @if(!$user->hasPermission('purchasing.receive') && !$user->hasPermission('inventory.manage') && !$user->isOwner() && !$user->isSuperAdmin())
    <div class="p-4 rounded-2xl bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E] text-xs font-semibold flex items-center gap-3">
      <svg class="w-5 h-5 text-[#F59E0B] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <div>
        <p class="font-bold">Akses Dibatasi — Khusus Pengelola Gudang Sentral</p>
        <p class="text-[11px] text-[#92400E]/90 mt-0.5">Akun Anda ({{ $user->primaryRole()?->label ?? 'User' }}) hanya memiliki akses pantau dasar. Mutasi dan penerimaan fisik barang gudang dikunci oleh server.</p>
      </div>
    </div>
  @endif

  <!-- 1. SOFT SOLID ORANGE HERO BANNER (No gradient, no glow shadow) -->
  <div class="relative bg-brandOrange rounded-[24px] p-7 sm:p-8 text-white overflow-hidden flex items-center justify-between min-h-[155px]">
    <div class="relative z-10 max-w-md">
      <h1 class="font-display text-2xl sm:text-[32px] font-bold tracking-tight leading-tight">
        Operasional Gudang Pusat
      </h1>
      <p class="text-white/95 text-sm sm:text-[15px] mt-1.5 font-medium">
        {{ $activeLocation?->name ?? 'Gudang Pusat Sentral' }} • Rantai Pasok & Distribusi Terpadu
      </p>
      <div class="mt-4 flex flex-wrap items-center gap-2.5">
        <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
          </svg>
          <span>{{ $stats['total_sku'] ?? 14 }} SKU Aktif Terdaftar</span>
        </span>
        <span class="bg-white/20 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
          </svg>
          <span>18 Armada Siap Muat</span>
        </span>
      </div>
    </div>

    <!-- Flat Soft Isometric Tablet & Boxes Illustration -->
    <div class="hidden sm:block relative w-56 h-36 pointer-events-none select-none">
      <svg viewBox="0 0 260 180" class="w-full h-full transform rotate-[-6deg] scale-110">
        <polygon points="90,95 235,35 265,115 120,175" fill="#E2E4EC" />
        <polygon points="95,92 232,35 258,108 122,166" fill="#F8F9FC" />
        <polygon points="110,85 180,55 198,102 128,132" fill="#F4B2B7" />
        <polygon points="55,60 195,5 220,42 85,95" fill="#FFFFFF" stroke="#E5E7EB" stroke-width="2" />
        <g transform="translate(135, 25)">
          <polygon points="30,0 60,14 30,28 0,14" fill="#FAD689" />
          <polygon points="0,14 30,28 30,60 0,46" fill="#EFA852" />
          <polygon points="30,28 60,14 60,46 30,60" fill="#DE8344" />
          <line x1="15" y1="7" x2="45" y2="21" stroke="#FFF" stroke-width="3" stroke-linecap="round"/>
        </g>
      </svg>
    </div>
  </div>

  <!-- 2. QUICK AUTOMATION FUNCTION SHORTCUTS -->
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="font-display font-bold text-lg text-textMain tracking-tight">Fungsi Alur Logistik</h2>
      <a href="{{ route('master.products.index') }}" class="text-xs font-semibold text-brandCoral hover:underline">Kelola Katalog Master →</a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
      <!-- Feature 1: Penerimaan PO -->
      <div onclick="openToast('Penerimaan PO Suplayer: Fitur aktif pada Fase 3!')" class="bg-panelBg hover:bg-white border border-[#ECEBF4] hover:border-gray-300 p-4 rounded-[20px] cursor-pointer transition flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
          </svg>
        </div>
        <div>
          <h4 class="font-bold text-xs text-textMain leading-snug">Penerimaan PO</h4>
          <span class="text-[11px] text-textMuted">Inbound Suplayer</span>
        </div>
      </div>

      <!-- Feature 2: Dispatch Transfer -->
      <div onclick="openToast('Transfer Antar Lokasi: Fitur aktif pada Fase 4!')" class="bg-panelBg hover:bg-white border border-[#ECEBF4] hover:border-gray-300 p-4 rounded-[20px] cursor-pointer transition flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
          </svg>
        </div>
        <div>
          <h4 class="font-bold text-xs text-textMain leading-snug">Kirim Transfer</h4>
          <span class="text-[11px] text-textMuted">Gudang → Toko</span>
        </div>
      </div>

      <!-- Feature 3: Blind Count Opname -->
      <div onclick="openToast('Blind Count Opname: Fitur aktif pada Fase 7!')" class="bg-panelBg hover:bg-white border border-[#ECEBF4] hover:border-gray-300 p-4 rounded-[20px] cursor-pointer transition flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
          </svg>
        </div>
        <div>
          <h4 class="font-bold text-xs text-textMain leading-snug">Blind Opname</h4>
          <span class="text-[11px] text-textMuted">Hitung Fisik Stok</span>
        </div>
      </div>

      <!-- Feature 4: Serah Terima QR B2B -->
      <div onclick="openToast('Scan Tiket B2B: Fitur aktif pada Fase 6!')" class="bg-panelBg hover:bg-white border border-[#ECEBF4] hover:border-gray-300 p-4 rounded-[20px] cursor-pointer transition flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-brandOrange flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
          </svg>
        </div>
        <div>
          <h4 class="font-bold text-xs text-textMain leading-snug">Pickup B2B QR</h4>
          <span class="text-[11px] text-textMuted">Serah Terima Mitra</span>
        </div>
      </div>
    </div>
  </div>

  <!-- 3. INVENTORY ACTIVITY SNAPSHOT -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-4">
      <div>
        <h3 class="font-bold text-base text-textMain">Status Jalur Inbound / Outbound Gudang</h3>
        <p class="text-xs text-textMuted mt-0.5">Pantauan pergerakan dermaga muat barang dan antrean kendaraan logistik.</p>
      </div>
      <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-bold">
        Gate Dermaga: 4 Siap
      </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="p-4 bg-panelBg rounded-2xl border border-[#ECEBF4]">
        <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Penerimaan PO Hari Ini</span>
        <h4 class="text-xl font-extrabold text-sidebar mt-1">3 Pengiriman Masuk</h4>
        <p class="text-xs text-textMuted mt-1">Beras Rojolele (500 Krg), Minyak Bimoli (200 Jrg)</p>
      </div>

      <div class="p-4 bg-panelBg rounded-2xl border border-[#ECEBF4]">
        <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Transfer Menuju Toko</span>
        <h4 class="text-xl font-extrabold text-blue-600 mt-1">2 Mobil Transit</h4>
        <p class="text-xs text-textMuted mt-1">STR-PASAR-1 (Status: Dalam Perjalanan)</p>
      </div>

      <div class="p-4 bg-panelBg rounded-2xl border border-[#ECEBF4]">
        <span class="text-[10px] font-bold text-textMuted uppercase tracking-wider block">Pesanan B2B Siap Diambil</span>
        <h4 class="text-xl font-extrabold text-purple-600 mt-1">4 Tiket QR Aktif</h4>
        <p class="text-xs text-textMuted mt-1">Menunggu serah terima mandiri mitra warung</p>
      </div>
    </div>
  </div>
</main>
@endsection

@section('right_panel')
  @include('partials.right-panel')
@endsection
