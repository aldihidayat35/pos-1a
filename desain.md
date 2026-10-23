# Panduan Desain & Standar UI/UX (desain.md)
**WaMac — Modern POS & Smart Warehouse Management System**

Dokumen ini adalah acuan baku (*single source of truth*) untuk desain visual, sistem token, tipografi, ikonografi, dan spesifikasi animasi interaktif di seluruh project ini. Setiap pengembang maupun asisten AI **wajib** mengikuti seluruh standar di bawah ini secara konsisten tanpa deviasi.

---

## 1. Filosofi Desain & Prinsip Anti-AI Slop

1. **Zero AI Slop (Bebas Efek Murahan AI)**:
   - **DILARANG** menggunakan latar belakang gradasi warna-warni (*gradient mesh/multicolor gradients*). Gunakan warna solid, flat, dan pastel/lembut yang rapi.
   - **DILARANG** menggunakan efek bayangan neon bercahaya (*neon glow, colored outer blur, drop-shadow neon*). Gunakan border tipis `1px` (`#ECEBF4` atau `#E6E4F0`) dan shadow sangat lembut (*subtle diffuse shadow* `0 1px 3px rgba(0,0,0,0.05)`).
   - **DILARANG** menggunakan elemen visual melayang tanpa fungsi struktural yang jelas.

2. **Dilarang Keras Menggunakan Emoji**:
   - Seluruh representasi visual, status, kategori, atau ilustrasi tombol **wajib menggunakan SVG icon**.
   - Tidak boleh ada emoji unicode seperti 📦, 🚚, 🔒, ⚡, 🔔, dll. dalam teks, badge, maupun tombol.

3. **Tipografi Wajib Konsisten: Poppins**:
   - Seluruh teks tanpa pengecualian (judul, paragraf, angka statistik, label tombol, form input, dan data tabel) menggunakan font keluarga **Poppins**.

4. **Motion-First (Setiap Objek Interaktif Memiliki Animasi)**:
   - Setiap elemen yang dapat diklik, di-hover, ditoggle, dimuat, atau berpindah status **wajib** memiliki animasi micro-interaction yang terdefinisi dengan jelas (*snappy & fluid*, durasi 200–350ms).

---

## 2. Design Tokens & Palet Warna Baku

Gunakan kode warna heksadesimal resmi berikut pada konfigurasi Tailwind CSS (`tailwind.config`):

| Token Nama | Nilai HEX | Karakter & Kegunaan |
| :--- | :--- | :--- |
| **`sidebar`** | `#342A6E` | Warna primer: Desktop sidebar, mobile bottom bar, tombol aksi utama, teks kontras tinggi |
| **`sidebarDark`** | `#292059` | Hover state tombol sidebar & aksen gelap |
| **`brandOrange`** | `#F29F3D` | Aksen sekunder: Hero banner, tombol primary highlight, indikator throughput, status masuk (IN) |
| **`brandCoral`** | `#E86B74` | Aksen peringatan/kritis: Tombol navigasi aktif, notifikasi dot, ring kapasitas Cold Storage |
| **`brandCyan`** | `#7AC7D9` | Aksen pelengkap: Gate dock siap, sensor suku cadang, showcase link |
| **`brandLavender`** | `#E9E8F5` | Aksen lembut: Background sub-menu hover, kartu non-aktif |
| **`panelBg`** | `#F6F6FA` | Latar belakang panel samping, card container, dan sub-menu pill bar |
| **`textMain`** | `#382E6B` | Warna teks utama (kontras tinggi, mudah dibaca, elegan) |
| **`textMuted`** | `#9490AD` | Warna teks sekunder, deskripsi, tanggal, dan placeholder |
| **`borderSubtle`** | `#ECEBF4` | Garis tepi (*border*) standar pembatas kartu & modul |
| **`borderDarker`** | `#E6E4F0` | Garis tepi (*border*) untuk tombol, dropdown, dan input form |
| **`cream`** | `#FAD9B8` | Aksen pastel hangat untuk ornamen grafis flat |
| **`white`** | `#FFFFFF` | Kanvas dasar dashboard, kartu aktif, dan notch indikator |

---

## 3. Standar Tipografi (Poppins)

Semua hierarki tipografi menggunakan font **Poppins** dengan skala proporsional berikut:

```css
font-family: 'Poppins', sans-serif;
```

| Tingkat Hierarki | Ukuran Font Tailwind | Font Weight | Contoh Penggunaan |
| :--- | :--- | :--- | :--- |
| **Page / Hero Heading** | `text-2xl sm:text-[32px]` | `font-bold` (700) | Judul hero banner ("Halo, Barbara!"), judul modul halaman |
| **Section Title** | `text-lg` (18px) | `font-bold` (700) | Judul bagian ("Fungsi Otomasi", "Laporan Pergerakan Stok") |
| **Card / Item Title** | `text-sm` (14px) / `text-base` (16px) | `font-bold` (700) | Nama barang inventaris, nama armada truk, label zona rak |
| **Big Number Stat** | `text-2xl` (24px) / `text-3xl` | `font-extrabold` (800) | Persentase throughput ("58%"), total SKU ("4.210 Unit") |
| **Standard Body** | `text-xs` (12px) / `text-sm` (14px) | `font-medium` (500) | Paragraf deskripsi, informasi operator, isi tabel |
| **Label / Sub-Menu Pill** | `text-[11px]` / `text-xs` | `font-semibold` (600) | Tombol sub-menu, status barang di tabel, badge gate dock |
| **Micro Badge / Day Tag** | `text-[10px]` | `font-bold` (700) uppercase | Tag hari kalender ("MO", "TU", "KAMIS SAAT INI") |

