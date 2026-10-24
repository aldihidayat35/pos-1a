@extends('layouts.app')

@section('title', 'Kemitraan B2B & Sales — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-7">
  <!-- RBAC Access Warning -->
  @if(!$user->hasPermission('b2b.order') && !$user->isOwner() && !$user->isSuperAdmin())
    <div class="p-4 rounded-2xl bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E] text-xs font-semibold flex items-center gap-3">
      <svg class="w-5 h-5 text-[#F59E0B] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <div>
        <p class="font-bold">Akses Dibatasi — Khusus Sales & Otoritas B2B</p>
        <p class="text-[11px] text-[#92400E]/90 mt-0.5">Akun Anda ({{ $user->primaryRole()?->label ?? 'User' }}) hanya memiliki akses pantau dasar. Transaksi grosir dan penerbitan piutang dijaga ketat oleh sistem.</p>
      </div>
    </div>
  @endif

  <!-- 1. SOLID HERO BANNER (Flat solid purple #342A6E, no gradient, no glow shadow) -->
  <div class="relative bg-sidebar rounded-[26px] p-6 sm:p-8 text-white overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-sm">
    <div class="relative z-10 max-w-xl">
      <div class="inline-flex items-center gap-2 bg-white/15 px-3 py-1 rounded-full text-xs font-semibold mb-3">
        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
        <span>Kemitraan Korporasi & Grosir Ber-Plafon</span>
      </div>
      <h1 class="font-display text-2xl sm:text-[30px] font-bold tracking-tight leading-tight">
        Kemitraan B2B & Sales
      </h1>
      <p class="text-white/85 text-xs sm:text-sm mt-1.5 font-medium leading-relaxed">
        Pusat kendali pelanggan grosir, harga khusus per tier rekanan, penguncian otomatis plafon piutang, dan pencetakan kartu digital ber-QR token.
      </p>

      <div class="mt-4 flex flex-wrap items-center gap-2.5">
        <span class="bg-white/15 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span>{{ $stats['total_b2b_members'] }} Mitra Grosir Terverifikasi</span>
        </span>
        <span class="bg-white/15 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          <span>Plafon: Rp {{ number_format($stats['total_credit_limit'] ?? 0, 0, ',', '.') }}</span>
        </span>
      </div>
    </div>

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0 w-full sm:w-auto">
      <a href="{{ route('master.members.index') }}" class="px-5 py-3 bg-white hover:bg-[#F2F1FA] text-sidebar text-xs font-bold rounded-2xl transition flex items-center justify-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Kelola Rekanan di Master</span>
      </a>
      <button onclick="openToast('Modul Sales Order B2B lengkap aktif pada alur transaksi Fase 5!')" class="px-5 py-3 bg-brandOrange hover:bg-orange-600 text-white text-xs font-bold rounded-2xl transition flex items-center justify-center gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <span>Buat Order Grosir B2B</span>
      </button>
    </div>
  </div>

  <!-- 2. 4 KEY METRIC CARDS -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Mitra B2B Aktif</span>
      <h3 class="font-display font-extrabold text-2xl text-textMain mt-2">{{ $stats['total_b2b_members'] }}</h3>
      <p class="text-xs text-textMuted mt-1">Akun grosir resmi berbadan usaha</p>
    </div>

    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <span class="text-[11px] font-bold text-textMuted uppercase tracking-wider">Total Batas Plafon</span>
      <h3 class="font-display font-extrabold text-2xl text-sidebar mt-2">Rp {{ number_format($stats['total_credit_limit'] ?? 0, 0, ',', '.') }}</h3>
      <p class="text-xs text-textMuted mt-1">Fasilitas kredit berjangka waktu</p>
    </div>

    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <span class="text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full inline-block">Piutang Overdue</span>
      <h3 class="font-display font-extrabold text-2xl text-emerald-600 mt-2">0 Tagihan</h3>
      <p class="text-xs text-textMuted mt-1">Kolektibilitas lancar 100%</p>
    </div>

    <div class="p-5 rounded-[22px] bg-panelBg border border-[#ECEBF4] hover:bg-white transition duration-200">
      <span class="text-[11px] font-bold text-sidebar bg-purple-100 px-2 py-0.5 rounded-full inline-block">Proteksi Hard Lock</span>
      <h3 class="font-display font-extrabold text-2xl text-sidebar mt-2">Aktif Otomatis</h3>
      <p class="text-xs text-textMuted mt-1">Blokir kasir jika plafon terlampaui</p>
    </div>
  </div>

  <!-- 3. B2B PARTNER DIRECTORY TABLE -->
  <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h3 class="font-display font-bold text-lg text-textMain">Daftar Mitra Grosir B2B Resmi</h3>
        <p class="text-xs text-textMuted mt-0.5">Entitas usaha pemegang plafon piutang dan hak harga grosir bertingkat</p>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('master.members.index') }}" class="text-xs font-bold text-sidebar hover:text-sidebarDark underline">
          Buka Katalog Lengkap Member →
        </a>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted">
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Kode & Nama Usaha</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Tier Rekanan</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Plafon Kredit</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Kontak PIC</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px]">Status</th>
            <th class="py-3 px-3 font-bold uppercase text-[10px] text-right">Kartu Digital</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @forelse($b2bMembers as $m)
            <tr class="hover:bg-panelBg/60 transition">
              <td class="py-3.5 px-3 font-semibold text-textMain">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-xl bg-purple-100 text-sidebar font-bold flex items-center justify-center shrink-0 text-[11px]">
                    B2B
                  </div>
                  <div>
                    <span class="block font-bold">{{ $m->name }}</span>
                    <span class="text-[11px] font-mono text-textMuted">{{ $m->member_code }}</span>
                  </div>
                </div>
              </td>
              <td class="py-3.5 px-3">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-[#F3F0FA] text-sidebar border border-[#ECEBF4]">
                  {{ $m->tier?->name ?? 'NON-TIER' }}
                </span>
              </td>
              <td class="py-3.5 px-3 font-mono font-bold text-textMain">
                Rp {{ number_format($m->credit_limit, 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-3 text-textMuted">
                <span>{{ $m->phone ?? '-' }}</span>
                @if($m->email)
                  <span class="block text-[10px] text-textMuted">{{ $m->email }}</span>
                @endif
              </td>
              <td class="py-3.5 px-3">
                @if($m->is_active)
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                @else
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Nonaktif</span>
                @endif
              </td>
              <td class="py-3.5 px-3 text-right">
                <a href="{{ route('master.members.card', $m) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-panelBg hover:bg-sidebar hover:text-white text-sidebar font-bold text-[11px] border border-[#E6E4F0] transition">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                  </svg>
                  <span>Kartu QR</span>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-8 text-center text-textMuted text-xs">
                Belum ada mitra grosir B2B terdaftar.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</main>
@endsection
