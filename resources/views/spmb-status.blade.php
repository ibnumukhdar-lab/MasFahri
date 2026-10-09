@extends('layouts.publik')
@section('judul', 'Status Pendaftaran — SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'SPMB', 'judul' => 'Status Pendaftaran', 'sub' => 'Masukkan nomor pendaftaran Anda.'])
@php $waTautan = session('tautan_wa'); $waNomor = session('wa_nomor'); @endphp
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
    <input name="nomor" value="{{ $nomor }}" placeholder="SPMB-2026-0001" class="flex-1 border border-slate-200 rounded-xl px-3.5 py-2.5">
    <button class="bg-navy text-white rounded-xl px-5 font-semibold">Cek</button>
  </form>
  @if ($registrasi)
    <div class="kartu overflow-hidden max-w-xl">
      <div class="bg-navy-soft px-4 py-3 text-[13px] text-navy font-semibold">{{ $registrasi->nomor }}</div>
      <table class="w-full text-[13.5px]"><tbody>
        <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500 w-40">Nama siswa</td><td class="px-4 py-2.5 font-semibold text-navy">{{ $registrasi->nama_siswa }}</td></tr>
        <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500">Nama wali</td><td class="px-4 py-2.5">{{ $registrasi->nama_wali }}</td></tr>
        <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500">Tahun ajaran</td><td class="px-4 py-2.5">{{ $registrasi->tahun_ajaran }}</td></tr>
        <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500">Status</td><td class="px-4 py-2.5"><span class="chip">{{ \App\Models\Registration::STATUS[$registrasi->status] ?? $registrasi->status }}</span></td></tr>
        @if ($registrasi->pesan_admin)<tr><td class="px-4 py-2.5 text-slate-500">Pesan</td><td class="px-4 py-2.5">{{ $registrasi->pesan_admin }}</td></tr>@endif
      </tbody></table>
    </div>
  @elseif ($nomor)
    <p class="text-rose-700 text-[14px]">Nomor <strong>{{ $nomor }}</strong> tidak ditemukan. Periksa kembali atau hubungi admin sekolah.</p>
  @endif
</x-seksi>
@endsection
