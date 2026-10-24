@extends('layouts.app')

@section('title', 'Input Pembelian Emergency Toko — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Error Validation Alert -->
  @if($errors->any() || session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <span>{{ session('error') ?? 'Harap perbaiki kesalahan input belanja emergency:' }}</span>
      </div>
      @if($errors->any())
        <ul class="list-disc pl-5 font-normal text-[11px] space-y-0.5">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      @endif
    </div>
  @endif

  <!-- Top Breadcrumb & Header -->
  <div class="flex items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('store.index') }}" class="hover:underline">Operasional Toko</a>
        <span>/</span>
        <a href="{{ route('store.emergency.index') }}" class="hover:underline">Pembelian Emergency</a>
        <span>/</span>
        <span class="text-textMain font-bold">Catat Belanja</span>
      </div>
      <h1 class="font-display text-xl sm:text-2xl font-bold text-textMain tracking-tight">
        Input Pembelian Emergency Toko (+Foto Bukti)
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Catat belanja darurat langsung di pasar tanpa menunggu persetujuan bertingkat. Stok langsung aktif di rak.
      </p>
    </div>

    <a href="{{ route('store.emergency.index') }}" class="px-3.5 py-2 bg-panelBg hover:bg-[#EAE8F2] text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4]">
      Batal
    </a>
  </div>

  <!-- Form -->
  <form action="{{ route('store.emergency.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <!-- Operational Context & Warning -->
    <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-900 flex items-start gap-3">
      <div class="w-7 h-7 rounded-xl bg-brandOrange text-white shrink-0 flex items-center justify-center font-black text-xs shadow-sm">
        !
      </div>
      <div class="space-y-1">
        <div class="font-bold text-amber-950">Aturan Pengadaan Darurat Toko (No Owner Bottleneck):</div>
        <p class="text-[11px] leading-relaxed text-amber-800">
          Stok akan langsung bertambah ke rak toko saat formulir ini dikirim. Sebagai gantinya, sistem secara otomatis mewajibkan <strong>foto fisik nota</strong>, membukukan mutasi stok dengan <strong>Kode Warna Oranye (#F59E0B)</strong>, serta menerbitkan peringatan ke <strong>Audit Trail Anomali</strong> untuk verifikasi berkala oleh Owner.
        </p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left 2 Cols: Data Form -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
          <h3 class="font-bold text-sm text-textMain border-b border-[#ECEBF4] pb-3 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-brandOrange"></span>
            <span>1. Detail Barang & Pembelian</span>
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
              <label class="block font-bold text-textMain mb-1.5">Lokasi Toko Bertambah Stok</label>
              <input type="hidden" name="location_id" value="{{ $activeLocation->id }}">
              <div class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-bold text-sidebar flex items-center justify-between">
                <span>{{ $activeLocation->name }} ({{ $activeLocation->code }})</span>
                <span class="text-[10px] bg-white px-2 py-0.5 rounded text-brandOrange border border-amber-200 font-extrabold">TOKO</span>
              </div>
            </div>

            <div>
              <label class="block font-bold text-textMain mb-1.5">Nama Pedagang / Kios / Warung <span class="text-rose-500">*</span></label>
              <input type="text" name="merchant_name" value="{{ old('merchant_name') }}" required
                     placeholder="Misal: Kios Berkah Sayur Blok A-08"
                     class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar">
            </div>

            <div>
              <label class="block font-bold text-textMain mb-1.5">Pilih Produk <span class="text-rose-500">*</span></label>
              <select name="product_id" id="product_id" required onchange="onProductSelectChange()"
                      class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar">
                <option value="">-- Pilih Barang yang Dibeli --</option>
                @foreach($products as $prod)
                  <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                    {{ $prod->name }} ({{ $prod->sku }})
                  </option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block font-bold text-textMain mb-1.5">Satuan Pembelian (UOM) <span class="text-rose-500">*</span></label>
              <select name="product_uom_id" id="product_uom_id" required
                      class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none focus:border-sidebar">
                <option value="">-- Pilih Satuan --</option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-textMain mb-1.5">Jumlah Dibeli <span class="text-rose-500">*</span></label>
              <input type="number" step="0.001" min="0.001" name="qty_input" id="qty_input" value="{{ old('qty_input', '1') }}" required oninput="calculateTotal()"
                     class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-bold text-sidebar text-right focus:outline-none focus:border-sidebar">
            </div>

            <div>
              <label class="block font-bold text-textMain mb-1.5">Harga Beli Satuan (Rp) <span class="text-rose-500">*</span></label>
              <input type="number" step="1" min="1" name="unit_cost_input" id="unit_cost_input" value="{{ old('unit_cost_input', '0') }}" required oninput="calculateTotal()"
                     class="w-full px-3.5 py-2.5 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-bold text-amber-700 text-right focus:outline-none focus:border-sidebar">
            </div>

            <div class="sm:col-span-2">
              <label class="block font-bold text-textMain mb-1.5">Catatan / Alasan Emergency</label>
              <textarea name="notes" rows="2" placeholder="Contoh: Stok beras pulen di rak tiba-tiba habis mendadak pada jam ramai pasar, transfer gudang belum tiba."
                        class="w-full px-3.5 py-2 bg-panelBg rounded-xl border border-[#ECEBF4] text-xs font-medium text-textMain focus:outline-none focus:border-sidebar">{{ old('notes') }}</textarea>
            </div>
          </div>
        </div>

        <!-- Total Calculation Card -->
        <div class="bg-panelBg/70 rounded-3xl border border-[#ECEBF4] p-5 flex items-center justify-between">
          <div>
            <span class="text-xs text-textMuted font-bold uppercase tracking-wider block">Estimasi Total Biaya Tunai:</span>
            <span class="text-[11px] text-textMuted">Dana operasional kas kecil kasir</span>
          </div>
          <div class="text-right">
            <span class="text-xl sm:text-2xl font-black text-amber-700 font-mono" id="total_cost_display">Rp 0</span>
          </div>
        </div>
      </div>

      <!-- Right 1 Col: Mandatory Photo Upload -->
      <div class="space-y-6">
        <div class="bg-white rounded-3xl border border-[#ECEBF4] p-6 shadow-sm space-y-4">
          <h3 class="font-bold text-sm text-textMain border-b border-[#ECEBF4] pb-3 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
            <span>2. Wajib Foto Nota Fisik <span class="text-rose-500">*</span></span>
          </h3>

          <div class="space-y-3">
            <p class="text-[11px] text-textMuted leading-normal">
              Foto bukti struk/kuitansi/nota bertanda tangan pedagang pasar sebagai pertanggungjawaban audit keuangan & pencegahan fraud.
            </p>

            <div class="border-2 border-dashed border-[#ECEBF4] rounded-2xl p-4 text-center hover:border-brandOrange transition bg-panelBg/40 cursor-pointer" onclick="document.getElementById('receipt_photo').click()">
              <div id="preview_container" class="hidden mb-3">
                <img id="photo_preview" src="#" alt="Preview Nota" class="max-h-48 mx-auto rounded-xl shadow-sm border border-gray-200 object-cover">
              </div>

              <div id="upload_prompt" class="space-y-2 py-4">
                <div class="w-10 h-10 rounded-full bg-amber-100 text-brandOrange mx-auto flex items-center justify-center">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                </div>
                <div class="text-xs font-bold text-sidebar">Klik untuk Unggah Foto Nota</div>
                <div class="text-[10px] text-textMuted">Mendukung format JPG, PNG, WEBP (Maksimal 5 MB)</div>
              </div>

              <input type="file" name="receipt_photo" id="receipt_photo" required accept="image/jpeg,image/png,image/webp" onchange="previewReceiptPhoto(this)" class="hidden">
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-3.5 bg-brandOrange hover:bg-brandOrange/90 text-white font-bold text-xs rounded-2xl shadow-md transition flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
          </svg>
          <span>Simpan & Masukkan ke Stok Toko</span>
        </button>
      </div>
    </div>
  </form>
</main>

<script>
  const products = @json($products);

  function onProductSelectChange() {
    const prodId = document.getElementById('product_id').value;
    const uomSelect = document.getElementById('product_uom_id');
    uomSelect.innerHTML = '<option value="">-- Pilih Satuan --</option>';

    if (!prodId) return;

    const prod = products.find(p => p.id == prodId);
    if (prod && prod.product_uoms) {
      prod.product_uoms.forEach(pu => {
        const factorTxt = parseFloat(pu.factor_to_base) > 1 ? ` (Isi ${parseFloat(pu.factor_to_base)} ${prod.base_uom?.symbol ?? ''})` : ' (Satuan Dasar)';
        uomSelect.innerHTML += `<option value="${pu.id}">${pu.uom?.name ?? 'UOM'} [${pu.uom?.symbol ?? ''}]${factorTxt}</option>`;
      });
      uomSelect.selectedIndex = 0;
    }
    calculateTotal();
  }

  function calculateTotal() {
    const qty = parseFloat(document.getElementById('qty_input').value) || 0;
    const cost = parseFloat(document.getElementById('unit_cost_input').value) || 0;
    const total = qty * cost;
    document.getElementById('total_cost_display').textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
  }

  function previewReceiptPhoto(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('photo_preview').src = e.target.result;
        document.getElementById('preview_container').classList.remove('hidden');
        document.getElementById('upload_prompt').classList.add('hidden');
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('product_id').value) {
      onProductSelectChange();
    }
  });
</script>
@endsection

@section('right_panel')
  @include('partials.right-panel')
@endsection
