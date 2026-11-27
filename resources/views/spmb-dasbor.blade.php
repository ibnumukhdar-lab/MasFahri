@extends('layouts.publik')
@section('judul', 'Dashboard Pendaftar — SPMB SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'Area Pendaftar SPMB', 'judul' => 'Halo, ' . $registrasi->nama_siswa . ' 👋', 'sub' => 'Nomor pendaftaran '.$registrasi->nomor.' — status: '.(\App\Models\Registration::STATUS[$registrasi->status] ?? $registrasi->status).'. Lengkapi formulir dan biodata di bawah ini.'])
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

@if ($formulir->terkirim_pada || in_array($registrasi->status, ['dihubungi', 'lengkapi_formulir', 'tes'], true))
<span id="formulir"></span>
<x-seksi judul="Formulir Pendaftaran Murid Baru">
  @if (session('sukses'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-[13.5px] mb-5">{{ session('sukses') }}</div>
  @endif
  @if ($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-4 py-3 text-[13px] mb-5">
      <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  @if (! $formulir->terkirim_pada)
    <div class="rounded-xl px-5 py-4 mb-5 text-[13.5px] leading-relaxed" style="background:#fffbe8;border:1px solid #f2e3ac;color:#5b4a00">
      <b>Lengkapi Formulir Pendaftaran Murid Baru</b> seperti formulir resmi sekolah — isi Data Pribadi dan Data Orang Tua/Wali, lalu unggah <b>pas foto latar merah</b>.
      Belum siap semua? Tekan <b>Simpan Data</b> dulu (draf) — misalnya bila perlu meninggalkan halaman untuk memproses foto. Saat sudah lengkap, tekan <b>Simpan &amp; Serahkan</b>: semua kolom wajib terisi (tidak boleh kosong) dan status Anda <b>otomatis masuk Tahap tes</b>.
    </div>
  @endif

  <div class="kartu p-6 md:p-7 mb-5 max-w-2xl" style="box-shadow:0 18px 40px -32px rgba(31,58,95,.5)">
    <div class="grid md:grid-cols-2 gap-x-8 gap-y-2 text-[13px]">
      <p><span class="text-slate-500">No. Formulir · </span><b class="text-navy">{{ $registrasi->nomor }}</b></p>
      <p><span class="text-slate-500">Tahun Pelajaran · </span><b class="text-navy">{{ $registrasi->tahun_ajaran }}</b></p>
      <p><span class="text-slate-500">Nama lengkap · </span><b class="text-navy">{{ $registrasi->nama_siswa }}</b></p>
      <p><span class="text-slate-500">Jenis kelamin · </span><b class="text-navy">{{ $registrasi->jenis_kelamin ?: '—' }}</b></p>
      <p><span class="text-slate-500">Asal sekolah · </span><b class="text-navy">{{ $registrasi->sekolah_asal ?: '—' }}</b></p>
      <p><span class="text-slate-500">Status formulir · </span>
        @if ($formulir->terkirim_pada)
          <b class="text-emerald-700">Diserahkan {{ $formulir->terkirim_pada->format('d M Y H:i') }}</b>
        @elseif ($formulir->disimpan_pada)
          <b style="color:#a06a00">Draf tersimpan {{ $formulir->disimpan_pada->format('d M Y H:i') }} — belum diserahkan</b>
        @else
          <b style="color:#a06a00">Belum diisi</b>
        @endif
      </p>
    </div>
  </div>

  <form action="{{ route('spmb.pendaftar.formulir') }}" method="post" enctype="multipart/form-data" class="kartu p-6 md:p-8 grid md:grid-cols-2 gap-4 text-[13.5px]">
    @csrf
    <p class="md:col-span-2 text-[11.5px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">I. Data Pribadi</p>

    <div><label class="block mb-1.5 font-medium text-navy">Nama panggilan</label><input name="nama_panggilan" value="{{ old('nama_panggilan', $formulir->nama_panggilan) }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">NISN <span class="text-slate-400 font-normal">(10 angka)</span></label><input name="nisn" value="{{ old('nisn', $formulir->nisn ?: $biodata->nisn) }}" required inputmode="numeric" maxlength="10" placeholder="10 digit angka" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">NIK <span class="text-slate-400 font-normal">(16 angka)</span></label><input name="nik" value="{{ old('nik', $formulir->nik ?: $biodata->nik) }}" required inputmode="numeric" maxlength="16" placeholder="16 digit angka" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Golongan darah</label>
      <select name="golongan_darah" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-600">
        <option value="">— Pilih —</option>
        @foreach (['A', 'B', 'AB', 'O', '-'] as $gol)
          <option value="{{ $gol }}" @selected(old('golongan_darah', $formulir->golongan_darah) === $gol)>{{ $gol === '-' ? 'Tidak tahu / belum cek' : $gol }}</option>
        @endforeach
      </select>
    </div>
    <div><label class="block mb-1.5 font-medium text-navy">Tempat lahir</label><input name="tempat_lahir" value="{{ old('tempat_lahir', $formulir->tempat_lahir ?: $biodata->tempat_lahir) }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Tanggal lahir</label><input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', ($formulir->tanggal_lahir ?? $biodata->tanggal_lahir)?->format('Y-m-d')) }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Suku bangsa</label><input name="suku_bangsa" value="{{ old('suku_bangsa', $formulir->suku_bangsa) }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Anak ke- … dari … bersaudara</label>
      <div class="flex items-center gap-2">
        <input name="anak_ke" value="{{ old('anak_ke', $formulir->anak_ke ?: $biodata->anak_ke) }}" required inputmode="numeric" placeholder="Anak ke" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5">
        <span class="text-slate-400">dari</span>
        <input name="dari_bersaudara" value="{{ old('dari_bersaudara', $formulir->dari_bersaudara ?: $biodata->jumlah_saudara) }}" required inputmode="numeric" placeholder="Jumlah" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5">
      </div>
    </div>
    <div class="md:col-span-2"><label class="block mb-1.5 font-medium text-navy">Alamat — Jalan</label><input name="alamat_jalan" value="{{ old('alamat_jalan', $formulir->alamat_jalan) }}" required placeholder="Contoh: Jalan Setia Usaha No. 04" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Kelurahan/desa</label><input name="kelurahan" value="{{ old('kelurahan', $formulir->kelurahan) }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Kecamatan</label><input name="kecamatan" value="{{ old('kecamatan', $formulir->kecamatan) }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>

    <p class="md:col-span-2 text-[11.5px] font-bold uppercase tracking-wider text-slate-500 mt-3 mb-0.5">II. Data Orang Tua / Wali Murid</p>

    <p class="md:col-span-2 font-semibold text-navy">1. Ayah</p>
    <div><label class="block mb-1.5 font-medium text-navy">Nama ayah</label><input name="ayah_nama" value="{{ old('ayah_nama', $formulir->ayah_nama ?: $biodata->nama_ayah) }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Tempat tanggal lahir</label><input name="ayah_ttl" value="{{ old('ayah_ttl', $formulir->ayah_ttl) }}" required placeholder="Contoh: Sampit, 12 Mei 1980" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Pendidikan terakhir</label><input name="ayah_pendidikan" value="{{ old('ayah_pendidikan', $formulir->ayah_pendidikan) }}" required placeholder="Contoh: SMA / S1" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Pekerjaan</label><input name="ayah_pekerjaan" value="{{ old('ayah_pekerjaan', $formulir->ayah_pekerjaan ?: $biodata->pekerjaan_ayah) }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div class="md:col-span-2"><label class="block mb-1.5 font-medium text-navy">Penghasilan per bulan</label>
      <div class="flex flex-wrap gap-x-5 gap-y-2 text-[13px] text-slate-600">
        @foreach (['<2jt' => 'a. &lt; Rp 2.000.000', '2-5jt' => 'b. Rp 2.000.000 – Rp 5.000.000', '>5jt' => 'c. &gt; Rp 5.000.000'] as $val => $lab)
          <label class="inline-flex items-center gap-1.5"><input type="radio" name="ayah_penghasilan" value="{{ $val }}" @checked(old('ayah_penghasilan', $formulir->ayah_penghasilan) === $val) required> {!! $lab !!}</label>
        @endforeach
      </div>
    </div>
    <div><label class="block mb-1.5 font-medium text-navy">Nomor telepon/HP</label><input name="ayah_telepon" value="{{ old('ayah_telepon', $formulir->ayah_telepon) }}" required inputmode="tel" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>

    <p class="md:col-span-2 font-semibold text-navy mt-2">2. Ibu</p>
    <div><label class="block mb-1.5 font-medium text-navy">Nama ibu</label><input name="ibu_nama" value="{{ old('ibu_nama', $formulir->ibu_nama ?: $biodata->nama_ibu) }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Tempat tanggal lahir</label><input name="ibu_ttl" value="{{ old('ibu_ttl', $formulir->ibu_ttl) }}" required placeholder="Contoh: Sampit, 3 Maret 1983" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Pendidikan terakhir</label><input name="ibu_pendidikan" value="{{ old('ibu_pendidikan', $formulir->ibu_pendidikan) }}" required placeholder="Contoh: SMA / S1" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div><label class="block mb-1.5 font-medium text-navy">Pekerjaan</label><input name="ibu_pekerjaan" value="{{ old('ibu_pekerjaan', $formulir->ibu_pekerjaan ?: $biodata->pekerjaan_ibu) }}" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>
    <div class="md:col-span-2"><label class="block mb-1.5 font-medium text-navy">Penghasilan per bulan</label>
      <div class="flex flex-wrap gap-x-5 gap-y-2 text-[13px] text-slate-600">
        @foreach (['<2jt' => 'a. &lt; Rp 2.000.000', '2-5jt' => 'b. Rp 2.000.000 – Rp 5.000.000', '>5jt' => 'c. &gt; Rp 5.000.000'] as $val => $lab)
          <label class="inline-flex items-center gap-1.5"><input type="radio" name="ibu_penghasilan" value="{{ $val }}" @checked(old('ibu_penghasilan', $formulir->ibu_penghasilan) === $val) required> {!! $lab !!}</label>
        @endforeach
      </div>
    </div>
    <div><label class="block mb-1.5 font-medium text-navy">Nomor telepon/HP</label><input name="ibu_telepon" value="{{ old('ibu_telepon', $formulir->ibu_telepon) }}" required inputmode="tel" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5"></div>

    <div class="md:col-span-2"><label class="block mb-1.5 font-medium text-navy">Pas foto latar merah <span class="text-slate-400 font-normal">(wajib saat diserahkan — JPG/PNG, maks 4 MB)</span></label>
      <input type="file" name="foto" accept="image/*" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-600" style="background:#f8fafd">
      @if ($formulir->foto_latar_merah)
        <p class="text-[12px] text-slate-500 mt-1.5">Foto sudah tersimpan — unggah baru untuk mengganti. <a href="{{ $formulir->foto_url }}" target="_blank" class="text-navy font-semibold underline">Lihat foto</a></p>
      @else
        <p class="text-[12px] text-slate-500 mt-1.5">Foto formal siswa dengan <b class="text-navy">latar belakang merah</b>. Boleh disimpan draf dulu bila foto belum siap.</p>
      @endif
    </div>
    <div class="md:col-span-2 flex flex-wrap gap-3 items-center">
      <button name="aksi" value="simpan" formnovalidate class="rounded-xl px-6 py-3 font-semibold text-[13.5px]" style="background:#fff;color:#1f3a5f;border:1px solid #c6d8ea">Simpan Data (draf)</button>
      <button name="aksi" value="serahkan" class="bg-navy text-white rounded-xl px-7 py-3 font-bold">Simpan &amp; Serahkan</button>
      <span class="text-[12px] text-slate-500">"Serahkan" wajib lengkap semua kolom + pas foto — status otomatis ke Tahap tes.</span>
    </div>
  </form>
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