---

## 4. Standar Ikonografi (Pengganti Emoji)

### Aturan Ikon:
1. Menggunakan **Inline SVG** bergaya *Heroicons Outline*:
   - `viewBox="0 0 24 24"`
   - `fill="none"`
   - `stroke="currentColor"`
   - `stroke-width="2"` (atau `2.2` untuk ketegasan visual)
   - `stroke-linecap="round"` dan `stroke-linejoin="round"`
2. Ukuran baku ikon:
   - **Ikon micro / dalam badge / tag:** `w-3.5 h-3.5`
   - **Ikon tombol standar / navbar / input:** `w-4 h-4` atau `w-5 h-5`
   - **Ikon fitur dalam kotak wadah (*container box*):** Kotak berukuran `w-8 h-8 rounded-xl` dengan ikon `w-4 h-4` di dalamnya.

### Contoh Mapping Pengganti Emoji:

```html
<!-- PENGGANTI EMOJI BOX / BARANG (📦) -->
<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
</svg>

<!-- PENGGANTI EMOJI TRUK / PENGIRIMAN (🚚) -->
<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
  <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
</svg>

<!-- PENGGANTI EMOJI GEMBOK / KEAMANAN (🔒) -->
<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
  <rect x="5" y="11" width="14" height="10" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
  <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0v4"/>
</svg>

<!-- PENGGANTI EMOJI CEKLIS / VERIFIKASI (✅) -->
<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
</svg>
```

---

## 5. Sistem & Katalog Animasi (Motion Design System)

Standar pergerakan mengutamakan **Snappy & Fluid Micro-interactions**. Tidak membuat pengguna menunggu, namun memberikan umpan balik taktil dan visual yang sangat memuaskan.

### Token Timing & Easing Baku:
- **Snappy Feedback (Hover/Click):** `150ms – 250ms ease`
- **Fluid Movement (Indicator/Transition):** `300ms – 450ms cubic-bezier(0.34, 1.42, 0.64, 1)` (efek peredam kurva cairan)
- **Data Load / Chart Growth:** `600ms – 750ms cubic-bezier(0.34, 1.56, 0.64, 1)`

---

### Spesifikasi Animasi Per Objek:

#### 1. Indikator Sidebar Desktop (Liquid Curve Notch)
* **Karakter:** Notch putih berbentuk kapsul yang meluncur vertikal mengikuti tombol sidebar yang aktif, menyatu tanpa sambungan (*seamless*) ke kanvas putih dashboard berkat lengkungan kurva cekung atas dan bawah (*concave fillet curve*).
* **Durasi & Easing:** `0.45s cubic-bezier(0.34, 1.42, 0.64, 1)`
* **Implementasi:**
  ```css
  .nav-indicator {
    position: absolute;
    right: 0;
    width: 70px;
    height: 56px;
    background-color: #FFFFFF;
    border-top-left-radius: 30px;
    border-bottom-left-radius: 30px;
    box-shadow: 14px 0 0 0 #FFFFFF;
    transition: transform 0.45s cubic-bezier(0.34, 1.42, 0.64, 1);
    pointer-events: none;
    z-index: 1;
  }
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
  ```

#### 2. Tombol Navigasi Sidebar & Bottom Bar (Icon Transition)
* **Karakter:** Saat aktif, ikon berubah warna menjadi `#342A6E` dan bergeser sedikit ke kanan pada desktop (`translateX(4px)`) atau bergeser ke atas pada mobile (`translateY(-2px)`).
* **Durasi:** `transition: color 0.25s ease, transform 0.25s ease;`

#### 3. Kartu Interaktif / Metric Cards (Hover Lift)
* **Karakter:** Saat kursor mouse diarahkan ke kartu metrik atau kalender, kartu naik tipis ke atas (`translateY(-2px)`), menguatkan rasa elevasi tanpa shadow neon yang berlebihan.
* **Kelas Tailwind:** `transform hover:-translate-y-0.5 transition duration-200 cursor-pointer`

#### 4. Sakelar Toggle Otomasi (Split Header Switch)
* **Karakter:** Dot sakelar meluncur secara horizontal (`translateX(14px)`) saat aktif dan kembali ke `translateX(0px)` saat mati, disertai transisi warna header kartu secara simultan.
* **Durasi:** `transition duration-300 ease`
* **Implementasi:**
  ```javascript
  // Aktif:
  dot.style.transform = 'translateX(14px)';
  dot.style.backgroundColor = activeHex;
  // Non-aktif:
  dot.style.transform = 'translateX(0px)';
  dot.style.backgroundColor = '#9D97C4';
  ```

