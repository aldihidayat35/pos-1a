<!-- ==========================================================
     CHANGE PASSWORD MODAL (AJAX DRIVEN)
     Enables current logged-in user to securely update their password
     ========================================================== -->
<div id="changePasswordModal" class="fixed inset-0 z-50 bg-sidebarDark/70 backdrop-blur-xs hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-[#ECEBF4] transform transition-all duration-200 scale-95 opacity-0" id="changePasswordModalBox">
    
    <!-- Modal Header -->
    <div class="px-6 py-4 bg-gradient-to-r from-[#F8F7FC] to-[#F1EFF9] border-b border-[#ECEBF4] flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-sidebar text-white flex items-center justify-center shadow-xs">
          <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
          </svg>
        </div>
        <div>
          <h3 class="font-bold text-sm sm:text-base text-sidebar leading-tight">Ubah Kata Sandi</h3>
          <p class="text-[11px] text-textMuted">Akun: <strong class="text-textMain">{{ auth()->user()?->name }}</strong></p>
        </div>
      </div>
      <button type="button" onclick="closeChangePasswordModal()" class="w-8 h-8 rounded-xl bg-white border border-[#E6E4F0] text-textMuted hover:text-rose-600 hover:bg-rose-50 transition flex items-center justify-center font-bold">
        ✕
      </button>
    </div>

    <!-- Modal Form -->
    <form id="changePasswordForm" onsubmit="submitChangePassword(event)" class="p-6 space-y-4">
      @csrf

      <!-- Error Alert Box (Hidden by default) -->
      <div id="changePasswordErrorBox" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
        <ul id="changePasswordErrorList" class="list-disc list-inside space-y-1"></ul>
      </div>

      <!-- Current Password -->
      <div>
        <label for="cp_current_password" class="block text-xs font-bold text-textMain mb-1.5">
          Kata Sandi Saat Ini <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
          <input type="password" 
                 id="cp_current_password" 
                 name="current_password" 
                 required 
                 autocomplete="current-password"
                 placeholder="Ketik kata sandi lama Anda"
                 class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6E4F0] bg-panelBg text-xs font-medium text-textMain focus:outline-none focus:border-sidebar focus:bg-white transition pr-10">
          <button type="button" onclick="togglePasswordVisibility('cp_current_password', this)" class="absolute right-3 top-2.5 text-textMuted hover:text-textMain text-xs font-medium">
            👁️
          </button>
        </div>
      </div>

      <!-- New Password -->
      <div>
        <label for="cp_password" class="block text-xs font-bold text-textMain mb-1.5">
          Kata Sandi Baru <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
          <input type="password" 
                 id="cp_password" 
                 name="password" 
                 required 
                 minlength="6"
                 autocomplete="new-password"
                 placeholder="Minimal 6 karakter"
                 class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6E4F0] bg-panelBg text-xs font-medium text-textMain focus:outline-none focus:border-sidebar focus:bg-white transition pr-10">
          <button type="button" onclick="togglePasswordVisibility('cp_password', this)" class="absolute right-3 top-2.5 text-textMuted hover:text-textMain text-xs font-medium">
            👁️
          </button>
        </div>
        <p class="text-[10px] text-textMuted mt-1">Gunakan kombinasi huruf dan angka untuk keamanan optimal.</p>
      </div>

      <!-- Password Confirmation -->
      <div>
        <label for="cp_password_confirmation" class="block text-xs font-bold text-textMain mb-1.5">
          Ulangi Kata Sandi Baru <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
          <input type="password" 
                 id="cp_password_confirmation" 
                 name="password_confirmation" 
                 required 
                 minlength="6"
                 autocomplete="new-password"
                 placeholder="Ketik ulang kata sandi baru"
                 class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6E4F0] bg-panelBg text-xs font-medium text-textMain focus:outline-none focus:border-sidebar focus:bg-white transition pr-10">
          <button type="button" onclick="togglePasswordVisibility('cp_password_confirmation', this)" class="absolute right-3 top-2.5 text-textMuted hover:text-textMain text-xs font-medium">
            👁️
          </button>
        </div>
      </div>

      <!-- Modal Actions -->
      <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-[#ECEBF4]">
        <button type="button" 
                onclick="closeChangePasswordModal()"
                class="px-4 py-2.5 rounded-xl border border-[#E6E4F0] bg-white hover:bg-panelBg text-xs font-bold text-textMuted hover:text-textMain transition">
          Batal
        </button>
        <button type="submit" 
                id="cpSubmitBtn"
                class="px-5 py-2.5 rounded-xl bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold transition shadow-sm flex items-center gap-2">
          <span>Simpan Kata Sandi</span>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function openChangePasswordModal() {
    // Close user dropdown first if open
    closeUserProfileDropdown();

    const modal = document.getElementById('changePasswordModal');
    const box = document.getElementById('changePasswordModalBox');
    const form = document.getElementById('changePasswordForm');
    const errBox = document.getElementById('changePasswordErrorBox');
    
    if (form) form.reset();
    if (errBox) errBox.classList.add('hidden');
    
    if (modal && box) {
      modal.classList.remove('hidden');
      setTimeout(() => {
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
      }, 10);
      document.body.style.overflow = 'hidden';
      setTimeout(() => {
        document.getElementById('cp_current_password')?.focus();
      }, 50);
    }
  }

  function closeChangePasswordModal() {
    const modal = document.getElementById('changePasswordModal');
    const box = document.getElementById('changePasswordModalBox');
    if (modal && box) {
      box.classList.remove('scale-100', 'opacity-100');
      box.classList.add('scale-95', 'opacity-0');
      setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
      }, 150);
    }
  }

  function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    if (input.type === 'password') {
      input.type = 'text';
      btn.innerText = '🙈';
    } else {
      input.type = 'password';
      btn.innerText = '👁️';
    }
  }

  async function submitChangePassword(event) {
    event.preventDefault();
    const btn = document.getElementById('cpSubmitBtn');
    const errBox = document.getElementById('changePasswordErrorBox');
    const errList = document.getElementById('changePasswordErrorList');
    
    errBox.classList.add('hidden');
    errList.innerHTML = '';

    const currentPassword = document.getElementById('cp_current_password').value;
    const password = document.getElementById('cp_password').value;
    const passwordConfirmation = document.getElementById('cp_password_confirmation').value;

    if (password !== passwordConfirmation) {
      errBox.classList.remove('hidden');
      errList.innerHTML = '<li>Konfirmasi kata sandi baru tidak cocok.</li>';
      return;
    }

    const originalBtnText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span>Menyimpan...</span>';

    try {
      const response = await fetch("{{ route('profile.password') }}", {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          current_password: currentPassword,
          password: password,
          password_confirmation: passwordConfirmation
        })
      });

      const data = await response.json();

      if (response.ok && data.success) {
        closeChangePasswordModal();
        if (typeof SwalToast !== 'undefined') {
          SwalToast.fire({ icon: 'success', title: data.message || 'Kata sandi berhasil diperbarui!' });
        } else if (typeof openToast === 'function') {
          openToast(data.message || 'Kata sandi berhasil diperbarui!', 'success');
        } else {
          alert(data.message || 'Kata sandi berhasil diperbarui!');
        }
      } else {
        errBox.classList.remove('hidden');
        if (data.errors) {
          errList.innerHTML = Object.values(data.errors).map(errArr => `<li>${errArr[0]}</li>`).join('');
        } else {
          errList.innerHTML = `<li>${data.message || 'Gagal mengubah kata sandi. Silakan periksa kembali.'}</li>`;
        }
      }
    } catch (err) {
      errBox.classList.remove('hidden');
      errList.innerHTML = '<li>Terjadi gangguan koneksi server. Silakan coba kembali.</li>';
    } finally {
      btn.disabled = false;
      btn.innerHTML = originalBtnText;
    }
  }

  // Keyboard shortcut listener for ESC key
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const modal = document.getElementById('changePasswordModal');
      if (modal && !modal.classList.contains('hidden')) {
        closeChangePasswordModal();
      }
    }
  });
</script>
