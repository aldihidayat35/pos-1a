@extends('layouts.app')

@section('title', 'Terminal Kasir POS Ritel — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-4">
  <!-- Shift Warning Alert if No Active Shift -->
  @if(!$activeShift)
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold flex items-center justify-between gap-3 shadow-sm">
      <div class="flex items-center gap-2.5">
        <div class="w-7 h-7 rounded-xl bg-brandOrange text-white flex items-center justify-center font-bold text-xs shrink-0">
          !
        </div>
        <div>
          <span class="font-bold text-amber-950 block">Shift Laci Kasir Belum Dibuka!</span>
          <span class="text-[11px] text-amber-800">SOP Toko mewajibkan pembukaan modal awal kasir sebelum transaksi penjualan dapat diposting.</span>
        </div>
      </div>
      <a href="{{ route('store.shifts.index') }}" class="px-4 py-2 bg-brandOrange hover:bg-brandOrange/90 text-white font-bold text-xs rounded-xl shadow-sm transition shrink-0">
        Buka Shift Sekarang →
      </a>
    </div>
  @endif

  <!-- Top Terminal Bar -->
  <div class="bg-panelBg p-4 sm:p-5 rounded-[22px] border border-[#ECEBF4] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-2xl bg-sidebar text-white flex items-center justify-center font-bold shadow-sm shrink-0">
        <svg class="w-5 h-5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
      </div>
      <div>
        <div class="flex items-center gap-2">
          <h1 class="font-display text-lg sm:text-xl font-bold text-textMain tracking-tight">Terminal Kasir POS</h1>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-sidebar text-white uppercase">{{ $activeLocation->code }}</span>
        </div>
        <p class="text-[11px] text-textMuted mt-0.5">
          Kasir: <strong class="text-textMain">{{ $user->name }}</strong> • 
          @if($activeShift)
            <span class="text-emerald-700 font-bold">Shift: {{ $activeShift->shift_number }} (Aktif)</span>
          @else
            <span class="text-rose-600 font-bold">Laci Terkunci</span>
          @endif
        </p>
      </div>
    </div>

    <div class="flex items-center gap-2 shrink-0">
      <a href="{{ route('store.pos.history') }}" class="px-3.5 py-2 bg-white hover:bg-panelBg text-sidebar font-bold text-xs rounded-xl transition border border-[#ECEBF4] shadow-sm flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Riwayat Struk</span>
      </a>

      <a href="{{ route('store.shifts.index') }}" class="px-3.5 py-2 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        <span>Laci Kasir</span>
      </a>
    </div>
  </div>

  <!-- Main POS Grid: Left Catalog (60%), Right Cart (40%) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
    <!-- Left 7 Cols: Catalog & Search -->
    <div class="lg:col-span-7 space-y-4">
      <!-- Search & Barcode Input Bar -->
      <div class="bg-white rounded-3xl p-4 border border-[#ECEBF4] shadow-sm space-y-3">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-textMuted">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
          <input type="text" id="posSearchInput" placeholder="Scan Barcode atau ketik Nama / SKU Produk (Tekan Enter)..."
                 autofocus oninput="filterCatalog()"
                 class="w-full pl-10 pr-4 py-2.5 bg-panelBg rounded-2xl border border-[#ECEBF4] text-xs font-semibold text-textMain placeholder-textMuted focus:outline-none focus:border-sidebar focus:bg-white transition">
        </div>

        <!-- Category Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
          <button type="button" onclick="selectCategory('ALL')" id="cat-btn-all"
                  class="cat-pill px-3 py-1.5 rounded-xl font-bold bg-sidebar text-white shadow-sm transition whitespace-nowrap">
            Semua ({{ count($catalog) }})
          </button>
          @foreach($categories as $cat)
            <button type="button" onclick="selectCategory('{{ $cat->id }}')" id="cat-btn-{{ $cat->id }}"
                    class="cat-pill px-3 py-1.5 rounded-xl font-bold bg-panelBg hover:bg-[#EAE8F2] text-textMuted transition whitespace-nowrap border border-[#ECEBF4]">
              {{ $cat->name }}
            </button>
          @endforeach
        </div>
      </div>

      <!-- Catalog Cards Grid -->
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="catalogGrid">
        @forelse($catalog as $item)
          <div class="product-card bg-white rounded-2xl border border-[#ECEBF4] p-3.5 shadow-sm hover:border-sidebar hover:shadow transition cursor-pointer flex flex-col justify-between"
               data-id="{{ $item['id'] }}"
               data-name="{{ strtolower($item['name']) }}"
               data-sku="{{ strtolower($item['sku']) }}"
               data-cat="{{ $item['category_id'] }}"
               onclick="addToCart({{ json_encode($item) }})">
            <div>
              <div class="flex items-center justify-between text-[10px] text-textMuted mb-1 font-mono">
                <span>{{ $item['sku'] }}</span>
                <span class="px-1.5 py-0.5 rounded font-bold bg-panelBg text-sidebar">
                  Stok: {{ number_format($item['on_hand_qty'], 1) }}
                </span>
              </div>
              <h3 class="font-bold text-xs text-textMain line-clamp-2 leading-snug">
                {{ $item['name'] }}
              </h3>
            </div>

            <div class="mt-3 pt-2 border-t border-[#ECEBF4] flex items-center justify-between">
              <div>
                <span class="text-[9px] text-textMuted uppercase font-bold block">Harga Jual:</span>
                <span class="font-black text-sidebar font-mono text-xs">
                  Rp {{ number_format($item['list_price'], 0, ',', '.') }}
                </span>
              </div>
              <span class="w-6 h-6 rounded-lg bg-panelBg group-hover:bg-sidebar text-sidebar flex items-center justify-center font-bold text-xs">
                +
              </span>
            </div>
          </div>
        @empty
          <div class="col-span-full py-12 text-center text-xs text-textMuted bg-white rounded-3xl border border-dashed border-[#ECEBF4]">
            Belum ada produk dengan saldo stok on-hand aktif di toko ini.
          </div>
        @endforelse
      </div>
    </div>

    <!-- Right 5 Cols: Active Cart & Checkout -->
    <div class="lg:col-span-5 space-y-4">
      <div class="bg-white rounded-3xl border border-[#ECEBF4] p-5 shadow-sm space-y-4">
        <!-- Cart Header -->
        <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
          <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-movePurple"></span>
            <h2 class="font-bold text-sm text-textMain">Keranjang Belanja</h2>
          </div>
          <button type="button" onclick="clearCart()" class="text-[11px] font-bold text-rose-600 hover:underline">
            Kosongkan
          </button>
        </div>

        <!-- Customer / Member Selector -->
        <div class="p-3 bg-panelBg/70 rounded-2xl border border-[#ECEBF4] space-y-2 text-xs">
          <div class="flex items-center justify-between">
            <span class="text-textMuted text-[10px] uppercase font-bold tracking-wider">Identitas Pelanggan:</span>
            <span class="text-[10px] text-textMuted">Opsional</span>
          </div>
          <div class="grid grid-cols-2 gap-2">
            <input type="text" id="customer_name_input" placeholder="Nama Pelanggan Umum"
                   class="px-2.5 py-1.5 bg-white rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none">
            <select id="member_id_select" class="px-2.5 py-1.5 bg-white rounded-xl border border-[#ECEBF4] text-xs font-semibold text-textMain focus:outline-none">
              <option value="">-- Member Terdaftar --</option>
              @foreach($members as $m)
                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->member_code }})</option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- Cart Line Items -->
        <div id="cartItemsContainer" class="space-y-2.5 max-h-[380px] overflow-y-auto pr-1">
          <!-- Populated by JavaScript -->
          <div id="cartEmptyState" class="py-10 text-center text-textMuted text-xs">
            Keranjang masih kosong. Klik produk di sebelah kiri atau scan barcode untuk menambah.
          </div>
        </div>

        <!-- Price Breakdown -->
        <div class="pt-3 border-t border-[#ECEBF4] space-y-2 text-xs">
          <div class="flex items-center justify-between text-textMuted">
            <span>Subtotal:</span>
            <span class="font-bold text-textMain font-mono text-xs" id="cartSubtotal">Rp 0</span>
          </div>
          <div class="flex items-center justify-between text-textMuted">
            <span>Diskon Nota:</span>
            <input type="number" id="cartDiscount" value="0" min="0" oninput="calculateCartTotals()"
                   class="w-24 px-2 py-0.5 bg-panelBg rounded-lg border border-[#ECEBF4] text-right font-mono font-bold text-xs text-textMain focus:outline-none">
          </div>
          <div class="flex items-center justify-between pt-2 border-t border-[#ECEBF4]">
            <span class="text-xs font-bold text-sidebar uppercase tracking-wider">Total Belanja:</span>
            <span class="text-xl sm:text-2xl font-black text-sidebar font-mono" id="cartGrandTotal">Rp 0</span>
          </div>
        </div>

        <!-- Checkout Action Button -->
        <button type="button" onclick="openPaymentModal()" id="btnOpenPayment"
                class="w-full py-3.5 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-2xl shadow-md transition flex items-center justify-center gap-2">
          <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span>Lanjut ke Pembayaran (Bayar)</span>
        </button>
      </div>
    </div>
  </div>
