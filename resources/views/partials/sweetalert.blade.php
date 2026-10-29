<!-- ============================================================
     PasarHub SweetAlert2 Design System & Notification Engine
     - Unified Toasts (Top-End Auto Dismiss) for Success & Info
     - Central Modal Dialogs for Error & Warning & Form Validation
     - Unified Interactive Confirmation Modals with Danger Accents
     - Automatic Form Confirmation Interception (data-confirm & onsubmit)
     - window.alert & openToast Bridging to SweetAlert2
     ============================================================ -->
<style>
  /* SweetAlert2 PasarHub Theme Overrides */
  .swal2-container {
    font-family: 'Poppins', sans-serif !important;
    z-index: 99999 !important;
  }
  .swal2-popup.swal2-toast {
    box-shadow: 0 10px 25px -5px rgba(52, 42, 110, 0.12), 0 8px 10px -6px rgba(52, 42, 110, 0.08) !important;
    border: 1px solid #ECEBF4 !important;
    border-radius: 1.25rem !important; /* 20px */
    padding: 0.875rem 1.25rem !important;
    background: #FFFFFF !important;
  }
  .swal2-popup.swal2-toast .swal2-title {
    font-family: 'Poppins', sans-serif !important;
    font-size: 0.8125rem !important; /* 13px */
    font-weight: 600 !important;
    color: #382E6B !important;
    margin: 0 !important;
    line-height: 1.4 !important;
  }
  .swal2-popup.swal2-toast .swal2-icon {
    margin: 0 0.75rem 0 0 !important;
    transform: scale(0.85);
  }
  .swal2-popup.swal2-modal {
    border-radius: 1.75rem !important; /* 28px */
    border: 1px solid #ECEBF4 !important;
    box-shadow: 0 25px 50px -12px rgba(52, 42, 110, 0.25) !important;
    padding: 1.75rem 1.5rem !important;
    background: #FFFFFF !important;
  }
  .swal2-popup.swal2-modal .swal2-title {
    font-family: 'Poppins', sans-serif !important;
    font-size: 1.125rem !important; /* 18px */
    font-weight: 700 !important;
    color: #382E6B !important;
    padding-top: 0.5rem !important;
  }
  .swal2-popup.swal2-modal .swal2-html-container {
    font-family: 'Poppins', sans-serif !important;
    font-size: 0.8125rem !important; /* 13px */
    color: #9490AD !important;
    margin-top: 0.75rem !important;
    line-height: 1.6 !important;
  }
  .swal2-timer-progress-bar {
    background: #F29F3D !important; /* PasarHub Brand Orange */
    height: 3px !important;
  }
</style>

