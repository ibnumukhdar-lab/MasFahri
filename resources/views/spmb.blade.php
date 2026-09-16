@extends('layouts.publik')
@section('judul', 'SPMB — Penerimaan Murid Baru SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'Penerimaan ' . $tahunAjaran, 'judul' => 'Sistem Penerimaan Murid Baru', 'sub' => 'Dibuka sepanjang tahun dengan sistem waiting list, tersedia jalur prestasi.'])
<x-seksi judul="Formulir Pendaftaran">
  @if ($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-4 py-3 text-[13px] mb-5">
      <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif
  <form action="{{ route('spmb.simpan') }}" method="post" class="kartu p-6 md:p-8 grid md:grid-cols-2 gap-4 text-[13.5px]">
    @csrf
    <div><label class="block mb-1.5 font-medium text-navy">Nama Wali</label><input name="nama_wali" value="{{ old('nama_wali') }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Nama Siswa</label><input name="nama_siswa" value="{{ old('nama_siswa') }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Jenis Kelamin</label>
      <select name="jenis_kelamin" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-600">
        <option value="">— Pilih —</option><option @selected(old('jenis_kelamin') === 'Laki-laki')>Laki-laki</option><option @selected(old('jenis_kelamin') === 'Perempuan')>Perempuan</option>
      </select></div>
    <div><label class="block mb-1.5 font-medium text-navy">Sekolah Asal</label><input name="sekolah_asal" value="{{ old('sekolah_asal') }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Minat Program</label>
      <select name="minat_program" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-600">
        <option>Reguler — Boarding</option><option>Reguler — Non Boarding</option><option>Jalur Prestasi</option>
      </select></div>
    <div><label class="block mb-1.5 font-medium text-navy">Tahun Ajaran</label><input name="tahun_ajaran" value="{{ $tahunAjaran }}" readonly class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50"></div>
    <div class="md:col-span-2"><label class="block mb-1.5 font-medium text-navy">Nomor WhatsApp Wali</label><input name="whatsapp" value="{{ old('whatsapp') }}" placeholder="08xxxxxxxxxx" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div class="md:col-span-2"><label class="block mb-1.5 font-medium text-navy">Catatan (opsional)</label><textarea name="catatan" rows="2" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5">{{ old('catatan') }}</textarea></div>
    <div class="md:col-span-2 flex flex-wrap gap-3 items-center">
      <button class="bg-navy text-white rounded-xl px-6 py-2.5 font-semibold">Kirim Pendaftaran</button>
      <span class="text-[12.5px] text-slate-500">Anda akan menerima nomor pendaftaran untuk mengecek status.</span>
    </div>
  </form>
</x-seksi>
<x-seksi judul="Alur Pendaftaran">
  <div class="grid md:grid-cols-4 gap-4 text-[13.5px]">
    <div class="kartu p-5"><p class="font-bold text-navy mb-1">1. Isi formulir</p><p class="text-slate-600">Data wali &amp; calon murid, dari HP maupun komputer.</p></div>
    <div class="kartu p-5"><p class="font-bold text-navy mb-1">2. Verifikasi admin</p><p class="text-slate-600">Admin sekolah menghubungi lewat WhatsApp.</p></div>
    <div class="kartu p-5"><p class="font-bold text-navy mb-1">3. Tes &amp; wawancara</p><p class="text-slate-600">Tes baca Al-Qur'an, akademik dasar, wawancara wali.</p></div>
    <div class="kartu p-5"><p class="font-bold text-navy mb-1">4. Pengumuman</p><p class="text-slate-600">Hasil seleksi diumumkan lewat sistem dan WhatsApp.</p></div>
  </div>
</x-seksi>
<x-seksi judul="Cek Status Pendaftaran">
  <form action="{{ route('spmb.status') }}" method="get" class="kartu p-6 max-w-xl text-[13.5px]">
    <label class="block mb-1.5 font-medium text-navy">Nomor pendaftaran</label>
    <div class="flex gap-2">
      <input name="nomor" value="{{ request('nomor') }}" placeholder="SPMB-2026-0001" class="flex-1 border border-slate-200 rounded-xl px-3.5 py-2.5">
      <button class="bg-navy text-white rounded-xl px-5 font-semibold">Cek</button>
    </div>
  </form>
</x-seksi>
@endsection
