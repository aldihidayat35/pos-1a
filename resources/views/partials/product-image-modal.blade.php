<!-- GLOBAL PRODUCT IMAGE LIGHTBOX MODAL -->
<div id="productImageModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 sm:p-6 transition-all duration-300 opacity-0 pointer-events-none" role="dialog" aria-modal="true" aria-labelledby="productModalTitle">
  <!-- Backdrop with blur -->
  <div id="productImageModalBackdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeProductImageModal()"></div>

  <!-- Modal Dialog Card -->
  <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden z-10 transform scale-95 transition-all duration-300 flex flex-col max-h-[90vh]">
    <!-- Modal Header -->
    <div class="px-6 py-4 border-b border-slate-100 flex items-start justify-between gap-4 bg-slate-50/60">
      <div class="min-w-0 flex-1">
        <div class="flex items-center gap-2 mb-1 flex-wrap">
          <span id="productModalCategory" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brandLavender text-sidebar">
            Kategori
          </span>
          <span id="productModalSku" class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-medium bg-slate-100 text-slate-700 border border-slate-200">
            SKU
          </span>
        </div>
        <h3 id="productModalTitle" class="text-base sm:text-lg font-bold text-slate-900 truncate">
          Nama Produk
        </h3>
      </div>
      <!-- Close Button -->
      <button type="button" onclick="closeProductImageModal()" class="w-9 h-9 rounded-full bg-white hover:bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors shadow-xs shrink-0" title="Tutup (ESC)">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Modal Body: Image Preview -->
    <div class="p-6 flex-1 flex flex-col items-center justify-center bg-slate-50/30 overflow-hidden min-h-[280px]">
      <div class="relative w-full max-h-[60vh] flex items-center justify-center rounded-2xl overflow-hidden bg-white border border-slate-100 shadow-inner p-2">
        <!-- Actual Image Element -->
        <img id="productModalImage" src="" alt="Foto Produk" class="max-h-[50vh] max-w-full object-contain rounded-xl transition-transform duration-200 hover:scale-102" />
        
        <!-- Elegant Fallback Placeholder when image is null -->
        <div id="productModalPlaceholder" class="hidden flex-col items-center justify-center py-12 px-6 text-center">
          <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-brandLavender via-cream/40 to-brandCyan/20 flex items-center justify-center text-sidebar mb-3 shadow-inner">
            <svg class="w-10 h-10 text-sidebar/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
          <span class="text-sm font-semibold text-slate-800">Foto Belum Tersedia</span>
          <p class="text-xs text-slate-500 mt-1 max-w-xs">Produk ini belum memiliki foto yang diunggah. Anda dapat mengunggah foto melalui menu Edit Produk.</p>
        </div>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="px-6 py-3.5 border-t border-slate-100 bg-white flex items-center justify-between gap-3 text-xs text-slate-500">
      <span class="flex items-center gap-1.5 text-slate-400">
        <kbd class="px-1.5 py-0.5 bg-slate-100 border border-slate-200 rounded text-[10px] font-mono text-slate-600">ESC</kbd> untuk menutup
      </span>
      <div class="flex items-center gap-2">
        <a id="productModalEditBtn" href="#" class="hidden px-3 py-1.5 rounded-xl font-medium text-xs bg-sidebar text-white hover:bg-sidebarDark transition-colors shadow-xs">
          Edit Produk
        </a>
        <button type="button" onclick="closeProductImageModal()" class="px-3.5 py-1.5 rounded-xl font-medium text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
          Tutup
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  window.openProductImageModal = function(data) {
    const modal = document.getElementById('productImageModal');
    const backdrop = document.getElementById('productImageModalBackdrop');
    const title = document.getElementById('productModalTitle');
    const sku = document.getElementById('productModalSku');
    const category = document.getElementById('productModalCategory');
    const img = document.getElementById('productModalImage');
    const placeholder = document.getElementById('productModalPlaceholder');
    const editBtn = document.getElementById('productModalEditBtn');

    if (!modal) return;

    title.textContent = data.name || 'Detail Produk';
    sku.textContent = data.sku ? `SKU: ${data.sku}` : '-';
    
    if (data.category) {
      category.textContent = data.category;
      category.classList.remove('hidden');
    } else {
      category.classList.add('hidden');
    }

    if (data.imageUrl) {
      img.src = data.imageUrl;
      img.alt = data.name || 'Foto Produk';
      img.classList.remove('hidden');
      placeholder.classList.add('hidden');
    } else {
      img.src = '';
      img.classList.add('hidden');
      placeholder.classList.remove('hidden');
    }

    if (data.editUrl) {
      editBtn.href = data.editUrl;
      editBtn.classList.remove('hidden');
    } else {
      editBtn.classList.add('hidden');
    }

    // Show modal with animation
    modal.classList.remove('hidden');
    modal.classList.remove('pointer-events-none');
    document.body.classList.add('overflow-hidden');

    requestAnimationFrame(() => {
      modal.classList.remove('opacity-0');
      backdrop.classList.remove('opacity-0');
      const card = modal.querySelector('.relative.w-full');
      if (card) {
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
      }
    });
  };

  window.closeProductImageModal = function() {
    const modal = document.getElementById('productImageModal');
    const backdrop = document.getElementById('productImageModalBackdrop');
    if (!modal) return;

    modal.classList.add('opacity-0');
    backdrop.classList.add('opacity-0');
    const card = modal.querySelector('.relative.w-full');
    if (card) {
      card.classList.remove('scale-100');
      card.classList.add('scale-95');
    }

    setTimeout(() => {
      modal.classList.add('hidden');
      modal.classList.add('pointer-events-none');
      document.body.classList.remove('overflow-hidden');
    }, 200);
  };

  // Keyboard shortcut ESC
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const modal = document.getElementById('productImageModal');
      if (modal && !modal.classList.contains('hidden')) {
        closeProductImageModal();
      }
    }
  });
</script>
