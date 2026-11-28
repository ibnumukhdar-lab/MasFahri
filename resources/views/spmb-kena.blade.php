@extends('layouts.spmb')
@section('judul', 'Akun Belum Terverifikasi — SPMB SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'SPMB', 'judul' => 'Akun Belum Terverifikasi', 'sub' => 'Pendaftaran Anda sudah tercatat, namun belum diverifikasi oleh panitia.'])
<x-seksi judul="">
  <div class="max-w-lg mx-auto">
    <div class="kartu p-6 md:p-7 text-[13.5px]">
      <p class="mb-4">Nomor pendaftaran Anda <b class="text-navy">{{ $registrasi->nomor }}</b> (<b>{{ $registrasi->nama_siswa }}</b>) sudah masuk ke sistem, tetapi panitia <b>belum melakukan verifikasi</b>.</p>
      <div class="rounded-xl px-4 py-3.5 mb-4" style="background:#fffbe8;border:1px solid #f2e3ac">
        <p class="mb-1"><b class="text-navy">Silakan konfirmasi ke panitia:</b></p>
        <p class="text-slate-700">Hubungi <b>Bapak Rito</b> melalui WhatsApp untuk mempercepat verifikasi:</p>
        @php $waRito = preg_replace('/\D+/', '', \App\Models\Setting::ambil('wa_admin', '6285356002524')); @endphp
        <a href="https://wa.me/{{ $waRito }}?text={{ rawurlencode('Assalamu\'alaikum, saya pendaftar SPMB nomor '.$registrasi->nomor.' ('.$registrasi->nama_siswa.'). Mohon verifikasi pendaftaran saya agar bisa masuk area pendaftar. Terima kasih.') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 mt-3 bg-emerald-600 text-white rounded-xl px-5 py-2.5 font-semibold">
          Konfirmasi ke Panitia (WA)
        </a>
      </div>
      <p class="text-slate-600">Setelah panitia memverifikasi, buka kembali halaman ini dan masuk lagi — dashboard pendaftar beserta formulir biodata lengkap akan langsung tersedia.</p>
    </div>
    <div class="flex flex-wrap gap-3 justify-center mt-5">
      <a href="{{ route('spmb.pendaftar.masuk') }}" class="bg-navy text-white rounded-xl px-5 py-2.5 font-semibold text-[13.5px]">Kembali ke Halaman Masuk</a>
      <form action="{{ route('spmb.pendaftar.keluar') }}" method="post">@csrf<button class="border border-slate-300 text-slate-600 rounded-xl px-5 py-2.5 font-semibold text-[13.5px]">Keluar</button></form>
    </div>
  </div>
</x-seksi>
@endsection