</main>

<!-- Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl border border-[#ECEBF4] max-w-lg w-full p-6 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200">
    <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
      <div>
        <h3 class="font-display font-bold text-base text-textMain">Konfirmasi Pembayaran POS</h3>
        <p class="text-xs text-textMuted mt-0.5">Pilih metode pembayaran dan masukkan uang diterima.</p>
      </div>
      <button type="button" onclick="closePaymentModal()" class="text-textMuted hover:text-textMain p-1 rounded-lg">
        ✕
      </button>
    </div>

    <!-- Amount Due Callout -->
    <div class="p-4 rounded-2xl bg-panelBg border border-[#ECEBF4] flex items-center justify-between">
      <span class="text-xs font-bold text-textMuted uppercase tracking-wider">Total Tagihan:</span>
      <span class="text-2xl font-black text-sidebar font-mono" id="modalAmountDue">Rp 0</span>
    </div>

    <!-- Payment Methods -->
    <div class="space-y-1.5 text-xs">
      <label class="block font-bold text-textMain">Pilih Metode Pembayaran</label>
      <div class="grid grid-cols-4 gap-2">
        <button type="button" onclick="setPaymentMethod('CASH')" id="pm-btn-CASH"
                class="pm-btn py-2.5 rounded-xl border border-sidebar bg-sidebar text-white font-bold text-xs transition">
          TUNAI
        </button>
        <button type="button" onclick="setPaymentMethod('QRIS')" id="pm-btn-QRIS"
                class="pm-btn py-2.5 rounded-xl border border-[#ECEBF4] bg-panelBg hover:bg-white text-textMain font-bold text-xs transition">
          QRIS
        </button>
        <button type="button" onclick="setPaymentMethod('TRANSFER')" id="pm-btn-TRANSFER"
                class="pm-btn py-2.5 rounded-xl border border-[#ECEBF4] bg-panelBg hover:bg-white text-textMain font-bold text-xs transition">
          TRANSFER
        </button>
        <button type="button" onclick="setPaymentMethod('DEBIT')" id="pm-btn-DEBIT"
                class="pm-btn py-2.5 rounded-xl border border-[#ECEBF4] bg-panelBg hover:bg-white text-textMain font-bold text-xs transition">
          DEBIT
        </button>
      </div>
    </div>

    <!-- Tender Amount & Quick Cash (for CASH) -->
    <div class="space-y-3" id="cashTenderSection">
      <div>
        <label class="block text-xs font-bold text-textMain mb-1">Uang Diterima dari Pembeli (Rp)</label>
        <input type="number" id="paidAmountInput" step="1" oninput="calculateChange()"
               class="w-full px-3.5 py-3 rounded-xl border border-[#ECEBF4] bg-panelBg text-lg font-mono font-black text-sidebar focus:outline-none focus:border-sidebar focus:bg-white transition text-right">
      </div>

      <!-- Quick Amount Buttons -->
      <div class="flex items-center gap-2 overflow-x-auto text-[11px] font-bold">
        <button type="button" onclick="setQuickPaid('EXACT')" class="px-2.5 py-1.5 rounded-lg bg-panelBg hover:bg-sidebar hover:text-white transition border border-[#ECEBF4]">
          Uang Pas
        </button>
        <button type="button" onclick="setQuickPaid(50000)" class="px-2.5 py-1.5 rounded-lg bg-panelBg hover:bg-sidebar hover:text-white transition border border-[#ECEBF4]">
          Rp 50.000
        </button>
        <button type="button" onclick="setQuickPaid(100000)" class="px-2.5 py-1.5 rounded-lg bg-panelBg hover:bg-sidebar hover:text-white transition border border-[#ECEBF4]">
          Rp 100.000
        </button>
        <button type="button" onclick="setQuickPaid(200000)" class="px-2.5 py-1.5 rounded-lg bg-panelBg hover:bg-sidebar hover:text-white transition border border-[#ECEBF4]">
          Rp 200.000
        </button>
      </div>

      <!-- Change Display -->
      <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs">
        <span class="font-bold text-emerald-900 uppercase">Uang Kembalian:</span>
        <span class="text-lg font-black text-emerald-700 font-mono" id="modalChangeAmount">Rp 0</span>
      </div>
    </div>

    <!-- Submit Processing Button -->
    <button type="button" onclick="submitCheckout()" id="btnSubmitCheckout"
            class="w-full py-3.5 bg-sidebar hover:bg-sidebar/95 text-white font-bold text-xs rounded-2xl shadow-lg transition flex items-center justify-center gap-2">
      <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
      </svg>
      <span>Konfirmasi & Cetak Struk</span>
    </button>
  </div>
</div>

<script>
  // 1. Initial State
  const catalogData = @json($catalog);
  const activeLocationId = {{ $activeLocation->id }};
  let cart = [];
  let currentPaymentMethod = 'CASH';
  let idempotencyKey = crypto.randomUUID();

  // 2. Filter Catalog by Category & Search Text
  function filterCatalog() {
    const q = document.getElementById('posSearchInput').value.toLowerCase().trim();
    const activeCatBtn = document.querySelector('.cat-pill.bg-sidebar');
    const selectedCat = activeCatBtn ? activeCatBtn.id.replace('cat-btn-', '') : 'all';

    document.querySelectorAll('.product-card').forEach(card => {
      const name = card.dataset.name || '';
      const sku = card.dataset.sku || '';
      const cat = card.dataset.cat || '';

      const matchesText = name.includes(q) || sku.includes(q);
      const matchesCat = (selectedCat === 'all' || selectedCat === 'ALL' || cat === selectedCat);

      if (matchesText && matchesCat) {
        card.classList.remove('hidden');
      } else {
        card.classList.add('hidden');
      }
    });
  }

  function selectCategory(catId) {
    document.querySelectorAll('.cat-pill').forEach(btn => {
      btn.className = 'cat-pill px-3 py-1.5 rounded-xl font-bold bg-panelBg hover:bg-[#EAE8F2] text-textMuted transition whitespace-nowrap border border-[#ECEBF4]';
    });
    const activeBtn = document.getElementById(`cat-btn-${catId.toLowerCase()}`);
    if (activeBtn) {
      activeBtn.className = 'cat-pill px-3 py-1.5 rounded-xl font-bold bg-sidebar text-white shadow-sm transition whitespace-nowrap';
    }
    filterCatalog();
  }

  // 3. Cart Management
  function addToCart(item) {
    const baseUom = item.uoms.find(u => u.is_base) || item.uoms[0];
    const existing = cart.find(ci => ci.product_id === item.id && ci.product_uom_id === baseUom.id);

    if (existing) {
      existing.quantity += 1;
    } else {
      cart.push({
        product_id: item.id,
        name: item.name,
        sku: item.sku,
        product_uom_id: baseUom.id,
        uom_name: baseUom.name,
        uom_symbol: baseUom.symbol,
        uoms: item.uoms,
        quantity: 1,
        unit_price: baseUom.calculated_price,
        floor_price: baseUom.calculated_floor,
      });
    }

    renderCart();
  }

  function updateCartQty(idx, delta) {
    if (cart[idx]) {
      cart[idx].quantity += delta;
      if (cart[idx].quantity <= 0) {
        cart.splice(idx, 1);
      }
      renderCart();
    }
  }

  function removeCartItem(idx) {
    cart.splice(idx, 1);
    renderCart();
  }

  function changeCartUom(idx, uomId) {
    const item = cart[idx];
    const chosenUom = item.uoms.find(u => u.id == uomId);
    if (chosenUom) {
      item.product_uom_id = chosenUom.id;
      item.uom_name = chosenUom.name;
      item.uom_symbol = chosenUom.symbol;
      item.unit_price = chosenUom.calculated_price;
      item.floor_price = chosenUom.calculated_floor;
      renderCart();
    }
  }

  function clearCart() {
    cart = [];
    renderCart();
  }

  function renderCart() {
    const container = document.getElementById('cartItemsContainer');
    if (cart.length === 0) {
      container.innerHTML = `
        <div id="cartEmptyState" class="py-10 text-center text-textMuted text-xs">
          Keranjang masih kosong. Klik produk di sebelah kiri atau scan barcode untuk menambah.
        </div>
      `;
      calculateCartTotals();
      return;
    }

    let html = '';
    cart.forEach((item, idx) => {
      const lineSubtotal = Math.round(item.quantity * item.unit_price);
      let uomOptions = '';
      item.uoms.forEach(u => {
        uomOptions += `<option value="${u.id}" ${u.id === item.product_uom_id ? 'selected' : ''}>${u.symbol} (Rp ${Math.round(u.calculated_price).toLocaleString('id-ID')})</option>`;
      });

      html += `
        <div class="p-3 rounded-2xl bg-panelBg/70 border border-[#ECEBF4] space-y-2">
          <div class="flex items-start justify-between gap-2">
            <div>
              <h4 class="font-bold text-xs text-textMain line-clamp-1">${item.name}</h4>
              <span class="text-[10px] text-textMuted font-mono">${item.sku}</span>
            </div>
            <button type="button" onclick="removeCartItem(${idx})" class="text-textMuted hover:text-rose-600 p-0.5 text-xs font-bold" title="Hapus">
              ✕
            </button>
          </div>

          <div class="grid grid-cols-12 gap-2 items-center text-xs">
            <div class="col-span-5">
              <select onchange="changeCartUom(${idx}, this.value)" class="w-full px-2 py-1 bg-white rounded-lg border border-[#ECEBF4] text-[11px] font-semibold text-textMain focus:outline-none">
                ${uomOptions}
              </select>
            </div>

            <div class="col-span-4 flex items-center justify-center gap-1.5 bg-white rounded-lg border border-[#ECEBF4] p-0.5">
              <button type="button" onclick="updateCartQty(${idx}, -1)" class="w-5 h-5 rounded bg-panelBg hover:bg-gray-200 font-bold text-xs flex items-center justify-center text-sidebar">-</button>
              <span class="w-8 text-center font-bold text-xs text-sidebar font-mono">${item.quantity}</span>
              <button type="button" onclick="updateCartQty(${idx}, 1)" class="w-5 h-5 rounded bg-panelBg hover:bg-gray-200 font-bold text-xs flex items-center justify-center text-sidebar">+</button>
            </div>

            <div class="col-span-3 text-right font-black font-mono text-sidebar text-xs">
              Rp ${lineSubtotal.toLocaleString('id-ID')}
            </div>
          </div>
        </div>
      `;
    });

    container.innerHTML = html;
    calculateCartTotals();
  }

  function calculateCartTotals() {
    let subtotal = 0;
    cart.forEach(item => {
      subtotal += Math.round(item.quantity * item.unit_price);
    });

    const discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
    const grandTotal = Math.max(0, subtotal - discount);

    document.getElementById('cartSubtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('cartGrandTotal').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
  }

  // 4. Payment Modal & Execution
  function openPaymentModal() {
    if (cart.length === 0) {
      alert('Keranjang belanja masih kosong! Tambahkan produk terlebih dahulu.');
      return;
    }

    let subtotal = 0;
    cart.forEach(item => subtotal += Math.round(item.quantity * item.unit_price));
    const discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
    const grandTotal = Math.max(0, subtotal - discount);

    document.getElementById('modalAmountDue').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    document.getElementById('paidAmountInput').value = grandTotal;
    calculateChange();

    document.getElementById('paymentModal').classList.remove('hidden');
  }

  function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
  }

  function setPaymentMethod(pm) {
    currentPaymentMethod = pm;
    document.querySelectorAll('.pm-btn').forEach(btn => {
      btn.className = 'pm-btn py-2.5 rounded-xl border border-[#ECEBF4] bg-panelBg hover:bg-white text-textMain font-bold text-xs transition';
    });
    document.getElementById(`pm-btn-${pm}`).className = 'pm-btn py-2.5 rounded-xl border border-sidebar bg-sidebar text-white font-bold text-xs transition';

    const tenderSec = document.getElementById('cashTenderSection');
    if (pm === 'CASH') {
      tenderSec.classList.remove('hidden');
    } else {
      // Non-cash defaults to exact payment
      tenderSec.classList.add('hidden');
      let subtotal = 0;
      cart.forEach(item => subtotal += Math.round(item.quantity * item.unit_price));
      const discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
      document.getElementById('paidAmountInput').value = Math.max(0, subtotal - discount);
      calculateChange();
    }
  }

  function setQuickPaid(val) {
    let subtotal = 0;
    cart.forEach(item => subtotal += Math.round(item.quantity * item.unit_price));
    const discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
    const grandTotal = Math.max(0, subtotal - discount);

    if (val === 'EXACT') {
      document.getElementById('paidAmountInput').value = grandTotal;
    } else {
      document.getElementById('paidAmountInput').value = val;
    }
    calculateChange();
  }

  function calculateChange() {
    let subtotal = 0;
    cart.forEach(item => subtotal += Math.round(item.quantity * item.unit_price));
    const discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
    const grandTotal = Math.max(0, subtotal - discount);

    const paid = parseFloat(document.getElementById('paidAmountInput').value) || 0;
    const change = Math.max(0, paid - grandTotal);
    document.getElementById('modalChangeAmount').textContent = 'Rp ' + Math.round(change).toLocaleString('id-ID');
  }

  async function submitCheckout() {
    const btn = document.getElementById('btnSubmitCheckout');
    btn.disabled = true;
    btn.innerHTML = 'Memproses Transaksi di Server...';

    const discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
    const paidAmount = parseFloat(document.getElementById('paidAmountInput').value) || 0;
    const customerName = document.getElementById('customer_name_input').value || 'Pelanggan Umum';
    const memberId = document.getElementById('member_id_select').value || null;

    const payload = {
      location_id: activeLocationId,
      payment_method: currentPaymentMethod,
      paid_amount: paidAmount,
      discount_amount: discount,
      customer_name: customerName,
      member_id: memberId,
      idempotency_key: idempotencyKey,
      items: cart.map(item => ({
        product_id: item.product_id,
        product_uom_id: item.product_uom_id,
        quantity: item.quantity,
        unit_price: item.unit_price,
      }))
    };

    try {
      const response = await fetch('{{ route("store.pos.checkout") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
      });

      const data = await response.json();

      if (response.ok && data.success) {
        // Successful checkout! Redirect to printable thermal receipt
        window.location.href = data.receipt_url;
      } else {
        alert(data.message || 'Gagal memproses transaksi kasir.');
        btn.disabled = false;
        btn.innerHTML = 'Konfirmasi & Cetak Struk';
      }
    } catch (err) {
      alert('Terjadi kesalahan jaringan: ' + err.message);
      btn.disabled = false;
      btn.innerHTML = 'Konfirmasi & Cetak Struk';
    }
  }
</script>
@endsection

@section('right_panel')
  @include('partials.right-panel')
@endsection
