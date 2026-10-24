@extends('layouts.app')

@section('title', '403 — Akses Ditolak | PasarHub')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-10 px-4">
  <div class="max-w-md w-full bg-white rounded-3xl border border-[#ECEBF4] p-8 sm:p-10 text-center shadow-lg space-y-6">
    <!-- Shield Alert Icon -->
    <div class="relative w-20 h-20 mx-auto">
      <div class="w-20 h-20 rounded-3xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 shadow-sm">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
      </div>
      <span class="absolute -bottom-1 -right-1 px-2 py-0.5 rounded-full bg-sidebar text-white font-mono font-black text-[10px] shadow">
        403
      </span>
    </div>

    <!-- Title & Description -->
    <div class="space-y-2">
      <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
        Akses Ditolak / Dibatasi
      </h1>
      <p class="text-xs text-textMuted leading-relaxed">
        {{ $exception->getMessage() ?: 'Akun Anda tidak memiliki hak akses atau izin yang memadai untuk membuka halaman ini.' }}
      </p>
    </div>

    <!-- Active User Role Info -->
    @auth
      <div class="p-3.5 rounded-2xl bg-panelBg border border-[#ECEBF4] flex items-center justify-between text-xs">
        <div class="text-left">
          <span class="text-[10px] text-textMuted uppercase font-bold block">Pengguna Login:</span>
          <span class="font-bold text-textMain">{{ auth()->user()->name }}</span>
        </div>
        <span class="px-2.5 py-1 rounded-xl bg-sidebar text-white font-bold text-[10px] uppercase">
          {{ auth()->user()->primaryRole()?->label ?? 'User' }}
        </span>
      </div>
    @endauth

    <!-- Action Back Button -->
    <div class="pt-2">
      <a href="{{ route('dashboard') }}" class="w-full py-3.5 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-2xl shadow-md transition flex items-center justify-center gap-2">
        <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Kembali ke Dasbor Utama</span>
      </a>
    </div>
  </div>
</div>
@endsection
