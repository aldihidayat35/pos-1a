<!-- ============================================================
     RIGHT SIDE PANEL (Soft flat colors, no neon shadows)
     ============================================================ -->
<aside class="w-full lg:w-[370px] xl:w-[410px] shrink-0 flex flex-col justify-between">
  
  <!-- Soft Lavender-Gray Container -->
  <div class="bg-panelBg rounded-[30px] p-6 sm:p-7 flex-1 flex flex-col justify-between space-y-7 border border-[#ECEBF4]">

    <!-- SECTION 1: FUNGSI OTOMASI GUDANG -->
    <div class="space-y-4">
      <h2 class="font-display font-bold text-lg text-textMain">Fungsi Otomasi</h2>

      <!-- 2x2 Grid of Split-Header Toggle Cards (No neon shadows) -->
      <div class="grid grid-cols-2 gap-4">

        <!-- Card 1: Soft Coral ("Optimasi Rak") -->
        <div id="fnCard0" class="rounded-[20px] overflow-hidden border border-[#ECEBF4] bg-white transition duration-300">
          <div id="fnTop0" class="bg-brandCoral p-3.5 flex items-center justify-between text-white transition-colors duration-300">
            <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 20V10M12 20V4M6 20v-6" />
              </svg>
            </div>
            <button onclick="openToast('Optimasi Slotting Rak: Menempatkan barang fast-moving dekat pintu loading.')" class="opacity-80 hover:opacity-100" title="Detail Optimasi Rak">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
            </button>
          </div>
          <div class="p-3.5 flex items-center justify-between bg-white">
            <span class="text-xs font-bold text-textMain">Optimasi Rak</span>
            <button onclick="toggleFunctionCard(0, '#E86B74')" id="fnToggle0" class="w-8 h-4 rounded-full bg-brandCoral/25 relative transition flex items-center p-0.5">
              <span id="fnDot0" class="w-3.5 h-3.5 rounded-full bg-brandCoral transform translate-x-3.5 transition"></span>
            </button>
          </div>
        </div>

        <!-- Card 2: Soft Orange ("Smart Scan") -->
        <div id="fnCard1" class="rounded-[20px] overflow-hidden border border-[#ECEBF4] bg-white transition duration-300">
          <div id="fnTop1" class="bg-brandOrange p-3.5 flex items-center justify-between text-white transition-colors duration-300">
            <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0" />
              </svg>
            </div>
            <button onclick="openToast('Smart RFID Scan: Memindai palet secara otomatis saat melewati gerbang.')" class="opacity-80 hover:opacity-100" title="Detail Smart Scan">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
            </button>
          </div>
          <div class="p-3.5 flex items-center justify-between bg-white">
            <span class="text-xs font-bold text-textMain">Smart Scan</span>
            <button onclick="toggleFunctionCard(1, '#F29F3D')" id="fnToggle1" class="w-8 h-4 rounded-full bg-brandOrange/25 relative transition flex items-center p-0.5">
              <span id="fnDot1" class="w-3.5 h-3.5 rounded-full bg-brandOrange transform translate-x-3.5 transition"></span>
            </button>
          </div>
        </div>

        <!-- Card 3: Soft Sky Cyan ("Sensor Suhu") -->
        <div id="fnCard2" class="rounded-[20px] overflow-hidden border border-[#ECEBF4] bg-white transition duration-300">
          <div id="fnTop2" class="bg-brandCyan p-3.5 flex items-center justify-between text-white transition-colors duration-300">
            <div class="w-8 h-8 rounded-xl bg-white/25 flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <button onclick="openToast('Proteksi Suhu & Kelembaban aktif di seluruh lorong gudang.')" class="opacity-80 hover:opacity-100" title="Detail Sensor Suhu">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
            </button>
          </div>
          <div class="p-3.5 flex items-center justify-between bg-white">
            <span class="text-xs font-bold text-textMain">Sensor Suhu</span>
            <button onclick="toggleFunctionCard(2, '#7AC7D9')" id="fnToggle2" class="w-8 h-4 rounded-full bg-brandCyan/30 relative transition flex items-center p-0.5">
              <span id="fnDot2" class="w-3.5 h-3.5 rounded-full bg-brandCyan transform translate-x-3.5 transition"></span>
            </button>
          </div>
        </div>

        <!-- Card 4: Soft Lavender/Muted ("Sinkron ERP") -->
        <div id="fnCard3" class="rounded-[20px] overflow-hidden border border-[#E5E4F0] bg-[#F2F2FA] transition duration-300">
          <div id="fnTop3" class="bg-brandLavender p-3.5 flex items-center justify-between text-textMuted transition-colors duration-300">
            <div class="w-8 h-8 rounded-xl bg-white/60 flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </div>
            <button onclick="openToast('Sinkronisasi stok real-time dengan SAP / ERP Pusat.')" class="opacity-80 hover:opacity-100" title="Detail Sinkron ERP">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
            </button>
          </div>
          <div class="p-3.5 flex items-center justify-between bg-[#F2F2FA]">
            <span class="text-xs font-bold text-textMuted">Sinkron ERP</span>
            <button onclick="toggleFunctionCard(3, '#342A6E')" id="fnToggle3" class="w-8 h-4 rounded-full bg-indigo-200/70 relative transition flex items-center p-0.5">
              <span id="fnDot3" class="w-3.5 h-3.5 rounded-full bg-[#9D97C4] transform translate-x-0 transition"></span>
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- SECTION 2: STATISTIK THROUGHPUT GUDANG -->
    <div class="space-y-4">
      <h2 class="font-display font-bold text-lg text-textMain">Statistik Throughput</h2>

      <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-6">
        <!-- Header with Percentage & Chart Mode Switcher -->
        <div class="flex items-start justify-between">
          <div>
            <p id="statDayTitle" class="text-[10px] font-bold tracking-wider uppercase text-textMuted">KAMIS SAAT INI</p>
            <p id="statPercentValue" class="font-display text-2xl font-extrabold text-textMain mt-0.5">58%</p>
          </div>

          <!-- Bar / Line Mode Switcher -->
          <div class="flex items-center bg-panelBg rounded-xl p-1 border border-gray-100">
            <button id="btnBarMode" onclick="setChartMode('bar')" class="w-8 h-7 rounded-lg bg-brandOrange text-white flex items-center justify-center transition" title="Grafik Batang">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 20V10M12 20V4M6 20v-6"/>
              </svg>
            </button>
            <button id="btnLineMode" onclick="setChartMode('line')" class="w-8 h-7 rounded-lg text-textMuted hover:text-textMain flex items-center justify-center transition" title="Grafik Tren">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Vertical Thin Pill Bars (MO TU WE TH FR SA SU) -->
        <div id="barChartView" class="flex items-end justify-between h-40 pt-2 px-1">
          <!-- Populated dynamically by JS -->
        </div>

        <!-- Interactive SVG Line Chart Alternative (Flat fill, no gradient) -->
        <div id="lineChartView" class="hidden h-40 pt-4 flex flex-col justify-between">
          <svg viewBox="0 0 260 100" class="w-full h-28 overflow-visible">
            <path d="M10,45 C45,65 65,30 95,40 C125,50 140,15 170,22 C200,30 225,55 250,35 L250,95 L10,95 Z" fill="#FDF3E7" />
            <path d="M10,45 C45,65 65,30 95,40 C125,50 140,15 170,22 C200,30 225,55 250,35" fill="none" stroke="#F29F3D" stroke-width="3.5" stroke-linecap="round"/>
            <circle cx="135" cy="22" r="5" fill="#E86B74" stroke="#FFF" stroke-width="2"/>
          </svg>
          <div class="flex justify-between text-[10px] font-bold text-textMuted px-1">
            <span>MO</span><span>TU</span><span>WE</span><span class="text-brandOrange">TH</span><span>FR</span><span>SA</span><span>SU</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</aside>
