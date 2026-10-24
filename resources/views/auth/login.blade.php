<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Masuk — PasarHub Sistem Operasional Pasar Terpadu</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Google Fonts: Poppins -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600&display=swap" rel="stylesheet">

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Poppins"', 'sans-serif'],
            poppins: ['"Poppins"', 'sans-serif'],
          },
          colors: {
            sidebar: '#342A6E',
            sidebarDark: '#292059',
            brandOrange: '#F29F3D',
            brandCoral: '#E86B74',
            brandCyan: '#7AC7D9',
            brandLavender: '#E9E8F5',
            panelBg: '#F6F6FA',
            textMain: '#382E6B',
            textMuted: '#9490AD',
            borderSubtle: '#ECEBF4',
            borderDarker: '#E6E4F0',
          }
        }
      }
    }
  </script>

  <style>
    * {
      font-family: 'Poppins', sans-serif;
    }
    body {
      background: #F6F6FA;
      color: #382E6B;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">

  <div class="w-full max-w-md sm:max-w-lg bg-white rounded-[28px] border border-[#ECEBF4] shadow-sm p-6 sm:p-9 space-y-6">
    
    <!-- Brand Header -->
    <div class="text-center space-y-2">
      <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-sidebar text-white shadow-sm mb-2">
        <svg class="w-8 h-8 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
      </div>
      <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-sidebar tracking-tight">
        Pasar<strong class="text-brandOrange font-extrabold">Hub</strong>
      </h1>
      <p class="text-xs sm:text-sm text-textMuted font-medium">
        Sistem Manajemen Operasional Pasar Terpadu (Gudang, Toko, & B2B)
      </p>
    </div>

    <!-- Alert / Validation Feedback -->
    @if ($errors->any())
      <div class="p-4 rounded-2xl bg-[#FEF2F2] border border-[#FECACA] text-[#991B1B] text-xs font-semibold flex items-start gap-2.5">
        <svg class="w-5 h-5 shrink-0 text-[#EF4444]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="9"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
        </svg>
        <div class="space-y-0.5">
          @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
          @endforeach
        </div>
      </div>
    @endif

    @if (session('info'))
      <div class="p-4 rounded-2xl bg-[#EFF6FF] border border-[#BFDBFE] text-[#1E40AF] text-xs font-semibold flex items-center gap-2.5">
        <svg class="w-4 h-4 shrink-0 text-[#3B82F6]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="9"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
        </svg>
        <span>{{ session('info') }}</span>
      </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
      @csrf

      <!-- Username or Email Field -->
      <div class="space-y-1.5">
        <label for="loginInput" class="block text-xs font-bold uppercase tracking-wider text-textMain">
          Email atau Username
        </label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-textMuted">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <input
            id="loginInput"
            name="login"
            type="text"
            required
            autofocus
            value="{{ old('login') }}"
            placeholder="admin@pasarhub.id atau superadmin"
            class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#E6E4F0] bg-[#FDFDFC] focus:bg-white focus:outline-none focus:border-sidebar text-xs sm:text-sm font-medium transition"
          />
        </div>
      </div>

      <!-- Password Field -->
      <div class="space-y-1.5">
        <div class="flex items-center justify-between">
          <label for="passwordInput" class="block text-xs font-bold uppercase tracking-wider text-textMain">
            Kata Sandi
          </label>
          <span class="text-[11px] text-textMuted font-medium">Demo: password</span>
        </div>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-textMuted">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <rect x="5" y="11" width="14" height="10" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0v4"/>
            </svg>
          </div>
          <input
            id="passwordInput"
            name="password"
            type="password"
            required
            placeholder="••••••••"
            class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#E6E4F0] bg-[#FDFDFC] focus:bg-white focus:outline-none focus:border-sidebar text-xs sm:text-sm font-medium transition"
          />
        </div>
      </div>

      <!-- Remember & Locale Hint -->
      <div class="flex items-center justify-between pt-1 text-xs">
        <label class="inline-flex items-center gap-2 cursor-pointer select-none text-textMuted hover:text-textMain">
          <input type="checkbox" name="remember" class="w-4 h-4 rounded text-sidebar border-[#E6E4F0] focus:ring-sidebar" />
          <span>Ingat Sesi Masuk</span>
        </label>
        <span class="text-textMuted">Sesi Terenkripsi MySQL</span>
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        class="w-full min-h-[48px] py-3 px-5 rounded-2xl bg-sidebar hover:bg-sidebarDark active:scale-95 text-white text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm"
      >
        <span>Masuk ke Sistem</span>
        <svg class="w-4 h-4 text-brandOrange" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
      </button>
    </form>

    <!-- 8 Role Quick Demo Switcher (For Development & Testing Testing Convenience) -->
    <div class="pt-4 border-t border-[#F0EFF6] space-y-3">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold uppercase tracking-wider text-textMuted">Pilih Role Uji Demo (1-Click Fill):</span>
        <span class="text-[10px] font-semibold text-brandOrange bg-brandOrange/10 px-2 py-0.5 rounded-full">Dev Mode</span>
      </div>
      
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[11px] font-semibold">
        <!-- 1. Super Admin -->
        <button
          type="button"
          onclick="fillCredentials('superadmin', 'password')"
          class="p-2 rounded-xl border border-[#ECEBF4] hover:border-sidebar hover:bg-[#F0EEF8] text-sidebar transition text-left truncate"
          title="Super Administrator (Akses Penuh)"
        >
          <div class="text-[10px] text-textMuted">Admin</div>
          <div class="font-bold truncate">Super Admin</div>
        </button>

        <!-- 2. Owner -->
        <button
          type="button"
          onclick="fillCredentials('owner', 'password')"
          class="p-2 rounded-xl border border-[#ECEBF4] hover:border-sidebar hover:bg-[#F0EEF8] text-sidebar transition text-left truncate"
          title="Owner (Read-Only Finansial & Anomali)"
        >
          <div class="text-[10px] text-textMuted">Read-Only</div>
          <div class="font-bold truncate">Owner (Barbara)</div>
        </button>

        <!-- 3. Kepala Toko -->
        <button
          type="button"
          onclick="fillCredentials('kepalatoko', 'password')"
          class="p-2 rounded-xl border border-[#ECEBF4] hover:border-sidebar hover:bg-[#F0EEF8] text-sidebar transition text-left truncate"
          title="Kepala Toko (POS, Emergency, Opname Toko)"
        >
          <div class="text-[10px] text-textMuted">Toko</div>
          <div class="font-bold truncate">Kepala Toko</div>
        </button>

        <!-- 4. Kepala Gudang -->
        <button
          type="button"
          onclick="fillCredentials('kepalagudang', 'password')"
          class="p-2 rounded-xl border border-[#ECEBF4] hover:border-sidebar hover:bg-[#F0EEF8] text-sidebar transition text-left truncate"
          title="Kepala Gudang (PO, Transfer, Opname Gudang)"
        >
          <div class="text-[10px] text-textMuted">Gudang</div>
          <div class="font-bold truncate">Kepala Gudang</div>
        </button>

        <!-- 5. Staf Toko -->
        <button
          type="button"
          onclick="fillCredentials('staftoko', 'password')"
          class="p-2 rounded-xl border border-[#ECEBF4] hover:border-sidebar hover:bg-[#F0EEF8] text-sidebar transition text-left truncate"
          title="Staf Toko (Kasir POS & Blind Count Toko)"
        >
          <div class="text-[10px] text-textMuted">Kasir</div>
          <div class="font-bold truncate">Staf Toko</div>
        </button>

        <!-- 6. Staf Gudang -->
        <button
          type="button"
          onclick="fillCredentials('stafgudang', 'password')"
          class="p-2 rounded-xl border border-[#ECEBF4] hover:border-sidebar hover:bg-[#F0EEF8] text-sidebar transition text-left truncate"
          title="Staf Gudang (Penerimaan Fisik & Blind Count)"
        >
          <div class="text-[10px] text-textMuted">Inbound</div>
          <div class="font-bold truncate">Staf Gudang</div>
        </button>

        <!-- 7. Sales -->
        <button
          type="button"
          onclick="fillCredentials('sales', 'password')"
          class="p-2 rounded-xl border border-[#ECEBF4] hover:border-sidebar hover:bg-[#F0EEF8] text-sidebar transition text-left truncate"
          title="Sales (Pesanan B2B & Info Stok)"
        >
          <div class="text-[10px] text-textMuted">B2B</div>
          <div class="font-bold truncate">Sales Tim</div>
        </button>

        <!-- 8. B2B Mitra -->
        <button
          type="button"
          onclick="fillCredentials('mitrab2b', 'password')"
          class="p-2 rounded-xl border border-[#ECEBF4] hover:border-sidebar hover:bg-[#F0EEF8] text-sidebar transition text-left truncate"
          title="Mitra B2B (Portal Mandiri & Tiket QR)"
        >
          <div class="text-[10px] text-textMuted">Mitra</div>
          <div class="font-bold truncate">Mitra B2B</div>
        </button>
      </div>
    </div>

  </div>

  <script>
    function fillCredentials(username, password) {
      const loginInput = document.getElementById('loginInput');
      const passwordInput = document.getElementById('passwordInput');
      if (loginInput && passwordInput) {
        loginInput.value = username;
        passwordInput.value = password;
        loginInput.focus();
      }
    }
  </script>

</body>
</html>
