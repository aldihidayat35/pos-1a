<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'WaMac — Smart Warehouse Management Dashboard')</title>

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
            display: ['"Poppins"', 'sans-serif'],
            poppins: ['"Poppins"', 'sans-serif'],
          },
          colors: {
            cream: '#FAD9B8',
            sidebar: '#342A6E',
            sidebarDark: '#292059',
            brandOrange: '#F29F3D',
            brandCoral: '#E86B74',
            brandCyan: '#7AC7D9',
            brandLavender: '#E9E8F5',
            panelBg: '#F6F6FA',
            textMain: '#382E6B',
            textMuted: '#9490AD',
            // PRD Movement Color Codes
            moveGreen: '#10B981',
            moveGreenBg: '#ECFDF5',
            moveGreenText: '#065F46',
            moveBlue: '#3B82F6',
            moveBlueBg: '#EFF6FF',
            moveBlueText: '#1E40AF',
            movePurple: '#8B5CF6',
            movePurpleBg: '#F5F3FF',
            movePurpleText: '#5B21B6',
            moveOrange: '#F59E0B',
            moveOrangeBg: '#FFFBEB',
            moveOrangeText: '#92400E',
            moveRed: '#EF4444',
            moveRedBg: '#FEF2F2',
            moveRedText: '#991B1B'
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
      background: #FFFFFF;
      color: #382E6B;
      overflow: hidden;
      font-family: 'Poppins', sans-serif;
    }

    /* ============================================================
       LIQUID CURVE SIDEBAR INDICATOR (SEAMLESS MERGE TO WHITE CANVAS)
       ============================================================ */
    .sidebar-container {
      background: #342A6E;
      position: relative;
      z-index: 30;
    }

    /* The sliding white notch that connects seamlessly to the white dashboard canvas */
    .nav-indicator {
      position: absolute;
      right: 0;
      width: 70px;
      height: 56px;
      background-color: #FFFFFF;
      border-top-left-radius: 30px;
      border-bottom-left-radius: 30px;
      /* Extends pure white to the right to prevent any sub-pixel vertical seam */
      box-shadow: 14px 0 0 0 #FFFFFF;
      transition: transform 0.45s cubic-bezier(0.34, 1.42, 0.64, 1);
      pointer-events: none;
      z-index: 1;
    }

    /* Top concave liquid fillet curve */
    .nav-indicator::before {
      content: "";
      position: absolute;
      top: -20px;
      right: 0;
      width: 20px;
      height: 20px;
      background: transparent;
      border-bottom-right-radius: 20px;
      box-shadow: 10px 10px 0 10px #FFFFFF;
    }

    /* Bottom concave liquid fillet curve */
    .nav-indicator::after {
      content: "";
      position: absolute;
      bottom: -20px;
      right: 0;
      width: 20px;
      height: 20px;
      background: transparent;
      border-top-right-radius: 20px;
      box-shadow: 10px -10px 0 10px #FFFFFF;
    }

    /* Sidebar button transitions */
    .sidebar-btn {
      position: relative;
      z-index: 10;
      height: 56px;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: color 0.25s ease, transform 0.25s ease;
    }

    .sidebar-btn.active {
      color: #342A6E;
      transform: translateX(4px);
    }

    .sidebar-btn:not(.active) {
      color: rgba(255, 255, 255, 0.72);
    }

    .sidebar-btn:not(.active):hover {
      color: #FFFFFF;
    }

    /* Mobile liquid curve bottom nav */
    .mobile-nav-container {
      background: #342A6E;
      position: relative;
    }

    .mobile-nav-indicator {
      position: absolute;
      top: 0;
      left: 0;
      width: calc(100% / 6);
      height: 52px;
      transition: transform 0.45s cubic-bezier(0.34, 1.42, 0.64, 1);
      pointer-events: none;
      z-index: 1;
      display: flex;
      justify-content: center;
    }

    .mobile-nav-btn {
      position: relative;
      z-index: 10;
      height: 100%;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: color 0.25s ease, transform 0.25s ease;
      background: transparent;
      border: none;
      outline: none;
    }

    .mobile-nav-btn.active {
      color: #342A6E;
      transform: translateY(-2px);
    }

    .mobile-nav-btn:not(.active) {
      color: rgba(255, 255, 255, 0.72);
    }

    .mobile-nav-btn:not(.active):hover {
      color: #FFFFFF;
    }

    /* Page transition animations */
    .page-view {
      animation: pageSlideFade 0.3s ease-out forwards;
    }

    @keyframes pageSlideFade {
      0% {
        opacity: 0;
        transform: translateY(6px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Bottom-to-top entrance animation (280ms snappy cubic-bezier) */
    @keyframes contentSlideUpFade {
      0% {
        opacity: 0;
        transform: translateY(16px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-content-enter {
      animation: contentSlideUpFade 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
      will-change: transform, opacity;
    }

    /* Completely hide scrollbars across the whole site while retaining smooth scrolling */
    html, body, #scrollableWorkspace, * {
      -ms-overflow-style: none; /* IE and Edge */
      scrollbar-width: none; /* Firefox */
    }
    *::-webkit-scrollbar {
      display: none; /* Chrome, Safari and Opera */
      width: 0;
      height: 0;
    }

    /* Circular Progress Ring Animation */
    .progress-ring__circle {
      transition: stroke-dashoffset 0.75s cubic-bezier(0.4, 0, 0.2, 1);
      transform: rotate(-90deg);
      transform-origin: 50% 50%;
    }

    /* Bar chart grow animation */
    .bar-fill {
      transition: height 0.6s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.3s ease;
    }
  </style>

  @stack('styles')
</head>
<body class="h-screen w-full bg-white select-none m-0 p-0 overflow-hidden">

  <!-- TOP MICRO PROGRESS BAR (Smooth Page Loading Indicator) -->
  <div id="topProgressBar" class="fixed top-0 left-0 right-0 h-[3px] z-[9999] pointer-events-none opacity-0 transition-opacity duration-200">
    <div id="topProgressBarFill" class="h-full bg-gradient-to-r from-brandOrange via-[#F59E0B] to-movePurple w-0 shadow-[0_0_8px_#F29F3D]"></div>
  </div>

  <!-- MAIN OUTER SHELL: Fixed Full Viewport Height (Sidebar & Header stay fixed while inner content scrolls) -->
  <div class="w-full h-screen bg-white p-0 md:p-4 lg:p-5 flex flex-col md:flex-row gap-0 relative overflow-hidden">

    <!-- 1. FIXED DESKTOP SIDEBAR WITH SEAMLESS LIQUID CURVE -->
    @include('partials.sidebar')

    <!-- FIXED MOBILE NAVIGATION BAR WITH SEAMLESS LIQUID CURVE -->
    @include('partials.mobile-nav')

    <!-- 2. RIGHT COLUMN: FIXED TOP HEADER + SCROLLABLE WORKSPACE -->
    <div class="flex-1 flex flex-col h-full overflow-hidden bg-white pl-0 md:pl-8 md:pr-4 lg:pl-10 lg:pr-6">

      <!-- FIXED HEADER -->
      @include('partials.header')

      <!-- SCROLLABLE WORKSPACE CONTENT (Scrolls independently below Header) -->
      <div id="scrollableWorkspace" class="flex-1 overflow-y-auto px-4 md:px-0 pt-5 pb-32 md:pb-6">
        <div id="workspaceContentWrapper" class="animate-content-enter flex flex-col lg:flex-row gap-6 xl:gap-10 items-start w-full">

          <!-- MAIN DYNAMIC CONTENT AREA -->
          <div class="@hasSection('right_panel') flex-1 w-full min-w-0 @else w-full @endif">
            @yield('content')
          </div>

          <!-- RIGHT SIDE PANEL (CONTEXTUAL) -->
          @hasSection('right_panel')
            @yield('right_panel')
          @endif

        </div>
      </div>

    </div>
  </div>

  <!-- FLOATING CUSTOM MESSAGE TOAST -->
  @include('partials.toast')

  <!-- APPLICATION LOGIC & INTERACTIVE SCRIPTS -->
  @include('partials.scripts')
  @stack('scripts')

  <!-- SPA-LITE NAVIGATION & SMOOTH TRANSITION ENGINE -->
  <script src="{{ asset('js/pasar-spa.js') }}"></script>

</body>
</html>
