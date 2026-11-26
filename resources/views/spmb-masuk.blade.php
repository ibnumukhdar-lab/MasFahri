@extends('layouts.publik')
@section('judul', 'Masuk Area Pendaftar — SPMB SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'SPMB', 'judul' => 'Masuk Area Pendaftar', 'sub' => 'Masukkan nomor pendaftaran dan nomor WhatsApp yang Anda daftarkan di formulir.'])
<x-seksi judul="">
  <div class="max-w-md mx-auto">
    @if ($errors->any())
      <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-4 py-3 text-[13px] mb-5">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif
    <form action="{{ route('spmb.pendaftar.login') }}" method="post" class="kartu p-6 md:p-7 text-[13.5px]">
      @csrf
      <div class="mb-4">
        <label class="block mb-1.5 font-medium text-navy">Nomor pendaftaran</label>
        <input name="nomor" value="{{ old('nomor') }}" placeholder="SPMB-2027-0001" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 uppercase">
      </div>
      <div class="mb-5">
        <label class="block mb-1.5 font-medium text-navy">Nomor WhatsApp (sandi Anda)</label>
        <input name="whatsapp" placeholder="08xxxxxxxxxx" required inputmode="tel" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5">
        <p class="text-[12px] text-slate-500 mt-1.5">Sandi Anda adalah nomor WhatsApp yang didaftarkan saat mengisi formulir.</p>
      </div>
      <button class="w-full bg-navy text-white rounded-xl py-3 font-bold">Masuk</button>
    </form>
    <p class="text-[12.5px] text-slate-500 text-center mt-4">Belum mendaftar? <a href="{{ route('spmb') }}" class="text-navy font-semibold underline">Isi formulir SPMB dulu</a> · Lupa nomor? Cek halaman <a href="{{ route('spmb.status') }}" class="text-navy font-semibold underline">status pendaftaran</a>.</p>
  </div>
</x-seksi>
@endsection
