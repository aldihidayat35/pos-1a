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

  <!-- DASBOR UTAMA & KESEHATAN FINANSIAL -->
  @include('dashboard.pages.page-0')

</main>
@endsection

@section('right_panel')
  @include('dashboard.partials.right-panel')
@endsection
