@extends('layouts.app')

@section('title', 'PasarHub — Sistem Manajemen Operasional Pasar Terpadu (Gudang, Toko, & B2B)')


@section('content')
<!-- LEFT / CENTER DYNAMIC CONTENT AREA -->
<main class="flex-1 w-full space-y-6">

  <!-- CONTEXTUAL ROLE-BASED DASHBOARD (7 PROFILES) -->
  @if($dashboardProfile === 'store_head')
    @include('dashboard.profiles.store_head')
  @elseif($dashboardProfile === 'store_staff')
    @include('dashboard.profiles.store_staff')
  @elseif($dashboardProfile === 'warehouse_head')
    @include('dashboard.profiles.warehouse_head')
  @elseif($dashboardProfile === 'warehouse_staff')
    @include('dashboard.profiles.warehouse_staff')
  @elseif($dashboardProfile === 'sales')
    @include('dashboard.profiles.sales')
  @elseif($dashboardProfile === 'b2b_partner')
    @include('dashboard.profiles.b2b_partner')
  @else
    @include('dashboard.profiles.executive')
  @endif

</main>
@endsection

@section('right_panel')
  @if(in_array($dashboardProfile, ['store_head', 'store_staff']))
    @include('store.partials.right-panel')
  @elseif(in_array($dashboardProfile, ['warehouse_head', 'warehouse_staff']))
    @include('warehouse.partials.right-panel')
  @elseif(in_array($dashboardProfile, ['sales', 'b2b_partner']))
    @include('b2b.partials.right-panel')
  @else
    @include('dashboard.partials.right-panel')
  @endif
@endsection
