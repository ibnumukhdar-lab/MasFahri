@extends('layouts.publik')
@section('judul', 'Dashboard Pendaftar — SPMB SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'Area Pendaftar SPMB', 'judul' => 'Halo, ' . $registrasi->nama_siswa . ' 👋', 'sub' => 'Nomor pendaftaran '.$registrasi->nomor.' — status: '.(\App\Models\Registration::STATUS[$registrasi->status] ?? $registrasi->status).'. Lengkapi biodata di bawah ini.'])
@if ($registrasi->status === 'tes' || $ujian)
<x-seksi judul="Kartu Tes & Tes CBT">
  <div class="kartu p-6 md:p-7 max-w-2xl">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
      <div>
        <p class="text-[12px] font-bold uppercase tracking-wide text-slate-500 mb-1">Kode login tes (CBT)</p>
        <p class="text-[26px] font-black text-navy" style="font-family:'Courier New',monospace;letter-spacing:.3em">{{ $registrasi->kode_tes ?: '—' }}</p>
      </div>
      <span class="chip">{{ $ujian ? ($ujian->status === 'selesai' ? 'Tes: selesai — skor '.$ujian->skor : 'Tes: sedang berjalan') : 'Tes: belum dikerjakan' }}</span>
    </div>
    <p class="text-[13px] text-slate-600 mb-4">Kode ini juga tercetak di kartu tes. Simpan baik-baik — tanpa kode ini tes tidak bisa dibuka.</p>
    <div class="flex flex-wrap gap-3">
      <a href="{{ route('spmb.pendaftar.kartu') }}" class="bg-navy text-white rounded-xl px-5 py-2.5 font-semibold text-[13.5px]">Lihat Kartu Tes (Cetak)</a>
      <a href="{{ route('spmb.pendaftar.tes') }}" class="bg-emerald-600 text-white rounded-xl px-5 py-2.5 font-semibold text-[13.5px]">{{ $ujian?->status === 'selesai' ? 'Lihat Hasil Tes' : 'Buka Tes CBT' }}</a>
    </div>
  </div>
</x-seksi>
@endif

<x-seksi judul="Biodata Lengkap Siswa">
  @if (session('sukses'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-[13.5px] mb-5">{{ session('sukses') }}</div>
  @endif
  @if ($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-4 py-3 text-[13px] mb-5">
      <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="flex flex-wrap items-center gap-3 mb-5 text-[13px]">
    <span class="chip">Kelengkapan biodata: {{ $biodata->persen }}%</span>
    <span class="text-slate-500">Data ini dipakai panitia untuk kartu tes dan berkas pendaftaran — isi seakurat mungkin.</span>
  </div>

  <form action="{{ route('spmb.pendaftar.biodata') }}" method="post" enctype="multipart/form-data" class="kartu p-6 md:p-8 grid md:grid-cols-2 gap-4 text-[13.5px]">
    @csrf
    <div><label class="block mb-1.5 font-medium text-navy">NIK</label><input name="nik" value="{{ old('nik', $biodata->nik) }}" placeholder="16 angka" inputmode="numeric" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">NISN</label><input name="nisn" value="{{ old('nisn', $biodata->nisn) }}" placeholder="8-12 angka" inputmode="numeric" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Tempat lahir</label><input name="tempat_lahir" value="{{ old('tempat_lahir', $biodata->tempat_lahir) }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Tanggal lahir</label><input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $biodata->tanggal_lahir?->format('Y-m-d')) }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div class="md:col-span-2"><label class="block mb-1.5 font-medium text-navy">Alamat lengkap</label><textarea name="alamat" rows="2" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5">{{ old('alamat', $biodata->alamat) }}</textarea></div>
    <div><label class="block mb-1.5 font-medium text-navy">Anak ke-</label><input name="anak_ke" value="{{ old('anak_ke', $biodata->anak_ke) }}" inputmode="numeric" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Jumlah saudara</label><input name="jumlah_saudara" value="{{ old('jumlah_saudara', $biodata->jumlah_saudara) }}" inputmode="numeric" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Nama ayah</label><input name="nama_ayah" value="{{ old('nama_ayah', $biodata->nama_ayah) }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Pekerjaan ayah</label><input name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $biodata->pekerjaan_ayah) }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Nama ibu</label><input name="nama_ibu" value="{{ old('nama_ibu', $biodata->nama_ibu) }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Pekerjaan ibu</label><input name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $biodata->pekerjaan_ibu) }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Telepon rumah (opsional)</label><input name="telepon_rumah" value="{{ old('telepon_rumah', $biodata->telepon_rumah) }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Hobi (opsional)</label><input name="hobi" value="{{ old('hobi', $biodata->hobi) }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div class="md:col-span-2"><label class="block mb-1.5 font-medium text-navy">Foto siswa (opsional, maks 4 MB)</label>
      <input type="file" name="foto" accept="image/*" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-600" style="background:#f8fafd">
      @if ($biodata->foto)
        <p class="text-[12px] text-slate-500 mt-1.5">Sudah ada foto tersimpan — unggah baru untuk mengganti.</p>
      @endif
    </div>
    <div class="md:col-span-2">
      <button class="bg-navy text-white rounded-xl px-7 py-3 font-bold">Simpan Biodata</button>
    </div>
  </form>
</x-seksi>
<x-seksi judul="Data Formulir Anda">
  <div class="kartu overflow-hidden max-w-xl">
    <table class="w-full text-[13.5px]"><tbody>
      <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500 w-44">Nomor pendaftaran</td><td class="px-4 py-2.5 font-semibold text-navy">{{ $registrasi->nomor }}</td></tr>
      <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500">Nama wali</td><td class="px-4 py-2.5">{{ $registrasi->nama_wali }}</td></tr>
      <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500">Jalur</td><td class="px-4 py-2.5">{{ $registrasi->minat_program ?: '-' }}</td></tr>
      <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500">Tahun ajaran</td><td class="px-4 py-2.5">{{ $registrasi->tahun_ajaran }}</td></tr>
      <tr class="border-b border-slate-100"><td class="px-4 py-2.5 text-slate-500">Status</td><td class="px-4 py-2.5"><span class="chip">{{ \App\Models\Registration::STATUS[$registrasi->status] ?? $registrasi->status }}</span></td></tr>
    </tbody></table>
  </div>
  <form action="{{ route('spmb.pendaftar.keluar') }}" method="post" class="mt-5">@csrf<button class="border border-slate-300 text-slate-600 rounded-xl px-5 py-2.5 font-semibold text-[13.5px]">Keluar dari Area Pendaftar</button></form>
</x-seksi>
@endsection
