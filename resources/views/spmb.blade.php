@extends('layouts.publik')
@section('judul', 'SPMB — Penerimaan Murid Baru SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'Penerimaan Murid Baru ' . $tahunAjaran, 'judul' => 'SPMB Gelombang 1 Telah Dibuka', 'sub' => 'Mulai <b>1 Desember 2026 s/d Februari 2027</b> — kesempatan pertama masuk SMA IT Arafah tahun ajaran ' . $tahunAjaran . '. Tes penerimaan pertama: <b>Senin, 14 Desember 2026</b>. Seluruh murid wajib boarding.'])
<x-seksi judul="Jadwal SPMB Gelombang 1">
  <div class="mb-5">
    <p class="text-slate-600 text-[14.5px] md:text-[15.5px] leading-relaxed max-w-3xl mb-2.5">
      Persiapkan langkah terbaik untuk buah hati Anda: <b class="text-navy">Pendaftaran Siswa Baru SMA IT Arafah Tahun Ajaran {{ $tahunAjaran }}</b> resmi dibuka lewat Gelombang 1.
      Ini kesempatan pertama — dan terbaik — untuk mengamankan kursi di sekolah Islam terpadu yang membina iman, hafalan Al-Qur'an, dan prestasi akademik sekaligus.
    </p>
    <p class="text-slate-600 text-[14.5px] md:text-[15.5px] leading-relaxed max-w-3xl">
      Calon pendaftar Gelombang 1 mendapat kepastian lebih cepat: tes dilaksanakan lebih awal, hasil seleksi lebih cepat diketahui, dan kursi boarding tidak perlu diperebutkan dengan antrean Gelombang 2. Dua tanggal penting di bawah ini adalah kuncinya —
    </p>
  </div>
  <div class="grid md:grid-cols-2 gap-4 mb-5">
    <div class="kartu p-6 flex gap-4 items-start">
      <span aria-hidden="true" class="flex-none grid place-items-center w-12 h-12 rounded-xl bg-navy text-white text-xl">🗓️</span>
      <div>
        <p class="text-[12px] font-bold tracking-wide uppercase text-slate-500 mb-1">Pembukaan pendaftaran</p>
        <p class="font-extrabold text-navy text-[17px] leading-snug mb-1.5">SPMB Gelombang 1: 1 Desember 2026 s/d Februari 2027</p>
        <p class="text-slate-600 text-[13.5px] leading-relaxed">Pendaftaran dibuka mulai <b>1 Desember 2026</b> sampai <b>Februari 2027</b>. Isi formulir di bawah kapan saja dalam periode ini — semakin awal mendaftar, semakin cepat masuk proses seleksi.</p>
      </div>
    </div>
    <div class="kartu p-6 flex gap-4 items-start">
      <span aria-hidden="true" class="flex-none grid place-items-center w-12 h-12 rounded-xl bg-navy text-white text-xl">📝</span>
      <div>
        <p class="text-[12px] font-bold tracking-wide uppercase text-slate-500 mb-1">Tes penerimaan pertama</p>
        <p class="font-extrabold text-navy text-[17px] leading-snug mb-1.5">14 Desember 2026 — Potensi Akademik &amp; Minat Bakat</p>
        <p class="text-slate-600 text-[13.5px] leading-relaxed">Tes penerimaan pertama dilaksanakan pada <b>Senin, 14 Desember 2026</b>, mencakup tes <b>Potensi Akademik</b> dan <b>Minat Bakat</b>. Peserta akan dihubungi panitia lewat WhatsApp setelah pendaftaran terverifikasi.</p>
      </div>
    </div>
  </div>
  <div class="bg-navy-soft border border-navy-soft rounded-xl px-5 py-4 text-[13.5px] text-slate-700 leading-relaxed max-w-3xl">
    <b>Catatan:</b> setelah 14 Desember, tes berikutnya dijadwalkan berkelanjutan sampai kuota Gelombang 1 terisi — ikuti arahan panitia yang dikirim lewat WhatsApp.
  </div>
</x-seksi>
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
    <div><label class="block mb-1.5 font-medium text-navy">Jalur Pendaftaran</label>
      <select name="minat_program" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-600">
        <option value="">— Pilih jalur —</option>
        <option value="Jalur Reguler" @selected(old('minat_program') === 'Jalur Reguler')>Jalur Reguler</option>
        <option value="Jalur Prestasi" @selected(old('minat_program') === 'Jalur Prestasi')>Jalur Prestasi</option>
        <option value="Jalur Penghafal Al-Quran" @selected(old('minat_program') === 'Jalur Penghafal Al-Quran')>Jalur Penghafal Al-Quran</option>
        <option value="Jalur Tes Bahasa Arab dan Baca Kitab" @selected(old('minat_program') === 'Jalur Tes Bahasa Arab dan Baca Kitab')>Jalur Tes Bahasa Arab dan Baca Kitab</option>
      </select>
      <p class="text-[12px] text-slate-500 mt-1.5">Semua murid <b>wajib boarding</b> (berasrama). Tidak ada pilihan non-boarding.</p>
    </div>
    <div><label class="block mb-1.5 font-medium text-navy">Tahun Ajaran</label>
      <select name="tahun_ajaran" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-600">
        @foreach ($pilihanTahun as $th)
          <option value="{{ $th }}" @selected(old('tahun_ajaran', $tahunAjaran) === $th)>{{ $th }}</option>
        @endforeach
      </select>
      <p class="text-[12px] text-slate-500 mt-1.5">Pilih tahun ajaran yang dituju.</p>
    </div>
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