#### 5. Progress Ring Lingkaran (Monitoring Kapasitas Zona)
* **Karakter:** Garis melingkar SVG (*circle stroke*) bertambah atau berkurang dengan mulus (*smooth radial winding*) saat data diperbarui atau diklik.
* **Implementasi:**
  ```css
  .progress-ring__circle {
    transition: stroke-dashoffset 0.75s cubic-bezier(0.4, 0, 0.2, 1);
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
  }
  ```

#### 6. Grafik Batang Throughput (Bar Fill Growth)
* **Karakter:** Batang vertikal bertumbuh ke atas dari dasar container dengan kurva elastis halus saat hari dipilih.
* **Implementasi:**
  ```css
  .bar-fill {
    transition: height 0.6s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.3s ease;
  }
  ```

#### 7. Transisi Modul Halaman (Page Slide Fade)
* **Karakter:** Saat berpindah menu navigasi, halaman baru memudar masuk dengan sedikit pergeseran dari bawah ke atas sejauh 6px agar terasa alami.
* **Implementasi:**
  ```css
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
  ```

#### 8. Dropdown Menu & Popover (Origin Scaling)
* **Karakter:** Menu dropdown sub-modul mobile membesar dari sudut kiri atas (`origin-top-left`), dari `scale-95 opacity-0` menuju `scale-100 opacity-100`, sementara ikon chevron berputar 180 derajat (`rotate-180`).
* **Durasi:** `transition-all duration-200`

#### 9. Floating Toast Notifikasi (Slide & Fade Out)
* **Karakter:** Kotak toast muncul dari bawah layar dengan transisi `translate-y-24` ke `translate-y-0` dan `opacity-0` ke `opacity-100`. Setelah 3,4 detik otomatis kembali meluncur ke bawah dan memudar.
* **Durasi:** `transition-all duration-300`

#### 10. Tombol & Tombol Aksi (Click Feedback)
* **Karakter:** Tombol memiliki efek hover opasitas `hover:opacity-90` dan efek sentuh `active:scale-95` untuk kepastian feedback klik.

---

## 6. Ringkasan Do's & Don'ts

| Kategori | Wajib Dilakukan (DO) | Dilarang Keras (DON'T) |
| :--- | :--- | :--- |
| **Warna** | Warna solid, flat, soft tone (`#F29F3D`, `#E86B74`, `#7AC7D9`, `#342A6E`) | Gradasi warna pelangi, background gradient mesh, gradasi gelap-terang kasar |
| **Shadow** | Border halus `1px border-[#ECEBF4]` atau border container `border-[#E6E4F0]` | Bayangan neon berwarna (`shadow-[0_0_20px_#f29f3d]`, glowing drop shadow) |
| **Ikon** | Inline SVG Heroicons outline (`stroke="currentColor"`) | Emoji sistem (📦, 🚚, 🔒, dll.) |
| **Font** | `Poppins` di semua teks (regular, medium, bold, extrebold) | Font lain seperti Roboto, Arial, Times, Quicksand, Inter, dll. |
| **Scrollbar** | Disembunyikan rapi (`scrollbar-width: none; ::-webkit-scrollbar { display: none; }`) | Scrollbar default browser yang tebal dan merusak estetika |
| **Animasi** | Setiap interaksi memiliki transisi halus (`200–350ms`, `cubic-bezier`) | Komponen kaku tanpa animasi, atau animasi lambat (>1 detik) yang menghambat kerja |

---

## 7. Template Instruksi Sistem untuk AI (Copy-Pasteable)

Gunakan teks prompt berikut saat memerintahkan AI model agar selalu mematuhi desain ini:

```markdown
Anda adalah asisten AI front-end untuk proyek WaMac POS & Smart Warehouse.
Patuhi aturan desain berikut secara ketat tanpa pengecualian:
1. FONT: Gunakan font Poppins secara konsisten untuk seluruh teks, angka, dan tombol.
2. DILARANG EMOJI: Jangan pernah menggunakan emoji unicode (seperti 📦, 🚚, 🔒). Selalu gunakan inline SVG (viewBox="0 0 24 24", fill="none", stroke="currentColor", stroke-width="2", stroke-linecap="round", stroke-linejoin="round").
3. ANTI-AI SLOP: Dilarang menggunakan gradasi warna latar belakang dan bayangan cahaya neon/glowing. Gunakan palet flat solid: sidebar: #342A6E, brandOrange: #F29F3D, brandCoral: #E86B74, brandCyan: #7AC7D9, panelBg: #F6F6FA, border: #ECEBF4.
4. ANIMASI: Setiap elemen interaktif wajib memiliki animasi (hover lift translateY -2px, active scale, transition 200-350ms, liquid curve indicator, bar chart fill grow, page-view slide fade).
```
