@extends('layouts.app')

@section('title', 'Buat Pesanan Grosir B2B — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Bar & Breadcrumb -->
  <div class="flex items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('b2b.index') }}" class="hover:text-sidebar transition">Kemitraan B2B</a>
        <span>/</span>
        <a href="{{ route('b2b.orders.index') }}" class="hover:text-sidebar transition">Pesanan</a>
        <span>/</span>
        <span class="text-sidebar font-bold">Buat Order Grosir Baru</span>
      </div>
      <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
        Formulir Pemesanan Grosir &amp; Reservasi Stok B2B
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Penjualan tempo berplafon, alokasi stok otomatis, dan penerbitan tiket serah terima QR one-time.
      </p>
    </div>

    <a href="{{ route('b2b.orders.index') }}" class="px-4 py-2 bg-white hover:bg-panelBg text-sidebar text-xs font-bold rounded-2xl border border-[#ECEBF4] transition inline-flex items-center gap-2 shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      <span>Kembali ke Daftar</span>
    </a>
  </div>

  @if(session('error'))
    <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-start gap-3">
      <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      <div>
        <p class="font-bold">Transaksi Ditolak Sistem (Server Guard):</p>
        <p class="mt-0.5 text-red-700">{{ session('error') }}</p>
      </div>
    </div>
  @endif

  <!-- Main Order Form -->
  <form id="b2bOrderForm" method="POST" action="{{ route('b2b.orders.store') }}" class="space-y-6">
    @csrf

    <!-- SECTION 1: MITRA & KREDIT MONITOR -->
    <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-5">
      <h3 class="font-display font-bold text-base text-textMain flex items-center gap-2">
        <span class="w-6 h-6 rounded-full bg-sidebar text-white text-xs flex items-center justify-center font-bold">1</span>
        <span>Informasi Rekanan B2B &amp; Lokasi Pemenuhan</span>
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Partner Selection -->
        <div>
          <label class="block text-xs font-bold text-textMain mb-1.5">Pilih Mitra Grosir B2B <span class="text-red-500">*</span></label>
          <select id="memberSelect" name="member_id" class="w-full text-xs rounded-xl border border-[#ECEBF4] px-3.5 py-2.5 bg-white focus:outline-none focus:border-sidebar" required onchange="handleMemberChange(this.value)">
            <option value="">-- Pilih Rekanan Grosir --</option>
            @foreach($b2bMembers as $m)
              <option value="{{ $m->id }}" {{ (old('member_id', request('member_id')) == $m->id) ? 'selected' : '' }}>
                {{ $m->name }} ({{ $m->member_code }}) — Plafon: Rp {{ number_format($m->credit_limit, 0, ',', '.') }}
              </option>
            @endforeach
          </select>
          @error('member_id')
            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Fulfillment Location -->
        <div>
          <label class="block text-xs font-bold text-textMain mb-1.5">Lokasi Serah Terima (Stok Reservasi) <span class="text-red-500">*</span></label>
          <select name="fulfillment_location_id" class="w-full text-xs rounded-xl border border-[#ECEBF4] px-3.5 py-2.5 bg-white focus:outline-none focus:border-sidebar" required>
            @foreach($locations as $loc)
              <option value="{{ $loc->id }}" {{ (old('fulfillment_location_id', $activeLocation?->id) == $loc->id) ? 'selected' : '' }}>
                {{ $loc->name }} ({{ $loc->code }})
              </option>
            @endforeach
          </select>
          @error('fulfillment_location_id')
            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
          @enderror
        </div>

        <!-- Payment Term -->
        <div>
          <label class="block text-xs font-bold text-textMain mb-1.5">Ketentuan Pembayaran (Term of Payment) <span class="text-red-500">*</span></label>
          <select id="paymentTermSelect" name="payment_term" class="w-full text-xs rounded-xl border border-[#ECEBF4] px-3.5 py-2.5 bg-white focus:outline-none focus:border-sidebar" required onchange="evaluateCreditLock()">
            <option value="CASH" {{ old('payment_term') === 'CASH' ? 'selected' : '' }}>Tunai / Cash Before Delivery (CBD)</option>
            <option value="NET_7" {{ old('payment_term') === 'NET_7' ? 'selected' : '' }}>Tempo 7 Hari (NET_7)</option>
            <option value="NET_14" {{ old('payment_term') === 'NET_14' ? 'selected' : '' }}>Tempo 14 Hari (NET_14)</option>
            <option value="NET_30" {{ old('payment_term') === 'NET_30' ? 'selected' : '' }}>Tempo 30 Hari (NET_30)</option>
          </select>
          @error('payment_term')
            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <!-- Live Member Credit Status Panel -->
      <div id="creditPanel" class="hidden p-4 rounded-2xl bg-panelBg border border-[#ECEBF4] space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#ECEBF4] pb-2.5">
          <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-sidebar"></span>
            <span class="text-xs font-bold text-textMain" id="partnerNameTitle">Rekanan</span>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800" id="partnerTierBadge">TIER</span>
          </div>
          <div class="text-[11px] font-semibold text-textMuted">
            Diskon Rekanan Otomatis: <span class="text-sidebar font-bold" id="partnerDiscountVal">0%</span>
          </div>
        </div>

        <!-- Overdue Warning Banner -->
        <div id="overdueAlert" class="hidden p-3 rounded-xl bg-red-100 border border-red-200 text-red-800 text-xs font-semibold flex items-center gap-2.5">
          <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          <div>
            <span class="font-bold">PERINGATAN OVERDUE:</span>
            <span id="overdueText">Mitra memiliki tagihan jatuh tempo yang belum lunas. Pembayaran tempo diblokir oleh server!</span>
          </div>
        </div>

        <!-- Credit Numbers Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
          <div>
            <span class="text-[10px] text-textMuted font-bold uppercase">Total Plafon</span>
            <p class="font-mono font-bold text-textMain text-sm" id="valCreditLimit">Rp 0</p>
          </div>
          <div>
            <span class="text-[10px] text-textMuted font-bold uppercase">Piutang Terbuka (Faktur)</span>
            <p class="font-mono font-bold text-sidebar text-sm" id="valInvoicedDebt">Rp 0</p>
          </div>
          <div>
            <span class="text-[10px] text-textMuted font-bold uppercase">Pesanan Aktif (Reserved)</span>
            <p class="font-mono font-bold text-purple-600 text-sm" id="valUninvoicedOrders">Rp 0</p>
          </div>
          <div>
            <span class="text-[10px] text-textMuted font-bold uppercase">Sisa Plafon Tersedia</span>
            <p class="font-mono font-extrabold text-emerald-600 text-sm" id="valAvailableCredit">Rp 0</p>
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION 2: ITEM PESANAN & RESERVASI STOK -->
    <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="font-display font-bold text-base text-textMain flex items-center gap-2">
          <span class="w-6 h-6 rounded-full bg-sidebar text-white text-xs flex items-center justify-center font-bold">2</span>
          <span>Daftar Produk yang Dipesan &amp; Direservasi</span>
        </h3>
        <button type="button" onclick="addProductRow()" class="px-3.5 py-1.5 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 shadow-sm">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          <span>Tambah Baris Produk</span>
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted">
              <th class="py-2.5 px-3 font-bold uppercase text-[10px] w-2/5">Produk</th>
              <th class="py-2.5 px-3 font-bold uppercase text-[10px] w-1/5">Satuan (UOM)</th>
              <th class="py-2.5 px-3 font-bold uppercase text-[10px] w-24">Jumlah</th>
              <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Harga Satuan (Rp)</th>
              <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Subtotal (Rp)</th>
              <th class="py-2.5 px-3 font-bold uppercase text-[10px] text-right w-12">Hapus</th>
            </tr>
          </thead>
          <tbody id="itemsTableBody" class="divide-y divide-[#ECEBF4]">
            <!-- Dynamic rows will be inserted here -->
          </tbody>
        </table>
      </div>

      <!-- Financial Total Summary -->
      <div class="p-5 rounded-2xl bg-panelBg border border-[#ECEBF4] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="text-xs text-textMuted max-w-md">
          <p class="font-semibold text-textMain">Catatan Sistem Reservasi:</p>
          <p class="text-[11px] mt-0.5">Saat pesanan dibuat, kuantitas fisik akan langsung masuk ke <span class="font-bold text-purple-700">reserved_qty</span> di lokasi yang dipilih sehingga aman dari transaksi kasir POS atau order lain.</p>
        </div>

        <div class="w-full sm:w-72 space-y-2 text-right">
          <div class="flex justify-between text-xs text-textMuted">
            <span>Subtotal Produk:</span>
            <span class="font-mono font-bold text-textMain" id="summarySubtotal">Rp 0</span>
          </div>
          <div class="flex justify-between text-xs text-textMuted">
            <span>Diskon Rekanan (<span id="discountPercentText">0%</span>):</span>
            <span class="font-mono font-semibold text-emerald-700" id="summaryDiscount">- Rp 0</span>
          </div>
          <div class="border-t border-[#ECEBF4] pt-2 flex justify-between items-baseline">
            <span class="font-bold text-xs text-textMain">Total Pesanan:</span>
            <span class="font-display font-extrabold text-xl text-sidebar" id="summaryTotal">Rp 0</span>
          </div>

          <!-- Credit Ceiling Realtime Warning -->
          <div id="creditCeilingAlert" class="hidden text-left p-2.5 rounded-xl bg-red-100 border border-red-200 text-red-700 text-[11px] font-bold">
            Pesanan melebihi sisa plafon kredit! Opsi tempo akan ditolak server.
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION 3: CATATAN & SUBMIT -->
    <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-4">
      <h3 class="font-display font-bold text-base text-textMain flex items-center gap-2">
        <span class="w-6 h-6 rounded-full bg-sidebar text-white text-xs flex items-center justify-center font-bold">3</span>
        <span>Catatan &amp; Konfirmasi Pemesanan</span>
      </h3>

      <div>
        <label class="block text-xs font-bold text-textMain mb-1">Catatan Tambahan untuk Petugas Gudang / Toko</label>
        <textarea name="notes" rows="2" placeholder="Instruksi packing khusus, estimasi waktu penjemputan, dsb..." class="w-full text-xs rounded-xl border border-[#ECEBF4] p-3 focus:outline-none focus:border-sidebar">{{ old('notes') }}</textarea>
      </div>

      <div class="pt-2 flex items-center justify-end gap-3">
        <a href="{{ route('b2b.orders.index') }}" class="px-5 py-3 rounded-2xl border border-[#ECEBF4] text-xs font-bold text-textMuted hover:text-sidebar transition">
          Batal
        </a>
        <button id="btnSubmitOrder" type="submit" class="px-6 py-3 bg-brandOrange hover:bg-orange-600 text-white text-xs font-bold rounded-2xl transition shadow-sm flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          <span>Buat Pesanan &amp; Reservasi Stok Sekarang</span>
        </button>
      </div>
    </div>
  </form>
</main>

<script>
  const productsCatalog = @json($products);
  let activeMemberExposure = null;
  let currentDiscountPercent = 0;
  let rowCounter = 0;

  document.addEventListener('DOMContentLoaded', function () {
    const initialMember = document.getElementById('memberSelect').value;
    if (initialMember) {
      handleMemberChange(initialMember);
    }
    // Add first item row by default
    addProductRow();
  });

  function handleMemberChange(memberId) {
    if (!memberId) {
      document.getElementById('creditPanel').classList.add('hidden');
      activeMemberExposure = null;
      currentDiscountPercent = 0;
      recalculateAll();
      return;
    }

    fetch(`/b2b/credit-check/${memberId}`)
      .then(res => res.json())
      .then(data => {
        if (!data.success) return;
        activeMemberExposure = data.exposure;
        currentDiscountPercent = data.member.discount_percent || 0;

        document.getElementById('partnerNameTitle').innerText = data.member.name + ' (' + data.member.code + ')';
        document.getElementById('partnerTierBadge').innerText = data.member.tier;
        document.getElementById('partnerDiscountVal').innerText = currentDiscountPercent + '%';
        document.getElementById('discountPercentText').innerText = currentDiscountPercent + '%';

        document.getElementById('valCreditLimit').innerText = formatIdr(data.exposure.credit_limit);
        document.getElementById('valInvoicedDebt').innerText = formatIdr(data.exposure.invoiced_outstanding);
        document.getElementById('valUninvoicedOrders').innerText = formatIdr(data.exposure.un_invoiced_active_exposure);
        document.getElementById('valAvailableCredit').innerText = formatIdr(data.exposure.available_credit);

        const overdueEl = document.getElementById('overdueAlert');
        if (data.exposure.has_overdue_invoices) {
          overdueEl.classList.remove('hidden');
          document.getElementById('overdueText').innerText = 
            `Mitra memiliki ${data.exposure.overdue_count} faktur jatuh tempo belum lunas (Total: ${formatIdr(data.exposure.overdue_amount)}). Opsi pembayaran tempo akan DITOLAK server!`;
        } else {
          overdueEl.classList.add('hidden');
        }

        document.getElementById('creditPanel').classList.remove('hidden');
        recalculateAll();
        evaluateCreditLock();
      })
      .catch(err => console.error('Credit check failed', err));
  }

  function addProductRow() {
    rowCounter++;
    const tbody = document.getElementById('itemsTableBody');
    const tr = document.createElement('tr');
    tr.id = `row-${rowCounter}`;
    tr.className = 'hover:bg-panelBg/50 transition';

    let productOptions = '<option value="">-- Pilih Produk --</option>';
    productsCatalog.forEach(p => {
      productOptions += `<option value="${p.id}" data-price="${p.price}">${p.name} (${p.sku})</option>`;
    });

    tr.innerHTML = `
      <td class="py-3 px-3">
        <select name="items[${rowCounter}][product_id]" class="w-full text-xs rounded-xl border border-[#ECEBF4] p-2 bg-white focus:outline-none focus:border-sidebar" required onchange="handleProductSelect(${rowCounter}, this.value)">
          ${productOptions}
        </select>
      </td>
      <td class="py-3 px-3">
        <select name="items[${rowCounter}][product_uom_id]" id="uom-select-${rowCounter}" class="w-full text-xs rounded-xl border border-[#ECEBF4] p-2 bg-white focus:outline-none focus:border-sidebar" onchange="handleUomChange(${rowCounter})">
          <option value="">Satuan Dasar</option>
        </select>
      </td>
      <td class="py-3 px-3">
        <input type="number" step="0.001" min="0.001" name="items[${rowCounter}][quantity]" id="qty-${rowCounter}" value="1" class="w-full text-xs rounded-xl border border-[#ECEBF4] p-2 text-center focus:outline-none focus:border-sidebar" required oninput="calculateRowSubtotal(${rowCounter})">
      </td>
      <td class="py-3 px-3">
        <input type="number" step="0.01" min="0" name="items[${rowCounter}][unit_price]" id="price-${rowCounter}" value="0" class="w-full text-xs rounded-xl border border-[#ECEBF4] p-2 font-mono text-right focus:outline-none focus:border-sidebar" required oninput="calculateRowSubtotal(${rowCounter})">
      </td>
      <td class="py-3 px-3 font-mono font-bold text-sidebar text-right" id="subtotal-${rowCounter}">
        Rp 0
      </td>
      <td class="py-3 px-3 text-right">
        <button type="button" onclick="removeRow(${rowCounter})" class="p-1.5 text-textMuted hover:text-red-600 rounded-lg transition" title="Hapus Baris">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  }

  function removeRow(id) {
    const row = document.getElementById(`row-${id}`);
    if (row) row.remove();
    recalculateAll();
  }

  function handleProductSelect(rowId, productId) {
    const product = productsCatalog.find(p => p.id == productId);
    const uomSelect = document.getElementById(`uom-select-${rowId}`);
    const priceInput = document.getElementById(`price-${rowId}`);

    uomSelect.innerHTML = '';
    if (!product) {
      priceInput.value = 0;
      calculateRowSubtotal(rowId);
      return;
    }

    // Default base UOM option
    const baseUomName = product.base_uom ? product.base_uom.name : 'Satuan Dasar';
    let options = `<option value="" data-factor="1">${baseUomName} (1.0)</option>`;

    if (product.uoms && product.uoms.length > 0) {
      product.uoms.forEach(pu => {
        const uName = pu.uom ? pu.uom.name : 'UOM';
        options += `<option value="${pu.id}" data-factor="${pu.conversion_factor}">${uName} (${pu.conversion_factor})</option>`;
      });
    }
    uomSelect.innerHTML = options;

    priceInput.value = product.price || 0;
    calculateRowSubtotal(rowId);
  }

  function handleUomChange(rowId) {
    calculateRowSubtotal(rowId);
  }

  function calculateRowSubtotal(rowId) {
    const qty = parseFloat(document.getElementById(`qty-${rowId}`)?.value || 0);
    const price = parseFloat(document.getElementById(`price-${rowId}`)?.value || 0);
    const subtotal = Math.max(0, qty * price);

    const subtotalEl = document.getElementById(`subtotal-${rowId}`);
    if (subtotalEl) {
      subtotalEl.innerText = formatIdr(subtotal);
      subtotalEl.dataset.subtotal = subtotal;
    }
    recalculateAll();
  }

  function recalculateAll() {
    let grandSubtotal = 0;
    document.querySelectorAll('[data-subtotal]').forEach(el => {
      grandSubtotal += parseFloat(el.dataset.subtotal || 0);
    });

    const discountAmount = grandSubtotal * (currentDiscountPercent / 100);
    const netTotal = Math.max(0, grandSubtotal - discountAmount);

    document.getElementById('summarySubtotal').innerText = formatIdr(grandSubtotal);
    document.getElementById('summaryDiscount').innerText = '- ' + formatIdr(discountAmount);
    document.getElementById('summaryTotal').innerText = formatIdr(netTotal);
    document.getElementById('summaryTotal').dataset.total = netTotal;

    evaluateCreditLock();
  }

  function evaluateCreditLock() {
    const paymentTerm = document.getElementById('paymentTermSelect').value;
    const alertEl = document.getElementById('creditCeilingAlert');
    const netTotal = parseFloat(document.getElementById('summaryTotal').dataset.total || 0);

    if (paymentTerm === 'CASH' || !activeMemberExposure) {
      alertEl.classList.add('hidden');
      return;
    }

    if (activeMemberExposure.has_overdue_invoices) {
      alertEl.classList.remove('hidden');
      alertEl.innerText = 'Transaksi tempo DIBLOKIR: Mitra memiliki tagihan jatuh tempo (overdue)!';
      return;
    }

    if (netTotal > activeMemberExposure.available_credit) {
      alertEl.classList.remove('hidden');
      alertEl.innerText = `Total pesanan (${formatIdr(netTotal)}) melebihi sisa plafon (${formatIdr(activeMemberExposure.available_credit)})! Server akan menolak tempo.`;
    } else {
      alertEl.classList.add('hidden');
    }
  }

  function formatIdr(val) {
    return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
  }

  // Double submit guard
  document.getElementById('b2bOrderForm').addEventListener('submit', function () {
    const btn = document.getElementById('btnSubmitOrder');
    btn.disabled = true;
    btn.innerText = 'Memproses Reservasi & Tiket...';
  });
</script>
@endsection
