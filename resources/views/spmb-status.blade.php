@extends('layouts.publik')
@section('judul', 'Status Pendaftaran — SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'SPMB', 'judul' => 'Status Pendaftaran', 'sub' => 'Masukkan nomor pendaftaran Anda.'])
@php $waTautan = session('tautan_wa'); $waNomor = session('wa_nomor'); @endphp

<style>
  .st-kepala{position:relative;overflow:hidden;color:#fff;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;
    background:linear-gradient(125deg,#1b3455 0%,#1f3a5f 45%,#2b5288 100%)}
  .st-kepala::after{content:"";position:absolute;right:-40px;top:-54px;width:150px;height:150px;border-radius:50%;
    background:radial-gradient(circle,rgba(253,209,0,.18) 0%,rgba(253,209,0,0) 72%)}
  .st-eyebrow{font-size:10px;font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.62)}
  .st-nomor{font-size:17px;font-weight:800;letter-spacing:-.01em;margin-top:2px}
  .st-chip{position:relative;z-index:1;flex:none;display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;
    padding:5px 11px;border-radius:999px}
  .st-chip.ok{background:rgba(34,163,90,.22);border:1px solid rgba(134,239,172,.45);color:#c7f7d8}
  .st-chip.tunggu{background:rgba(253,209,0,.16);border:1px solid rgba(253,209,0,.34);color:#ffe483}
  .st-panel{border-radius:16px;padding:16px 18px;border:1px solid #e7eef6;background:#fff}
  .st-panel.ok{background:linear-gradient(180deg,#f4fbf6,#eef8f2);border-color:#bfe8cf}
  .st-panel.tunggu{background:linear-gradient(180deg,#fffdf3,#fffbe8);border-color:#f2e3ac}
  .st-panel h3{font-size:14.5px;font-weight:800;color:#1f3a5f;margin:0 0 4px}
  .st-panel.ok h3{color:#136b43}
  .st-panel p{font-size:13px;line-height:1.6;color:#475569;margin:0}
  .st-tombol{display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:12px;font-weight:700;font-size:13.5px;
    padding:12px 20px;margin-top:12px;transition:filter .15s}
  .st-tombol.utama{background:linear-gradient(135deg,#1f3a5f,#2b5288);color:#fff;box-shadow:0 12px 22px -16px rgba(31,58,95,.9)}
  .st-tombol.utama:hover{filter:brightness(1.07)}
  .st-tombol.wa{background:#128c4a;color:#fff}
  .st-tombol.wa:hover{filter:brightness(1.07)}
  .st-hint{font-size:11.5px;color:#64748b;margin-top:8px}
  .st-ikon{flex:none;width:34px;height:34px;border-radius:11px;display:grid;place-items:center}
  .st-ikon.ok{background:#dcf5e6;color:#14875a}
  .st-ikon.tunggu{background:#fff2c2;color:#a06a00}
  .st-info{display:flex;justify-content:space-between;gap:14px;padding:9px 18px;border-bottom:1px dashed #eef3f9;font-size:13px}
  .st-info:last-child{border-bottom:0}
  .st-info dt{color:#7c8ba1;flex:none}
  .st-info dd{font-weight:600;color:#1f3a5f;text-align:right;word-break:break-word}
</style>

@if (session('sukses') || $waTautan)
<x-seksi judul="Pendaftaran Terkirim">
  <div class="kartu p-6 md:p-8 max-w-2xl">
    @if (session('sukses'))
      <p class="text-emerald-700 font-bold text-[15px] mb-1">{{ session('sukses') }}</p>
    @endif
    @if ($waTautan)
      <p class="text-[13.5px] text-slate-600 mb-4">Langkah terakhir: kirim pesan konfirmasi ke WhatsApp admin sekolah{{ $waNomor ? ' ('.$waNomor.')' : '' }}. Isi pesannya sudah otomatis memuat data pendaftaran Anda — Anda cukup menekan tombol kirim.</p>
      <div class="flex flex-wrap items-center gap-3">
        <a id="wa-tombol" href="{{ $waTautan }}" target="_blank" rel="noopener" class="bg-emerald-600 text-white rounded-xl px-5 py-2.5 font-semibold text-[13.5px]">Kirim Konfirmasi ke WhatsApp</a>
        <span class="text-[12.5px] text-slate-500">Mengarahkan otomatis dalam <b id="wa-hitung">3</b> detik · <a href="#" id="wa-batal" class="underline">saya nanti saja</a></span>
      </div>
      <p class="text-[12.5px] text-slate-500 mt-3">Setelah panitia memeriksa bukti pembayaran formulir dan menyatakan <b>terverifikasi</b>, status di halaman ini berubah dan tombol masuk untuk melanjutkan pengisian formulir akan muncul.</p>
      @if (session('pesan_wa'))
        <details class="mt-4">
          <summary class="cursor-pointer text-[12.5px] font-semibold text-navy">Lihat isi pesan yang akan dikirim</summary>
          <pre class="mt-2 whitespace-pre-wrap text-[12.5px] text-slate-700 bg-slate-50 border border-slate-200 rounded-xl p-3">{{ session('pesan_wa') }}</pre>
        </details>
      @endif
    @endif
  </div>
</x-seksi>
<script>
  (function () {
    var tombol = document.getElementById('wa-tombol');
    if (!tombol) { return; }
    var tautan = tombol.href, hitung = document.getElementById('wa-hitung'), batal = false, sisa = 3;
    var batalEl = document.getElementById('wa-batal');
    if (batalEl) {
      batalEl.addEventListener('click', function (e) {
        e.preventDefault(); batal = true;
        if (hitung) { hitung.textContent = '0'; }
      });
    }
    var t = setInterval(function () {
      if (batal) { clearInterval(t); return; }
      sisa = sisa - 1;
      if (hitung) { hitung.textContent = String(Math.max(0, sisa)); }
      if (sisa <= 0) { clearInterval(t); window.location.href = tautan; }
    }, 1000);
  })();
</script>
@endif

<x-seksi judul="Hasil">
  <form action="{{ route('spmb.status') }}" method="get" class="mb-6 flex gap-2 max-w-xl text-[13.5px]">
    <input name="nomor" value="{{ $nomor }}" placeholder="SPMB-2027-0001" class="flex-1 border border-slate-200 rounded-xl px-3.5 py-2.5">
    <button class="bg-navy text-white rounded-xl px-5 font-semibold">Cek</button>
  </form>

  @if ($registrasi)
    @php
      $statusKunci = (string) $registrasi->status;
      $terverifikasi = $statusKunci !== 'baru';
      $bolehMasuk = in_array($statusKunci, ['dihubungi', 'lengkapi_formulir', 'tes', 'diterima'], true);
      $labelStatus = \App\Models\Registration::STATUS[$statusKunci] ?? $statusKunci;
      $waAdmin = preg_replace('/\D+/', '', (string) \App\Models\Setting::ambil('wa_admin', '6285356002524'));
      $pesanWa = rawurlencode('Assalamu\'alaikum, saya pendaftar SPMB nomor '.$registrasi->nomor.' ('.$registrasi->nama_siswa.'). Mohon diperiksa verifikasi bukti pembayaran formulir saya agar saya bisa melanjutkan pengisian formulir. Terima kasih.');
      $labelTombol = $statusKunci === 'lengkapi_formulir' ? 'Masuk & Lanjut Isi Formulir' : 'Masuk Area Pendaftar';
      $teksTerverifikasi = match ($statusKunci) {
          'lengkapi_formulir' => 'Bukti pembayaran formulir Anda sudah diperiksa dan diverifikasi panitia. Silakan masuk untuk melanjutkan pengisian formulir pendaftaran murid baru.',
          'tes' => 'Formulir Anda sudah diterima dan diverifikasi. Silakan masuk area pendaftar untuk melihat kartu tes dan mengerjakan tes CBT.',
          'diterima' => 'Pendaftaran Anda sudah diverifikasi dan dinyatakan diterima. Silakan masuk area pendaftar atau hubungi panitia untuk daftar ulang.',
          default => 'Pendaftaran Anda sudah diperiksa panitia. Silakan masuk area pendaftar untuk melihat rincian dan tahap berikutnya.',
      };
    @endphp

    <div class="max-w-xl">
      <div class="kartu overflow-hidden">
        <div class="st-kepala">
          <div>
            <p class="st-eyebrow">Nomor pendaftaran</p>
            <p class="st-nomor">{{ $registrasi->nomor }}</p>
          </div>
          <span class="st-chip {{ $terverifikasi ? 'ok' : 'tunggu' }}">
            @if ($terverifikasi)
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"/></svg>
              Terverifikasi
            @else
              <span class="w-1.5 h-1.5 rounded-full" style="background:#fdd100"></span>
              Belum terverifikasi
            @endif
          </span>
        </div>
        <dl>
          <div class="st-info"><dt>Nama siswa</dt><dd>{{ $registrasi->nama_siswa }}</dd></div>
          <div class="st-info"><dt>Nama wali</dt><dd>{{ $registrasi->nama_wali }}</dd></div>
          <div class="st-info"><dt>Tahun ajaran</dt><dd>{{ $registrasi->tahun_ajaran }}</dd></div>
          <div class="st-info"><dt>Tahap</dt><dd>{{ $labelStatus }}</dd></div>
          @if ($registrasi->pesan_admin)
            <div class="st-info"><dt>Pesan panitia</dt><dd>{{ $registrasi->pesan_admin }}</dd></div>
          @endif
        </dl>
      </div>

      @if ($bolehMasuk)
        <div class="st-panel ok mt-4">
          <div class="flex items-start gap-3">
            <span class="st-ikon ok">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"/></svg>
            </span>
            <div class="min-w-0">
              <h3>Terverifikasi</h3>
              <p>{{ $teksTerverifikasi }}</p>
              <a href="{{ route('spmb.pendaftar.masuk') }}" class="st-tombol utama">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><path d="M10 17l5-5-5-5M15 12H3"/></svg>
                {{ $labelTombol }}
              </a>
              <p class="st-hint">Kode akun: <b>{{ $registrasi->nomor }}</b> · Kata sandi: nomor WhatsApp yang Anda daftarkan.</p>
            </div>
          </div>
        </div>
      @elseif ($terverifikasi)
        <div class="st-panel ok mt-4">
          <div class="flex items-start gap-3">
            <span class="st-ikon ok">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"/></svg>
            </span>
            <div class="min-w-0">
              <h3>Terverifikasi</h3>
              <p>Pendaftaran Anda sudah diperiksa panitia. Untuk keterangan lebih lanjut, silakan hubungi panitia sekolah.</p>
            </div>
          </div>
        </div>
      @else
        <div class="st-panel tunggu mt-4">
          <div class="flex items-start gap-3">
            <span class="st-ikon tunggu">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3 2"/></svg>
            </span>
            <div class="min-w-0">
              <h3>Belum terverifikasi</h3>
              <p>Pendaftaran Anda sudah kami terima, tetapi <b>bukti pembayaran formulir belum diperiksa panitia</b>. Tombol masuk untuk melanjutkan pengisian formulir akan muncul di sini setelah diverifikasi.</p>
              @if ($waAdmin)
                <a href="https://wa.me/{{ $waAdmin }}?text={{ $pesanWa }}" target="_blank" rel="noopener" class="st-tombol wa">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm0 18a8 8 0 01-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1112 20zm4.4-5.9c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.5.1l-.7.8c-.1.2-.3.2-.5.1a6.5 6.5 0 01-3.2-2.8c-.1-.2 0-.4.1-.5l.6-.7c.1-.2.1-.3 0-.5l-.7-1.6c-.2-.4-.4-.4-.5-.4h-.5a1 1 0 00-.7.3c-.3.3-.9.9-.9 2.1s.9 2.4 1 2.6c.1.2 1.7 2.7 4.2 3.7 2 .8 2.5.7 2.9.6.5-.1 1.4-.6 1.6-1.2.2-.6.2-1.1.1-1.2-.1-.1-.2-.2-.4-.3z"/></svg>
                  Konfirmasi ke Panitia (WA)
                </a>
              @endif
              <p class="st-hint">Bila belum mengunggah bukti transfer, kirimkan foto/screenshot bukti ke panitia lewat WhatsApp di atas.</p>
            </div>
          </div>
        </div>
      @endif
    </div>
  @elseif ($nomor)
    <p class="text-rose-700 text-[14px]">Nomor <strong>{{ $nomor }}</strong> tidak ditemukan. Periksa kembali atau hubungi admin sekolah.</p>
  @endif
</x-seksi>
@endsection
