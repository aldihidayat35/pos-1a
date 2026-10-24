@extends('layouts.app')

@section('title', 'PasarHub — Sistem Manajemen Operasional Pasar Terpadu (Gudang, Toko, & B2B)')

@section('content')
<!-- LEFT / CENTER DYNAMIC CONTENT AREA -->
<main class="flex-1 w-full space-y-6">

  <!-- Dynamic Sub-Menu Active Filter Banner (Shows when user switches sub-menus) -->
  <div id="subMenuContextBox" class="hidden bg-panelBg border border-[#E8E6F2] rounded-[18px] px-4 py-3 items-center justify-between">
    <div class="flex items-center gap-2.5">
      <span class="w-2 h-2 rounded-full bg-sidebar"></span>
      <p id="subMenuContextText" class="text-xs font-semibold text-textMain"></p>
    </div>
    <button onclick="selectSubMenu(0)" class="text-[11px] font-bold text-brandCoral hover:underline" data-i18n="btn_reset_view">Reset Tampilan</button>
  </div>

  <!-- PAGE 0 (INDEX 0): DASBOR UTAMA & KESEHATAN FINANSIAL (DEFAULT FASE 1) -->
  @include('dashboard.pages.page-0')

  <!-- PAGE 1 (INDEX 1): OPERASIONAL GUDANG PUSAT -->
  @include('dashboard.pages.page-1')

  <!-- PAGE 2 (INDEX 2): OPERASIONAL TOKO & KASIR POS -->
  @include('dashboard.pages.page-2')

  <!-- PAGE 3 (INDEX 3): KEMITRAAN B2B & SALES -->
  @include('dashboard.pages.page-3')

  <!-- PAGE 4 (INDEX 4): FEED PERGERAKAN 5-WARNA & ANOMALI -->
  @include('dashboard.pages.page-4')

  <!-- PAGE 5 (INDEX 5): MASTER PRODUK, UOM, & MEMBER QR -->
  @include('dashboard.pages.page-5')

</main>
@endsection

@section('right_panel')
  @include('partials.right-panel')
@endsection
