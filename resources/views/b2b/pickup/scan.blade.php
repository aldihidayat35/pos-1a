@extends('layouts.app')

@section('title', 'Scan & Serah Terima Tiket QR B2B — PasarHub')

@section('content')
<main class="flex-1 w-full space-y-6">
  <!-- Top Bar & Breadcrumb -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-semibold text-textMuted mb-1">
        <a href="{{ route('b2b.index') }}" class="hover:text-sidebar transition">Kemitraan B2B</a>
        <span>/</span>
        <span class="text-sidebar font-bold">Terminal Serah Terima Tiket QR</span>
      </div>
      <h1 class="font-display text-2xl font-bold text-textMain tracking-tight">
        Scan Tiket QR &amp; Serah Terima Barang B2B
      </h1>
      <p class="text-xs text-textMuted mt-0.5">
        Verifikasi token kriptografis sekali pakai, konversi reservasi ke mutasi stok keluar (Ungu), dan pembukuan piutang.
      </p>
    </div>

    <!-- Active Location Indicator -->
    <div class="flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-panelBg border border-[#ECEBF4] text-xs font-bold text-sidebar">
      <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
      <span>Lokasi Aktif: {{ $activeLocation?->name ?? 'Gudang/Toko' }} ({{ $activeLocation?->code ?? 'LOC' }})</span>
    </div>
  </div>



  <!-- 1. SCANNER & PENDING TICKETS (FULL WIDTH) -->
  <div class="space-y-6">
    <!-- SCANNER INPUT CARD -->
    <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-5">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
        </div>
        <div>
          <h3 class="font-display font-bold text-base text-textMain">Input / Scan Tiket Serah Terima</h3>
          <p class="text-xs text-textMuted">Arahkan scanner barcode/QR atau ketik nomor tiket/token manual</p>
        </div>
      </div>

      <form id="redeemForm" method="POST" action="{{ route('b2b.pickup.redeem') }}" class="space-y-4">
        @csrf
        <div class="space-y-1.5">
          <label class="block text-xs font-bold text-textMain">Nomor Tiket / Token QR Kriptografis</label>
          <div class="flex flex-col sm:flex-row items-stretch gap-2.5">
            <div class="relative flex-1">
              <input type="text" id="tokenInput" name="token" value="{{ old('token') }}" placeholder="Contoh: TKT-20261024-0001 atau TK-XXXXXX" class="w-full text-sm font-mono rounded-xl border-2 border-[#ECEBF4] pl-10 pr-4 py-3 focus:outline-none focus:border-sidebar uppercase" autofocus required>
              <svg class="w-5 h-5 text-textMuted absolute left-3 top-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            </div>

            <button type="button" onclick="verifyTicket()" class="px-5 py-3 bg-sidebar hover:bg-sidebarDark text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2 shadow-sm shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              <span>Cek Validitas</span>
            </button>
          </div>
        </div>

        <!-- VERIFICATION PREVIEW RESULT PANEL -->
        <div id="verifyResult" class="hidden p-5 rounded-2xl bg-panelBg border border-[#ECEBF4] space-y-4">
          <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
            <div>
              <span class="text-[10px] text-textMuted uppercase font-bold">Hasil Verifikasi Tiket</span>
              <h4 class="font-display font-bold text-sm text-textMain" id="previewOrderNum">B2B-ORD</h4>
            </div>
            <span id="previewStatusBadge" class="px-2.5 py-1 rounded-full text-[10px] font-bold">STATUS</span>
          </div>

          <!-- Mismatch or Already Redeemed Warning -->
          <div id="previewWarning" class="hidden p-3 rounded-xl bg-red-100 border border-red-200 text-red-800 text-xs font-semibold">
            <span id="previewWarningText">Peringatan</span>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
            <div>
              <span class="text-[10px] text-textMuted font-bold uppercase">Mitra Pemesan</span>
              <p class="font-bold text-textMain mt-0.5" id="previewMemberName">-</p>
              <span class="text-[10px] font-mono text-textMuted" id="previewMemberCode">-</span>
            </div>
            <div>
              <span class="text-[10px] text-textMuted font-bold uppercase">Lokasi Dituju</span>
              <p class="font-bold text-sidebar mt-0.5" id="previewLocationName">-</p>
            </div>
            <div>
              <span class="text-[10px] text-textMuted font-bold uppercase">Ketentuan Bayar</span>
              <p class="font-bold text-textMain mt-0.5" id="previewPaymentTerm">-</p>
            </div>
          </div>

          <!-- Items list preview -->
          <div class="border-t border-[#ECEBF4] pt-3">
            <span class="text-[10px] text-textMuted font-bold uppercase block mb-1">Daftar Barang yang Dikeluarkan:</span>
            <ul id="previewItemsList" class="space-y-1 text-xs text-textMain font-medium">
              <!-- Injected via JS -->
            </ul>
          </div>

          <div class="border-t border-[#ECEBF4] pt-3 flex justify-between items-center">
            <span class="text-xs font-bold text-textMain">Total Nilai Pesanan:</span>
            <span class="font-display font-extrabold text-base text-sidebar" id="previewTotalFormatted">Rp 0</span>
          </div>

          <!-- Submit Redemption Button -->
          <div class="pt-2">
            <button type="submit" id="btnRedeemSubmit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
              <span>Konfirmasi Serah Terima Barang (Redeem Tiket Sekarang)</span>
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- PENDING PICKUP TICKETS AT THIS LOCATION -->
    <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-4">
      <h3 class="font-display font-bold text-base text-textMain">Tiket Siap Diambil di Lokasi Ini</h3>
      <p class="text-xs text-textMuted">Daftar pesanan aktif yang menunggu penjemputan mitra di {{ $activeLocation?->name }}</p>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-[#ECEBF4] text-textMuted">
              <th class="py-2.5 px-3 font-bold uppercase text-[10px]">No. Tiket</th>
              <th class="py-2.5 px-3 font-bold uppercase text-[10px]">No. Pesanan</th>
              <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Mitra</th>
              <th class="py-2.5 px-3 font-bold uppercase text-[10px]">Total Nilai</th>
              <th class="py-2.5 px-3 font-bold uppercase text-[10px] text-right">Pilih</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#ECEBF4]">
            @forelse($pendingTickets as $pt)
              <tr class="hover:bg-panelBg/50 transition">
                <td class="py-3 px-3 font-mono font-bold text-purple-700">
                  {{ $pt->ticket_number }}
                </td>
                <td class="py-3 px-3 font-mono font-semibold text-textMain">
                  {{ $pt->order->order_number }}
                </td>
                <td class="py-3 px-3 font-medium text-textMain">
                  {{ $pt->order->member->name }}
                </td>
                <td class="py-3 px-3 font-mono text-sidebar font-bold">
                  Rp {{ number_format($pt->order->total_amount, 0, ',', '.') }}
                </td>
                <td class="py-3 px-3 text-right">
                  <button type="button" onclick="selectTicketNumber('{{ $pt->ticket_number }}')" class="px-2.5 py-1 bg-panelBg hover:bg-sidebar hover:text-white text-sidebar font-bold rounded-lg border border-[#ECEBF4] transition text-[11px]">
                    Pilih
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-6 text-center text-textMuted text-xs">
                  Tidak ada pesanan menunggu penjemputan di lokasi ini.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- 2. AUDIT LOG: RECENT REDEMPTIONS (FULL WIDTH AT BOTTOM) -->
    <div class="bg-white rounded-[26px] p-6 border border-[#ECEBF4] space-y-4">
      <div class="flex items-center justify-between border-b border-[#ECEBF4] pb-3">
        <div>
          <span class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider">Audit Log</span>
          <h3 class="font-display font-bold text-base text-textMain">Riwayat Serah Terima Terbaru</h3>
          <p class="text-xs text-textMuted mt-0.5">Penyerahan barang B2B yang telah diproses di lokasi ini</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @forelse($recentRedemptions as $rr)
          <div class="p-3.5 rounded-2xl bg-panelBg border border-[#ECEBF4] space-y-2 text-xs">
            <div class="flex items-center justify-between">
              <span class="font-mono font-bold text-sidebar">{{ $rr->ticket_number }}</span>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">REDEEMED</span>
            </div>
            <p class="font-bold text-textMain">{{ $rr->order->member->name }}</p>
            <div class="flex justify-between text-[11px] text-textMuted">
              <span>Diserahkan oleh: <span class="font-semibold text-textMain">{{ $rr->redeemedBy?->name ?? 'Staf' }}</span></span>
              <span>{{ $rr->redeemed_at?->format('H:i, d/m') }}</span>
            </div>
            <div class="border-t border-[#ECEBF4] pt-1.5 flex justify-between items-center text-[11px]">
              <span class="text-textMuted font-mono">{{ $rr->order->order_number }}</span>
              <a href="{{ route('b2b.orders.show', $rr->order) }}" class="font-bold text-purple-700 hover:underline">
                Rincian →
              </a>
            </div>
          </div>
        @empty
          <div class="col-span-full py-8 text-center text-textMuted text-xs">
            Belum ada riwayat serah terima tiket di lokasi ini.
          </div>
        @endforelse
      </div>
    </div>
  </div>
</main>

<script>
  function selectTicketNumber(ticketNum) {
    document.getElementById('tokenInput').value = ticketNum;
    verifyTicket();
  }

  function verifyTicket() {
    const token = document.getElementById('tokenInput').value.trim();
    if (!token) return;

    fetch('{{ route('b2b.pickup.verify') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ token: token })
    })
    .then(res => res.json())
    .then(data => {
      const panel = document.getElementById('verifyResult');
      const warningBox = document.getElementById('previewWarning');
      const warningText = document.getElementById('previewWarningText');
      const submitBtn = document.getElementById('btnRedeemSubmit');

      if (!data.success) {
        panel.classList.remove('hidden');
        warningBox.classList.remove('hidden');
        warningText.innerText = data.message || 'Tiket tidak valid!';
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        return;
      }

      panel.classList.remove('hidden');
      document.getElementById('previewOrderNum').innerText = data.order.order_number + ' • ' + data.ticket.ticket_number;
      document.getElementById('previewMemberName').innerText = data.order.member_name;
      document.getElementById('previewMemberCode').innerText = data.order.member_code;
      document.getElementById('previewLocationName').innerText = data.ticket.destination_location;
      document.getElementById('previewPaymentTerm').innerText = data.order.payment_term;
      document.getElementById('previewTotalFormatted').innerText = data.order.total_formatted;

      // Status badge
      const badge = document.getElementById('previewStatusBadge');
      if (data.ticket.status === 'ISSUED') {
        badge.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200';
        badge.innerText = 'AKTIF (BISA DIAMBIL)';
      } else if (data.ticket.status === 'REDEEMED') {
        badge.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800';
        badge.innerText = 'SUDAH TERPAKAI';
      } else {
        badge.className = 'px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-800';
        badge.innerText = data.ticket.status;
      }

      // Items list
      const itemsUl = document.getElementById('previewItemsList');
      itemsUl.innerHTML = '';
      data.order.items.forEach(it => {
        const li = document.createElement('li');
        li.className = 'flex justify-between border-b border-[#ECEBF4] py-1';
        li.innerHTML = `<span>• ${it.name} (Qty: ${it.qty})</span><span class="font-mono font-bold text-sidebar">${it.subtotal}</span>`;
        itemsUl.appendChild(li);
      });

      // Warning evaluation
      if (data.ticket.status === 'REDEEMED') {
        warningBox.classList.remove('hidden');
        warningText.innerText = `PERINGATAN: Tiket ini SUDAH DIGUNAKAN pada ${data.ticket.redeemed_at} oleh ${data.ticket.redeemed_by}. Pengambilan ganda dicegah!`;
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
      } else if (data.ticket.location_mismatch) {
        warningBox.classList.remove('hidden');
        warningText.innerText = `LOKASI TIDAK COCOK: Tiket ini dialokasikan untuk ${data.ticket.destination_location}, sedangkan Anda berada di ${data.ticket.active_location}.`;
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
      } else {
        warningBox.classList.add('hidden');
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
      }
    })
    .catch(err => {
      console.error(err);
      if (typeof SwalModal !== 'undefined') {
        SwalModal.fire({
          icon: 'error',
          title: 'Kesalahan Server',
          text: 'Terjadi kesalahan koneksi server saat memeriksa tiket.'
        });
      } else {
        alert('Terjadi kesalahan koneksi server saat memeriksa tiket.');
      }
    });
  }
</script>
@endsection
