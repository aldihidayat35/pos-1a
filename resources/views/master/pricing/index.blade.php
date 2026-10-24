@extends('layouts.app')

@section('title', 'Kebijakan Harga & Floor Price Lock — PasarHub')

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

  <!-- Top Breadcrumb & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('master.products.index') }}" class="hover:underline">Master Data</a>
        <span>/</span>
        <span class="text-textMain font-bold">Kebijakan Harga & Floor Lock</span>
      </div>
      <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
        Kebijakan Harga & Floor Price Lock
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Perlindungan margin laba: Melarang penjualan di bawah HPP Moving Average + Minimum Markup di level server.
      </p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('master.products.index') }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
        ← Katalog Produk Master
      </a>
    </div>
  </div>

  <!-- Formula & Protection Banner -->
  <div class="p-5 bg-white rounded-3xl border border-[#ECEBF4] shadow-sm space-y-3">
    <div class="flex items-center gap-2 font-bold text-sm text-textMain">
      <span class="w-2.5 h-2.5 rounded-full bg-brandOrange"></span>
      <span>Rumus Kebijakan Perlindungan Floor Price (Fase 4):</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
      <div class="p-3.5 bg-panelBg/70 rounded-2xl border border-[#ECEBF4] space-y-1">
        <span class="text-textMuted text-[10px] uppercase font-bold tracking-wider block">1. Formula Batas Dasar (Floor Price)</span>
        <p class="font-mono font-bold text-sidebar text-xs">
          Floor Price = Current Unit Cost (HPP) + Minimum Markup
        </p>
        <p class="text-[11px] text-textMuted">
          Markup dapat berupa persentase (%) atau nominal rupiah tetap (Fixed Rp) sesuai kanal penjualan.
        </p>
      </div>

      <div class="p-3.5 bg-panelBg/70 rounded-2xl border border-[#ECEBF4] space-y-1">
        <span class="text-textMuted text-[10px] uppercase font-bold tracking-wider block">2. Batas Minimum Efektif (Floor Lock)</span>
        <p class="font-mono font-bold text-rose-600 text-xs">
          Effective Min Price = MAX(Market Min Price, Floor Price)
        </p>
        <p class="text-[11px] text-textMuted">
          Kasir ritel & penjualan B2B otomatis diblokir di server (HTTP 422) jika harga tawar <span class="font-bold text-rose-600">&lt; Effective Min Price</span>.
        </p>
      </div>
    </div>
  </div>

  <!-- Channels Policies Cards -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($policies as $policy)
      <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
          <div class="flex items-center gap-2.5">
            <span class="w-3 h-3 rounded-full {{ $policy->channel === 'RETAIL' ? 'bg-brandOrange' : 'bg-sidebar' }}"></span>
            <div>
              <h3 class="font-bold text-sm text-textMain">Kanal {{ $policy->name }}</h3>
              <span class="text-[10px] text-textMuted font-mono">Channel: {{ $policy->channel }}</span>
            </div>
          </div>
          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
            Aktif Terkunci
          </span>
        </div>

        <div class="grid grid-cols-2 gap-3 text-xs">
          <div class="p-3 bg-panelBg/60 rounded-xl">
            <span class="text-[10px] text-textMuted uppercase font-bold block mb-1">Tipe Markup:</span>
            <span class="font-extrabold text-sidebar">{{ $policy->min_markup_type }}</span>
          </div>
          <div class="p-3 bg-panelBg/60 rounded-xl">
            <span class="text-[10px] text-textMuted uppercase font-bold block mb-1">Nilai Markup Minimum:</span>
            <span class="font-extrabold text-brandOrange font-mono text-sm">
              {{ $policy->min_markup_type === 'PERCENTAGE' ? $policy->min_markup_value . ' %' : 'Rp ' . number_format($policy->min_markup_value, 0, ',', '.') }}
            </span>
          </div>
        </div>

        @if($canManage)
          <form action="{{ route('master.pricing.policy.update') }}" method="POST" class="pt-2 border-t border-[#ECEBF4] space-y-3">
            @csrf
            @method('PATCH')
            <input type="hidden" name="policy_id" value="{{ $policy->id }}">

            <div class="flex items-center gap-2">
              <select name="min_markup_type" class="px-2.5 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none">
                <option value="PERCENTAGE" {{ $policy->min_markup_type === 'PERCENTAGE' ? 'selected' : '' }}>Persentase (%)</option>
                <option value="FIXED" {{ $policy->min_markup_type === 'FIXED' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
              </select>
              <input type="number" step="0.1" min="0" name="min_markup_value" value="{{ $policy->min_markup_value }}" required
                     class="w-24 px-2.5 py-1.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-bold text-sidebar text-right focus:outline-none">
              <button type="submit" class="px-3 py-1.5 bg-sidebar hover:bg-sidebar/90 text-white font-bold text-xs rounded-xl transition">
                Simpan
              </button>
            </div>
          </form>
        @endif
      </div>
    @endforeach
  </div>

  <!-- Interactive Sandbox: Test Floor Price Lock Live -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <div>
        <h3 class="font-bold text-sm text-textMain flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span>Simulator Uji Validasi Server Floor Price (Live Sandbox)</span>
        </h3>
        <p class="text-xs text-textMuted mt-0.5">
          Simulasikan penawaran harga kasir atau sales B2B. Cek apakah server akan menerima atau memblokir transaksi ini.
        </p>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs items-end">
      <div>
        <label class="block font-bold text-textMain mb-1.5">Pilih Produk</label>
        <select id="sim_product_id" class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none">
          @foreach($pricingMatrix as $row)
            <option value="{{ $row['product']->id }}">
              {{ $row['product']->name }} (HPP: Rp {{ number_format($row['limits']['current_unit_cost'], 0, ',', '.') }})
            </option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block font-bold text-textMain mb-1.5">Kanal Penjualan</label>
        <select id="sim_channel" class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none">
          <option value="RETAIL">RETAIL (Min Markup 10%)</option>
          <option value="B2B">B2B / Grosir (Min Markup 5%)</option>
        </select>
      </div>

      <div>
        <label class="block font-bold text-textMain mb-1.5">Harga Tawar / Dijual (Rp)</label>
        <input type="number" id="sim_offered_price" placeholder="Contoh: 12000"
               class="w-full px-3 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-bold text-sidebar text-right focus:outline-none">
      </div>

      <div>
        <button type="button" onclick="testPriceCheck()" class="w-full py-2 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-1.5">
          <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>Uji Validasi Server</span>
        </button>
      </div>
    </div>

    <!-- Live Test Result Output Box -->
    <div id="sim_result_box" class="hidden p-4 rounded-2xl text-xs transition"></div>
  </div>

  <!-- Pricing Matrix Table -->
  <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
    <div class="border-b border-[#ECEBF4] pb-3">
      <h3 class="font-bold text-sm text-textMain">Matriks Perlindungan Floor Price Produk Siap Jual</h3>
      <p class="text-xs text-textMuted mt-0.5">Batas minimum harga yang dihitung otomatis berdasarkan HPP Moving Average aktual.</p>
    </div>

    <!-- Desktop View: Matrix Table -->
    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="border-b border-[#ECEBF4] text-textMuted font-bold uppercase tracking-wider text-[11px]">
            <th class="py-3 px-3">Produk</th>
            <th class="py-3 px-3 text-right">HPP Berjalan</th>
            <th class="py-3 px-3 text-right">Min Markup (10%)</th>
            <th class="py-3 px-3 text-right">Floor Price</th>
            <th class="py-3 px-3 text-right">Market Min</th>
            <th class="py-3 px-3 text-right">Batas Min Efektif</th>
            <th class="py-3 px-3 text-right">Harga Katalog</th>
            <th class="py-3 px-3 text-center">Proteksi Server</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#ECEBF4]">
          @foreach($pricingMatrix as $row)
            @php $limits = $row['limits']; @endphp
            <tr class="hover:bg-panelBg/40 transition">
              <td class="py-3 px-3">
                <span class="font-bold text-textMain block">{{ $row['product']->name }}</span>
                <span class="text-[10px] text-textMuted font-mono">{{ $row['product']->sku }}</span>
              </td>
              <td class="py-3 px-3 text-right font-mono font-bold text-sidebar">
                Rp {{ number_format($limits['current_unit_cost'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-3 text-right font-mono text-textMuted">
                +Rp {{ number_format($limits['markup_amount'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-3 text-right font-mono font-bold text-amber-700">
                Rp {{ number_format($limits['floor_price'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-3 text-right font-mono text-textMuted">
                Rp {{ number_format($limits['market_min_price'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-3 text-right font-mono font-black text-rose-600 bg-rose-50/40">
                Rp {{ number_format($limits['effective_min_price'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-3 text-right font-mono font-bold text-emerald-700">
                Rp {{ number_format($limits['list_price'], 0, ',', '.') }}
              </td>
              <td class="py-3 px-3 text-center">
                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                  TERKUNCI
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Mobile View: Card Stack -->
    <div class="block md:hidden divide-y divide-[#ECEBF4]">
      @foreach($pricingMatrix as $row)
        @php $limits = $row['limits']; @endphp
        <div class="py-3.5 space-y-2.5">
          <div class="flex items-start justify-between gap-2">
            <div>
              <span class="inline-block px-1.5 py-0.5 rounded font-mono text-[10px] font-bold bg-sidebar/10 text-sidebar border border-sidebar/20">
                {{ $row['product']->sku }}
              </span>
              <h4 class="font-bold text-sm text-textMain mt-1">{{ $row['product']->name }}</h4>
            </div>
            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
              TERKUNCI
            </span>
          </div>

          <!-- Highlight Effective Floor Price -->
          <div class="bg-rose-50/80 border border-rose-200/80 p-2.5 rounded-xl flex items-center justify-between">
            <div>
              <span class="text-[10px] uppercase font-bold text-rose-700 block">Batas Min Efektif (Floor Lock)</span>
              <span class="text-[11px] text-textMuted">Batas mutlak transaksi sistem</span>
            </div>
            <span class="font-mono font-black text-rose-600 text-sm">
              Rp {{ number_format($limits['effective_min_price'], 0, ',', '.') }}
            </span>
          </div>

          <!-- Breakdown Grid -->
          <div class="grid grid-cols-2 gap-2 text-xs bg-panelBg/60 p-2.5 rounded-xl border border-[#ECEBF4]">
            <div>
              <span class="text-[10px] uppercase font-bold text-textMuted block">HPP Berjalan</span>
              <span class="font-mono font-bold text-sidebar">Rp {{ number_format($limits['current_unit_cost'], 0, ',', '.') }}</span>
            </div>
            <div>
              <span class="text-[10px] uppercase font-bold text-textMuted block">Floor Price (+Markup)</span>
              <span class="font-mono font-bold text-amber-700">Rp {{ number_format($limits['floor_price'], 0, ',', '.') }}</span>
            </div>
            <div>
              <span class="text-[10px] uppercase font-bold text-textMuted block">Market Min</span>
              <span class="font-mono text-textMuted">Rp {{ number_format($limits['market_min_price'], 0, ',', '.') }}</span>
            </div>
            <div>
              <span class="text-[10px] uppercase font-bold text-textMuted block">Harga Katalog</span>
              <span class="font-mono font-bold text-emerald-700">Rp {{ number_format($limits['list_price'], 0, ',', '.') }}</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</main>

<script>
  async function testPriceCheck() {
    const prodId = document.getElementById('sim_product_id').value;
    const channel = document.getElementById('sim_channel').value;
    const offeredPrice = document.getElementById('sim_offered_price').value;
    const resBox = document.getElementById('sim_result_box');

    if (!offeredPrice || offeredPrice <= 0) {
      alert('Harap masukkan nominal harga tawar terlebih dahulu.');
      return;
    }

    resBox.className = 'p-4 rounded-2xl text-xs bg-panelBg border border-[#ECEBF4] text-textMuted';
    resBox.innerHTML = 'Memeriksa otorisasi harga ke server...';
    resBox.classList.remove('hidden');

    try {
      const response = await fetch('{{ route("master.pricing.check") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          product_id: prodId,
          channel: channel,
          offered_price: offeredPrice
        })
      });

      const data = await response.json();

      if (response.ok && data.valid) {
        resBox.className = 'p-4 rounded-2xl text-xs bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-1';
        resBox.innerHTML = `
          <div class="font-bold text-emerald-800 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span>TRANSAKSI DIIZINKAN (VALID)</span>
          </div>
          <p class="text-[11px]">${data.message}</p>
          <div class="text-[10px] text-emerald-700 font-mono mt-1 pt-1 border-t border-emerald-200">
            HPP: Rp ${Math.round(data.limits.current_unit_cost).toLocaleString('id-ID')} |
            Floor Price: Rp ${Math.round(data.limits.floor_price).toLocaleString('id-ID')} |
            Effective Min: Rp ${Math.round(data.limits.effective_min_price).toLocaleString('id-ID')}
          </div>
        `;
      } else {
        resBox.className = 'p-4 rounded-2xl text-xs bg-rose-50 border border-rose-200 text-rose-900 space-y-1';
        resBox.innerHTML = `
          <div class="font-bold text-rose-800 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>TRANSAKSI DITOLAK SERVER (FLOOR PRICE LOCK AKTIF)</span>
          </div>
          <p class="text-[11px]">${data.message}</p>
          <div class="text-[10px] text-rose-700 font-mono mt-1 pt-1 border-t border-rose-200">
            HPP Aktual: Rp ${Math.round(data.limits.current_unit_cost).toLocaleString('id-ID')} |
            Markup Minimum: +Rp ${Math.round(data.limits.markup_amount).toLocaleString('id-ID')} |
            Batas Bawah Minimum: Rp ${Math.round(data.limits.effective_min_price).toLocaleString('id-ID')}
          </div>
        `;
      }
    } catch (err) {
      resBox.className = 'p-4 rounded-2xl text-xs bg-rose-50 border border-rose-200 text-rose-800';
      resBox.textContent = 'Gagal menghubungi server: ' + err.message;
    }
  }
</script>
@endsection

@section('right_panel')
  @include('master.partials.right-panel')
@endsection
