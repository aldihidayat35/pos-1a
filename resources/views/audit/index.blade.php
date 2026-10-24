@extends('layouts.app')

@section('title', 'Feed Pergerakan 5-Warna & Anomali — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- 1. HEADER BANNER -->
  <div class="bg-sidebar text-white p-6 sm:p-7 rounded-[26px] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
    <div>
      <div class="inline-flex items-center gap-2 bg-white/15 px-3 py-1 rounded-full text-xs font-semibold mb-2">
        <span class="w-2 h-2 rounded-full bg-brandOrange animate-pulse"></span>
        <span>Zero-Bottleneck Accountability Board</span>
        @if($user->isOwner())
          <span class="ml-2 bg-brandCoral text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
            Mode Owner Read-Only
          </span>
        @endif
      </div>
      <h1 class="font-display text-2xl font-bold tracking-tight">
        Audit Trail & Notifikasi Anomali Terpadu
      </h1>
      <p class="text-white/85 text-xs sm:text-sm mt-1 max-w-2xl font-medium">
        Audit transparan tanpa botol persetujuan: Setiap aksi krusial mengikat operator, lokasi, timestamp, dan dokumen bukti secara permanen.
      </p>
    </div>

    <!-- Severity Filter Pills -->
    <div class="flex items-center gap-2 shrink-0">
      <a href="{{ route('audit.index') }}"
         class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ !request('severity') ? 'bg-white text-sidebar shadow-sm' : 'bg-white/15 text-white hover:bg-white/25' }}">
        Semua ({{ $counts['ALL'] }})
      </a>
      <a href="{{ route('audit.index', ['severity' => 'ORANGE']) }}"
         class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request('severity') === 'ORANGE' ? 'bg-brandOrange text-white shadow-sm' : 'bg-white/15 text-white hover:bg-white/25' }}">
        <span class="w-2 h-2 rounded-full bg-brandOrange"></span>
        <span>Oranye ({{ $counts['ORANGE'] }})</span>
      </a>
      <a href="{{ route('audit.index', ['severity' => 'RED']) }}"
         class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ request('severity') === 'RED' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white/15 text-white hover:bg-white/25' }}">
        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
        <span>Merah ({{ $counts['RED'] }})</span>
      </a>
    </div>
  </div>

  <!-- 2. 5-WARNA STANDARD LEGEND BAR -->
  <div class="bg-white p-4 sm:p-5 rounded-[22px] border border-[#ECEBF4] flex flex-wrap items-center justify-between gap-3 text-xs">
    <div class="flex items-center gap-2">
      <span class="font-bold text-textMain">Standar 5 Warna Mutasi:</span>
    </div>
    <div class="flex flex-wrap items-center gap-2 sm:gap-3 font-semibold text-[11px]">
      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-moveGreenBg text-moveGreen border border-emerald-200">
        <span class="w-2 h-2 rounded-full bg-moveGreen"></span>
        <span>Hijau: Masuk Suplayer</span>
      </div>
      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-moveBlueBg text-moveBlue border border-blue-200">
        <span class="w-2 h-2 rounded-full bg-moveBlue"></span>
        <span>Biru: Mutasi Internal</span>
      </div>
      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-movePurpleBg text-movePurple border border-purple-200">
        <span class="w-2 h-2 rounded-full bg-movePurple"></span>
        <span>Ungu: Penjualan POS / B2B</span>
      </div>
      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-moveOrangeBg text-moveOrange border border-amber-200 font-bold">
        <span class="w-2 h-2 rounded-full bg-moveOrange"></span>
        <span>Oranye: Belanja Emergency</span>
      </div>
      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-moveRedBg text-moveRed border border-red-200 font-bold">
        <span class="w-2 h-2 rounded-full bg-moveRed"></span>
        <span>Merah: Selisih Opname / Susut</span>
      </div>
    </div>
  </div>

  <!-- 3. ACCOUNTABILITY LIVE FEED -->
  <div class="bg-white rounded-3xl p-6 border border-[#ECEBF4] shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <div>
        <h3 class="font-bold text-sm text-textMain flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-brandOrange"></span>
          <span>Feed Anomali Berjalan (Live Stream)</span>
        </h3>
        <p class="text-xs text-textMuted mt-0.5">
          Catatan anomali darurat (Oranye) dan selisih opname (Merah) yang diterbitkan secara real-time.
        </p>
      </div>
      <span class="text-xs font-semibold text-textMuted">Total {{ $anomalies->total() }} Catatan</span>
    </div>

    @if($anomalies->isEmpty())
      <div class="py-12 text-center text-xs text-textMuted bg-panelBg/40 rounded-2xl border border-dashed border-[#ECEBF4]">
        Tidak ada event anomali yang ditemukan untuk kriteria filter ini.
      </div>
    @else
      <div class="space-y-3">
        @foreach($anomalies as $anm)
          @php
            $isOrange = $anm->severity === 'ORANGE';
            $badgeBg = $isOrange ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-rose-50 text-rose-800 border-rose-200';
            $iconBg = $isOrange ? 'bg-amber-100 text-brandOrange' : 'bg-rose-100 text-rose-600';
            $payload = $anm->event_payload ?? [];
          @endphp
          <div class="p-4 rounded-2xl bg-panelBg/70 border border-[#ECEBF4] hover:bg-white transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
              <div class="w-10 h-10 rounded-xl {{ $iconBg }} flex items-center justify-center shrink-0 mt-0.5 font-bold">
                @if($isOrange)
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                @else
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                @endif
              </div>

              <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                  <span class="font-mono text-xs font-bold text-sidebar">{{ $anm->reference_number ?? ('#ANM-' . $anm->id) }}</span>
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border {{ $badgeBg }}">
                    {{ $isOrange ? 'ORANYE • BELANJA EMERGENCY' : 'MERAH • SELISIH OPNAME' }}
                  </span>
                  <span class="text-[10px] text-textMuted">• {{ $anm->location?->name }} ({{ $anm->location?->code }})</span>
                </div>

                <h4 class="font-bold text-xs sm:text-sm text-textMain">
                  {{ $anm->title }}
                </h4>

                <p class="text-xs text-textMuted leading-relaxed max-w-3xl">
                  {{ $anm->description }}
                </p>

                <div class="flex flex-wrap items-center gap-3 text-[11px] text-textMuted pt-1">
                  <span>Waktu: <strong class="text-textMain">{{ $anm->occurred_at?->format('d/m/Y H:i:s') }}</strong></span>
                  <span>Petugas: <strong class="text-textMain">{{ $anm->actor?->name }}</strong></span>
                  @if($anm->acknowledged_at)
                    <span class="text-emerald-700 font-bold">✓ Sudah Direview ({{ $anm->acknowledged_at->format('d/m/Y') }})</span>
                  @else
                    <span class="text-brandOrange font-bold">⏳ Menunggu Review Owner</span>
                  @endif
                </div>
              </div>
            </div>

            <!-- Action / Document Link -->
            <div class="shrink-0 flex items-center gap-2 self-start sm:self-auto">
              @if($anm->reference_id && ($anm->kind === 'EMERGENCY_PURCHASE' || $anm->reference_type === \App\Models\EmergencyPurchase::class))
                <a href="{{ route('store.emergency.show', $anm->reference_id) }}"
                   class="px-3.5 py-2 rounded-xl bg-white text-sidebar hover:bg-sidebar hover:text-white font-bold text-xs border border-[#ECEBF4] transition flex items-center gap-1.5 shadow-sm">
                  <svg class="w-3.5 h-3.5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <span>Lihat Bukti Foto Nota</span>
                </a>
              @endif
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4 pt-4 border-t border-[#ECEBF4]">
        {{ $anomalies->links() }}
      </div>
    @endif
  </div>
</main>
@endsection

@section('right_panel')
  @include('audit.partials.right-panel')
@endsection
