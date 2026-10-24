@extends('layouts.app')

@section('title', 'Daftar Pembelian Emergency Toko — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Flash Notifications -->
  @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
      <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
      </svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  <!-- Top Breadcrumb & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('store.index') }}" class="hover:underline">Operasional Toko</a>
        <span>/</span>
        <span class="text-textMain font-bold">Pembelian Emergency</span>
      </div>
      <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
        Pengadaan Darurat Toko (+Foto Nota)
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Pembelian mendesak saat stok toko habis mendadak tanpa bottleneck persetujuan Owner.
      </p>
    </div>

    <div class="flex items-center gap-2.5">
      <a href="{{ route('store.index') }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
        ← Kembali ke Toko
      </a>
      @if($user->hasPermission('emergency.input') || $user->isSuperAdmin())
        <a href="{{ route('store.emergency.create') }}" class="px-4 py-2 bg-brandOrange hover:bg-brandOrange/90 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
          <span>Input Belanja Emergency</span>
        </a>
      @endif
    </div>
  </div>

  <!-- Purchases Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm">
    @if($purchases->isEmpty())
      <div class="py-12 text-center text-textMuted text-xs">
        Belum ada riwayat pembelian emergency di lokasi toko ini.
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
              <th class="py-3 px-3">No. Dokumen</th>
              <th class="py-3 px-3">Barang Dibeli</th>
              <th class="py-3 px-3">Pedagang / Kios</th>
              <th class="py-3 px-3 text-right">Kuantitas</th>
              <th class="py-3 px-3 text-right">Total Biaya</th>
              <th class="py-3 px-3">Waktu & Petugas</th>
              <th class="py-3 px-3 text-center">Bukti Nota</th>
              <th class="py-3 px-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @foreach($purchases as $emg)
              <tr class="hover:bg-panelBg/60 transition">
                <td class="py-3.5 px-3 font-extrabold text-sidebar font-mono">
                  <a href="{{ route('store.emergency.show', $emg->id) }}" class="text-brandOrange hover:underline">
                    {{ $emg->purchase_number }}
                  </a>
                </td>
                <td class="py-3.5 px-3">
                  <span class="font-bold text-textMain block">{{ $emg->product->name }}</span>
                  <span class="text-[10px] text-textMuted font-mono">{{ $emg->product->sku }}</span>
                </td>
                <td class="py-3.5 px-3 font-medium text-textMain">
                  {{ $emg->merchant_name }}
                </td>
                <td class="py-3.5 px-3 text-right font-extrabold text-sidebar">
                  {{ number_format($emg->qty_input, 1) }}
                  <span class="text-[10px] font-normal text-textMuted">{{ $emg->productUom?->uom?->symbol }}</span>
                </td>
                <td class="py-3.5 px-3 text-right font-extrabold text-amber-700">
                  Rp {{ number_format($emg->total_amount, 0, ',', '.') }}
                </td>
                <td class="py-3.5 px-3 text-textMuted text-[11px]">
                  <span>{{ $emg->posted_at?->format('d/m/Y H:i') }}</span>
                  <span class="block text-[10px] text-textMain font-semibold">Oleh: {{ $emg->creator?->name }}</span>
                </td>
                <td class="py-3.5 px-3 text-center">
                  @if($emg->receipt_photo_path)
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                      Ada Foto
                    </span>
                  @else
                    <span class="text-rose-500 font-bold">Tanpa Foto</span>
                  @endif
                </td>
                <td class="py-3.5 px-3 text-center">
                  <a href="{{ route('store.emergency.show', $emg->id) }}" class="px-2.5 py-1 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-[11px] rounded-lg transition border border-[#ECEBF4]">
                    Detail
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-4 pt-4 border-t border-[#ECEBF4]">
        {{ $purchases->links() }}
      </div>
    @endif
  </div>
</main>
@endsection

@section('right_panel')
  @include('partials.right-panel')
@endsection