<script data-global-scripts="true">
(function() {
  'use strict';

  if (typeof Swal === 'undefined') {
    console.warn('SweetAlert2 library not loaded yet.');
    return;
  }

  // 1. SweetAlert Toast Instance (Top-Right, 3.5s auto dismiss)
  window.SwalToast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
  });

  // 2. SweetAlert Central Modal Instance (Errors, Warnings, Notices)
  window.SwalModal = Swal.mixin({
    buttonsStyling: false,
    confirmButtonText: 'Mengerti',
    customClass: {
      popup: 'swal2-modal',
      confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-xs shadow-sm transition active:scale-95 text-white bg-sidebar hover:bg-sidebarDark cursor-pointer'
    }
  });

  // 3. SweetAlert Confirmation Modal Instance
  window.SwalConfirm = Swal.mixin({
    buttonsStyling: false,
    showCancelButton: true,
    reverseButtons: true,
    focusCancel: true,
    cancelButtonText: 'Batal',
    confirmButtonText: 'Ya, Lanjutkan',
    customClass: {
      popup: 'swal2-modal',
      title: 'font-display text-base font-bold text-textMain',
      confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-xs shadow-sm transition active:scale-95 text-white cursor-pointer',
      cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-xs bg-panelBg hover:bg-[#EAE8F2] text-textMain transition active:scale-95 mr-2 cursor-pointer'
    }
  });

  // 4. Global Utility Functions
  window.showToast = function(msg, icon = 'success') {
    window.SwalToast.fire({
      icon: icon,
      title: msg
    });
  };

  window.showModal = function(title, textOrHtml, icon = 'info') {
    window.SwalModal.fire({
      icon: icon,
      title: title,
      html: textOrHtml
    });
  };

  // 5. Override native window.alert to render beautiful SweetAlert2 modals
  window.alert = function(message) {
    window.SwalModal.fire({
      icon: 'info',
      title: 'Pemberitahuan',
      text: typeof message === 'string' ? message : JSON.stringify(message),
      confirmButtonText: 'OK'
    });
  };

  // 6. Bridge openToast to SweetAlert2 Toast
  window.openToast = function(msg, icon = 'info') {
    window.SwalToast.fire({
      icon: icon,
      title: msg
    });
  };

  // 7. Automatic Form & Action Confirmation Interceptor
  function initConfirmInterceptors() {
    // Intercept forms with legacy onsubmit="return confirm('...')"
    document.querySelectorAll('form').forEach(form => {
      const onsubmitAttr = form.getAttribute('onsubmit');
      if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
        const match = onsubmitAttr.match(/confirm\((['"`])([\s\S]*?)\1\)/);
        const confirmMsg = match ? match[2] : 'Apakah Anda yakin ingin memproses tindakan ini?';
        form.removeAttribute('onsubmit');
        form.setAttribute('data-confirm', confirmMsg);
      }
    });

    // Intercept clickable elements with onclick="return confirm('...')"
    document.querySelectorAll('[onclick*="confirm("]').forEach(el => {
      const onclickAttr = el.getAttribute('onclick');
      if (onclickAttr && onclickAttr.includes('confirm(')) {
        const match = onclickAttr.match(/confirm\((['"`])([\s\S]*?)\1\)/);
        const confirmMsg = match ? match[2] : 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        el.removeAttribute('onclick');
        el.setAttribute('data-confirm', confirmMsg);
      }
    });
  }

  // Delegated Submit Event Listener for Forms
  document.addEventListener('submit', function(e) {
    const form = e.target;
    if (!form || !form.hasAttribute('data-confirm')) return;

    if (form.dataset.confirmed === 'true') {
      delete form.dataset.confirmed;
      return; // Allow submission to proceed
    }

    e.preventDefault();
    e.stopPropagation();

    const msg = form.getAttribute('data-confirm') || 'Apakah Anda yakin ingin memproses tindakan ini?';
    const methodInput = form.querySelector('input[name="_method"]');
    const method = (methodInput ? methodInput.value : form.method || 'POST').toUpperCase();
    const isDestructive = method === 'DELETE' || /hapus|delete|nonaktifkan|nonaktif|void|batal/i.test(msg);

    const title = form.getAttribute('data-confirm-title') || (isDestructive ? 'Konfirmasi Tindakan' : 'Konfirmasi');
    const confirmBtnText = form.getAttribute('data-confirm-btn') || (isDestructive ? 'Ya, Lanjutkan' : 'Ya, Setuju');
    const btnColorClass = isDestructive ? 'bg-rose-600 hover:bg-rose-700' : 'bg-sidebar hover:bg-sidebarDark';

    window.SwalConfirm.fire({
      title: title,
      text: msg,
      icon: isDestructive ? 'warning' : 'question',
      confirmButtonText: confirmBtnText,
      cancelButtonText: 'Batal',
      customClass: {
        popup: 'swal2-modal',
        title: 'font-display text-base font-bold text-textMain',
        confirmButton: `px-5 py-2.5 rounded-xl font-bold text-xs shadow-sm transition active:scale-95 text-white ${btnColorClass} cursor-pointer`,
        cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-xs bg-panelBg hover:bg-[#EAE8F2] text-textMain transition active:scale-95 mr-2 cursor-pointer'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        form.dataset.confirmed = 'true';
        form.submit();
      }
    });
  });

  // Delegated Click Event Listener for Standalone Buttons/Links with data-confirm
  document.addEventListener('click', function(e) {
    const trigger = e.target.closest('[data-confirm]:not(form)');
    if (!trigger) return;

    // If it's a submit button inside a form, let the form's submit listener handle it
    if (trigger.type === 'submit' && trigger.form) return;

    if (trigger.dataset.confirmed === 'true') {
      delete trigger.dataset.confirmed;
      return;
    }

    e.preventDefault();
    e.stopPropagation();

    const msg = trigger.getAttribute('data-confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
    const isDestructive = /hapus|delete|nonaktifkan|nonaktif|void|batal|keluar/i.test(msg);
    const title = trigger.getAttribute('data-confirm-title') || (isDestructive ? 'Konfirmasi Tindakan' : 'Konfirmasi');
    const confirmBtnText = trigger.getAttribute('data-confirm-btn') || (isDestructive ? 'Ya, Lanjutkan' : 'Ya, Setuju');
    const btnColorClass = isDestructive ? 'bg-rose-600 hover:bg-rose-700' : 'bg-sidebar hover:bg-sidebarDark';

    window.SwalConfirm.fire({
      title: title,
      text: msg,
      icon: isDestructive ? 'warning' : 'question',
      confirmButtonText: confirmBtnText,
      cancelButtonText: 'Batal',
      customClass: {
        popup: 'swal2-modal',
        title: 'font-display text-base font-bold text-textMain',
        confirmButton: `px-5 py-2.5 rounded-xl font-bold text-xs shadow-sm transition active:scale-95 text-white ${btnColorClass} cursor-pointer`,
        cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-xs bg-panelBg hover:bg-[#EAE8F2] text-textMain transition active:scale-95 mr-2 cursor-pointer'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        trigger.dataset.confirmed = 'true';
        if (trigger.tagName === 'A' && trigger.href) {
          window.location.href = trigger.href;
        } else {
          trigger.click();
        }
      }
    });
  });

  // Initialize on DOM ready and on dynamic events
  document.addEventListener('DOMContentLoaded', initConfirmInterceptors);
  window.addEventListener('pasar:page-loaded', initConfirmInterceptors);
  window.addEventListener('pasar:table-updated', initConfirmInterceptors);

  // 8. Auto-dispatch Laravel Flash Session Notifications on Page Load
  document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
      window.SwalToast.fire({
        icon: 'success',
        title: {!! json_encode(session('success')) !!}
      });
    @endif

    @if(session('info'))
      window.SwalToast.fire({
        icon: 'info',
        title: {!! json_encode(session('info')) !!}
      });
    @endif

    @if(session('error'))
      window.SwalModal.fire({
        icon: 'error',
        title: 'Terjadi Kesalahan',
        text: {!! json_encode(session('error')) !!},
        confirmButtonText: 'Tutup'
      });
    @endif

    @if(session('warning'))
      window.SwalModal.fire({
        icon: 'warning',
        title: 'Peringatan Sistem',
        text: {!! json_encode(session('warning')) !!},
        confirmButtonText: 'Mengerti'
      });
    @endif

    @if(isset($errors) && $errors->any())
      window.SwalModal.fire({
        icon: 'error',
        title: 'Validasi Gagal',
        html: `
          <div class="text-left mt-1">
            <p class="font-bold text-rose-600 mb-2 text-xs">Terdapat beberapa kesalahan input:</p>
            <ul class="list-disc pl-5 space-y-1 text-xs text-textMain">
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        `,
        confirmButtonText: 'Periksa Kembali'
      });
    @endif
  });

})();
</script>
