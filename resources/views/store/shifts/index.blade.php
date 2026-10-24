@extends('layouts.app')

@section('title', 'Manajemen Shift Laci Kasir — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Flash Notification -->
  @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
      <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
      </svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
      <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
      <span>{{ session('error') }}</span>
    </div>
  @endif

  <!-- Top Breadcrumb & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('store.index') }}" class="hover:underline">Operasional Toko</a>
        <span>/</span>
        <span class="text-textMain font-bold">Shift Laci Kasir</span>
      </div>
      <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
        Pengendalian Shift & Laci Uang Kasir
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Prosedur buka/tutup shift kasir dengan rekonsiliasi hitung buta (*Blind Cash Count*) di {{ $activeLocation->name }}.
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('store.pos.terminal') }}" class="px-4 py-2 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
        <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span>Buka Kasir POS</span>
      </a>
    </div>
  </div>

  <!-- Active Shift Highlight Card -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <div class="flex items-center gap-2.5">
        <span class="w-3 h-3 rounded-full {{ $activeShift ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
        <h3 class="font-bold text-sm text-textMain">Status Laci Kasir Saat Ini</h3>
      </div>

      @if($activeShift)
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
          Shift Aktif: {{ $activeShift->shift_number }}
        </span>
      @else
        <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
          Laci Kasir Terkunci
        </span>
      @endif
    </div>

    @if($activeShift)
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
        <div class="p-3.5 bg-panelBg/70 rounded-2xl border border-[#ECEBF4] space-y-1">
          <span class="text-textMuted text-[10px] uppercase font-bold tracking-wider block">Waktu Buka Shift</span>
          <span class="font-bold text-sidebar text-xs block">{{ $activeShift->opened_at->format('d/m/Y H:i') }} WIB</span>
          <span class="text-[10px] text-textMuted">Oleh: {{ $activeShift->cashier->name }}</span>
        </div>

        <div class="p-3.5 bg-panelBg/70 rounded-2xl border border-[#ECEBF4] space-y-1">
          <span class="text-textMuted text-[10px] uppercase font-bold tracking-wider block">Modal Awal Laci</span>
          <span class="font-black text-sidebar font-mono text-sm block">
            Rp {{ number_format($activeShift->starting_cash, 0, ',', '.') }}
          </span>
          <span class="text-[10px] text-textMuted">Uang kembalian awal</span>
        </div>

        <div class="p-3.5 bg-emerald-50/70 rounded-2xl border border-emerald-200 space-y-1">
          <span class="text-emerald-800 text-[10px] uppercase font-bold tracking-wider block">Total Penjualan Tunai</span>
          <span class="font-black text-emerald-700 font-mono text-sm block">
            +Rp {{ number_format($activeShiftCashSales, 0, ',', '.') }}
          </span>
          <span class="text-[10px] text-emerald-800">Uang tunai masuk laci</span>
        </div>

        <div class="p-3.5 bg-purple-50/70 rounded-2xl border border-purple-200 space-y-1">
          <span class="text-purple-800 text-[10px] uppercase font-bold tracking-wider block">Penjualan Non-Tunai</span>
          <span class="font-black text-purple-700 font-mono text-sm block">
            Rp {{ number_format($activeShiftNonCashSales, 0, ',', '.') }}
          </span>
          <span class="text-[10px] text-purple-800">QRIS / Transfer / Debit</span>
        </div>
      </div>

      <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-panelBg/50 p-4 rounded-2xl border border-[#ECEBF4]">
        <div class="text-xs">
          <span class="text-textMuted uppercase font-bold text-[10px] block">Estimasi Kas Fisik Laci yang Seharusnya:</span>
          <span class="text-lg font-black text-sidebar font-mono">
            Rp {{ number_format($activeShift->starting_cash + $activeShiftCashSales, 0, ',', '.') }}
          </span>
        </div>

        @if($canManage)
          <button type="button" onclick="openCloseShiftModal({{ $activeShift->id }}, '{{ $activeShift->shift_number }}')"
                  class="px-5 py-2.5 bg-brandOrange hover:bg-brandOrange/90 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>Tutup Shift Kasir (Blind Cash Count)</span>
          </button>
        @endif
      </div>
    @else
      <div class="py-6 text-center space-y-3">
        <p class="text-xs text-textMuted max-w-md mx-auto">
          Kasir harus memasukkan jumlah uang modal awal laci (*cash float*) sebelum dapat melayani transaksi pelanggan di terminal kasir.
        </p>
        @if($canManage)
          <button type="button" onclick="openStartShiftModal()"
                  class="px-5 py-2.5 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl shadow-md transition inline-flex items-center gap-2">
            <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Buka Shift Laci Kasir Baru</span>
          </button>
        @endif
      </div>
    @endif
  </div>

  <!-- Historical Shifts Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="border-b border-[#ECEBF4] pb-3">
      <h3 class="font-bold text-sm text-textMain">Riwayat Sesi Shift & Rekonsiliasi Kas</h3>
      <p class="text-xs text-textMuted mt-0.5">Catatan seluruh penutupan shift dan verifikasi selisih kas fisik laci.</p>
    </div>

    @if($shifts->isEmpty())
      <div class="py-12 text-center text-xs text-textMuted">
        Belum ada riwayat shift kasir di lokasi ini.
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3 px-3">No. Shift</th>
              <th class="py-3 px-3">Kasir</th>
              <th class="py-3 px-3">Buka / Tutup</th>
              <th class="py-3 px-3 text-right">Modal Awal</th>
              <th class="py-3 px-3 text-right">Kas Diharapkan</th>
              <th class="py-3 px-3 text-right">Kas Fisik Aktual</th>
              <th class="py-3 px-3 text-right">Selisih Kas</th>
              <th class="py-3 px-3 text-center">Status</th>
              <th class="py-3 px-3 text-center">Validasi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @foreach($shifts as $s)
              <tr class="hover:bg-panelBg/50 transition">
                <td class="py-3.5 px-3 font-mono font-extrabold text-sidebar">
                  {{ $s->shift_number }}
                </td>
                <td class="py-3.5 px-3 font-bold text-textMain">
                  {{ $s->cashier->name }}
                </td>
                <td class="py-3.5 px-3 text-textMuted text-[11px]">
                  <span>{{ $s->opened_at->format('d/m H:i') }}</span>
                  <span class="text-textMain block">
                    {{ $s->closed_at ? $s->closed_at->format('d/m H:i') : 'Masih Terbuka' }}
                  </span>
                </td>
                <td class="py-3.5 px-3 text-right font-mono font-bold text-sidebar">
                  Rp {{ number_format($s->starting_cash, 0, ',', '.') }}
                </td>
                <td class="py-3.5 px-3 text-right font-mono font-bold text-textMuted">
                  {{ $s->expected_cash !== null ? 'Rp ' . number_format($s->expected_cash, 0, ',', '.') : '-' }}
                </td>
                <td class="py-3.5 px-3 text-right font-mono font-bold text-sidebar">
                  {{ $s->actual_cash !== null ? 'Rp ' . number_format($s->actual_cash, 0, ',', '.') : '-' }}
                </td>
                <td class="py-3.5 px-3 text-right font-mono font-bold">
                  @if($s->difference !== null)
                    @if(abs($s->difference) < 0.01)
                      <span class="text-emerald-600">Rp 0 (Pas)</span>
                    @elseif($s->difference < 0)
                      <span class="text-rose-600 font-black">-Rp {{ number_format(abs($s->difference), 0, ',', '.') }}</span>
                    @else
                      <span class="text-amber-600 font-black">+Rp {{ number_format($s->difference, 0, ',', '.') }}</span>
                    @endif
                  @else
                    <span class="text-gray-400">-</span>
                  @endif
                </td>
                <td class="py-3.5 px-3 text-center">
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $s->status_badge_class }}">
                    {{ $s->status_label }}
                  </span>
                </td>
                <td class="py-3.5 px-3 text-center">
                  @if($s->status === 'CLOSED' && $isSupervisor)
                    <form action="{{ route('store.shifts.validate', $s->id) }}" method="POST">
                      @csrf
                      <button type="submit" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-[11px] rounded-lg transition border border-blue-200">
                        Validasi
                      </button>
                    </form>
                  @elseif($s->status === 'VALIDATED')
                    <span class="text-[10px] text-textMuted font-semibold">Oleh {{ $s->validator?->name }}</span>
                  @else
                    <span class="text-gray-400">-</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-4 pt-4 border-t border-[#ECEBF4]">
        {{ $shifts->links() }}
      </div>
    @endif
  </div>
</main>

<!-- Open Shift Modal -->
<div id="openShiftModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl border border-[#ECEBF4] max-w-md w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-200">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <h3 class="font-display font-bold text-base text-textMain flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
        <span>Buka Shift Laci Kasir Baru</span>
      </h3>
      <button type="button" onclick="closeStartShiftModal()" class="text-textMuted hover:text-textMain p-1 rounded-lg">✕</button>
    </div>

    <form action="{{ route('store.shifts.open') }}" method="POST" class="space-y-4 text-xs">
      @csrf
      <div>
        <label class="block font-bold text-textMain mb-1.5">Modal Awal Laci / Kas Awal (Rp) <span class="text-rose-500">*</span></label>
        <input type="number" step="1000" min="0" name="starting_cash" value="200000" required
               class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-sm font-mono font-bold text-sidebar focus:outline-none focus:border-sidebar">
        <span class="text-[10px] text-textMuted mt-1 block">Uang pecahan kecil untuk kembalian pelanggan.</span>
      </div>

      <div>
        <label class="block font-bold text-textMain mb-1.5">Catatan Shift (Opsional)</label>
        <input type="text" name="notes" placeholder="Contoh: Shift Pagi pk 08:00 - 16:00 WIB"
               class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar">
      </div>

      <div class="flex items-center justify-end gap-2 pt-2">
        <button type="button" onclick="closeStartShiftModal()" class="px-3.5 py-2 bg-panelBg text-sidebar font-bold text-xs rounded-xl hover:bg-gray-200 transition">
          Batal
        </button>
        <button type="submit" class="px-5 py-2 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl shadow-sm transition">
          Buka Shift Sekarang
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Close Shift Modal (Blind Cash Count) -->
<div id="closeShiftModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl border border-[#ECEBF4] max-w-md w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-200">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <h3 class="font-display font-bold text-base text-brandOrange flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-brandOrange"></span>
        <span>Tutup Shift & Hitung Kas Fisik</span>
      </h3>
      <button type="button" onclick="closeCloseShiftModal()" class="text-textMuted hover:text-textMain p-1 rounded-lg">✕</button>
    </div>

    <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
      <p class="font-bold">Prosedur Blind Cash Count:</p>
      <p class="text-[11px] leading-relaxed text-amber-800">
        Hitung seluruh lembar dan koin fisik yang ada di dalam laci uang kasir secara mandiri. Sistem akan membandingkan dengan catatan penjualan dan menerbitkan anomali jika ditemukan selisih.
      </p>
    </div>

    <form id="closeShiftForm" action="" method="POST" class="space-y-4 text-xs">
      @csrf
      <div>
        <label class="block font-bold text-textMain mb-1.5">Jumlah Uang Kas Fisik Nyata di Laci (Rp) <span class="text-rose-500">*</span></label>
        <input type="number" step="1" min="0" name="actual_cash" required placeholder="Masukkan total uang hasil hitungan fisik..."
               class="w-full px-3.5 py-3 bg-panelBg rounded-xl border border-[#ECEBF4] text-base font-mono font-black text-sidebar focus:outline-none focus:border-sidebar">
      </div>

      <div>
        <label class="block font-bold text-textMain mb-1.5">Catatan Penutupan Shift</label>
        <input type="text" name="notes" placeholder="Contoh: Laci uang diserahkan ke kasir shift berikutnya..."
               class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar">
      </div>

      <div class="flex items-center justify-end gap-2 pt-2">
        <button type="button" onclick="closeCloseShiftModal()" class="px-3.5 py-2 bg-panelBg text-sidebar font-bold text-xs rounded-xl hover:bg-gray-200 transition">
          Batal
        </button>
        <button type="submit" class="px-5 py-2 bg-brandOrange hover:bg-brandOrange/90 text-white font-bold text-xs rounded-xl shadow-sm transition">
          Konfirmasi & Tutup Shift
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function openStartShiftModal() {
    document.getElementById('openShiftModal').classList.remove('hidden');
  }

  function closeStartShiftModal() {
    document.getElementById('openShiftModal').classList.add('hidden');
  }

  function openCloseShiftModal(shiftId, shiftNumber) {
    document.getElementById('closeShiftForm').action = `/store/shifts/${shiftId}/close`;
    document.getElementById('closeShiftModal').classList.remove('hidden');
  }

  function closeCloseShiftModal() {
    document.getElementById('closeShiftModal').classList.add('hidden');
  }
</script>
@endsection

@section('right_panel')
  @include('store.partials.right-panel')
@endsection
