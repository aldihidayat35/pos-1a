<!-- =======================================================
     PAGE 3 (INDEX 3): KONTROL ZONA & IOT GUDANG
     Flat solid color banner (Removed gradient)
     ======================================================= -->
<div id="page-3" class="page-view hidden space-y-6">
  <div class="bg-brandCoral rounded-[24px] p-7 text-white">
    <h1 class="font-display text-2xl font-bold">Kalibrasi Sensor IoT & Parameter Rak</h1>
    <p class="text-white/90 text-xs sm:text-sm mt-1">Sesuaikan batas suhu otomatis, kecepatan robot AGV, dan ambang peringatan restock.</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <div class="bg-panelBg p-6 rounded-[24px] border border-[#ECEBF4] space-y-4">
      <div class="flex justify-between items-center">
        <span class="font-bold text-sm">Target Suhu Cold Storage (Zona D)</span>
        <span id="tempVal" class="font-extrabold text-brandCoral text-base">-18°C</span>
      </div>
      <input type="range" min="-25" max="-5" value="-18" oninput="document.getElementById('tempVal').innerText = this.value + '°C'" class="w-full accent-brandCoral cursor-pointer" />
      <p class="text-xs text-textMuted">Rentang aman produk beku: -16°C hingga -22°C.</p>
    </div>

    <div class="bg-panelBg p-6 rounded-[24px] border border-[#ECEBF4] space-y-4">
      <div class="flex justify-between items-center">
        <span class="font-bold text-sm">Kecepatan Armada Robot AGV</span>
        <span id="agvVal" class="font-extrabold text-brandOrange text-base">2.4 m/s</span>
      </div>
      <input type="range" min="10" max="35" value="24" oninput="document.getElementById('agvVal').innerText = (this.value/10).toFixed(1) + ' m/s'" class="w-full accent-brandOrange cursor-pointer" />
      <p class="text-xs text-textMuted">Kecepatan optimal untuk efisiensi baterai & keamanan lorong.</p>
    </div>

    <div class="bg-panelBg p-6 rounded-[24px] border border-[#ECEBF4] space-y-4">
      <div class="flex justify-between items-center">
        <span class="font-bold text-sm">Ambang Batas Auto-Reorder SKU</span>
        <span id="reorderVal" class="font-extrabold text-sidebar text-base">150 Unit</span>
      </div>
      <input type="range" min="50" max="500" step="25" value="150" oninput="document.getElementById('reorderVal').innerText = this.value + ' Unit'" class="w-full accent-sidebar cursor-pointer" />
      <p class="text-xs text-textMuted">Sistem otomatis menerbitkan draft PO saat stok di bawah batas ini.</p>
    </div>

    <div class="bg-panelBg p-6 rounded-[24px] border border-[#ECEBF4] space-y-4">
      <div class="flex justify-between items-center">
        <span class="font-bold text-sm">Kelembaban Udara Gudang Kering</span>
        <span id="humVal" class="font-extrabold text-sky-600 text-base">45% RH</span>
      </div>
      <input type="range" min="30" max="70" value="45" oninput="document.getElementById('humVal').innerText = this.value + '% RH'" class="w-full accent-sky-500 cursor-pointer" />
      <p class="text-xs text-textMuted">Dehumidifier industri aktif otomatis jika melebihi target.</p>
    </div>
  </div>
</div>
