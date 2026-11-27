@extends('layouts.publik')
@section('judul', 'Dashboard Pendaftar — SPMB SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'Area Pendaftar SPMB', 'judul' => 'Halo, ' . $registrasi->nama_siswa . ' 👋', 'sub' => 'Nomor pendaftaran '.$registrasi->nomor.' — status: '.(\App\Models\Registration::STATUS[$registrasi->status] ?? $registrasi->status).'. Lengkapi formulir dan biodata di bawah ini.'])

@php
  $kolPribadi = ['nama_panggilan', 'nisn', 'nik', 'golongan_darah', 'tempat_lahir', 'tanggal_lahir', 'suku_bangsa', 'anak_ke', 'dari_bersaudara', 'alamat_jalan', 'kelurahan', 'kecamatan'];
  $kolOrtu = ['ayah_nama', 'ayah_ttl', 'ayah_pendidikan', 'ayah_pekerjaan', 'ayah_penghasilan', 'ayah_telepon', 'ibu_nama', 'ibu_ttl', 'ibu_pendidikan', 'ibu_pekerjaan', 'ibu_penghasilan', 'ibu_telepon'];
  $isiPribadi = collect($kolPribadi)->filter(fn ($k) => filled($formulir->{$k}))->count();
  $isiOrtu = collect($kolOrtu)->filter(fn ($k) => filled($formulir->{$k}))->count();
  $adaFoto = filled($formulir->foto_latar_merah);
  $bobotForm = count($kolPribadi) + count($kolOrtu) + 1;
  $isiForm = $isiPribadi + $isiOrtu + ($adaFoto ? 1 : 0);
  $persenTotal = (int) round(100 * $isiForm / $bobotForm);
  $formulirTampil = $formulir->terkirim_pada || in_array($registrasi->status, ['dihubungi', 'lengkapi_formulir', 'tes'], true);
  $statusTiles = [];
  if ($formulirTampil) {
      $statusTiles[] = ['judul' => 'Biodata anak', 'ket' => 'Identitas calon murid', 'isi' => $isiPribadi, 'total' => count($kolPribadi), 'anchor' => '#data-pribadi'];
      $statusTiles[] = ['judul' => 'Foto anak', 'ket' => 'Latar merah — wajib', 'isi' => $adaFoto ? 1 : 0, 'total' => 1, 'anchor' => '#foto-anak'];
      $statusTiles[] = ['judul' => 'Biodata orang tua', 'ket' => 'Ayah & ibu / wali', 'isi' => $isiOrtu, 'total' => count($kolOrtu), 'anchor' => '#data-orang-tua'];
  }
@endphp

<section class="wrap pt-8">
  <div class="kartu p-5 md:p-6" style="box-shadow:0 22px 44px -36px rgba(31,58,95,.55)">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <p class="text-[11px] font-bold uppercase tracking-[.16em] text-slate-500 mb-1">Progres pengisian</p>
        <h2 class="text-[20px] md:text-[23px] font-extrabold text-navy">Yang sudah &amp; belum diisi</h2>
      </div>
      <div class="text-right">
        <p class="text-[24px] font-black text-navy leading-none">{{ $persenTotal }}%</p>
        <p class="text-[11.5px] text-slate-500">formulir pendaftaran</p>
      </div>
    </div>
    <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden my-4">
      <div class="h-full rounded-full" style="width:{{ $persenTotal }}%;background:linear-gradient(90deg,#1f3a5f 0%,#3d5987 100%)"></div>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
      @foreach ($statusTiles as $tile)
        @php $lengkap = $tile['isi'] >= $tile['total']; $persenTile = (int) round(100 * $tile['isi'] / $tile['total']); @endphp
        <a href="{{ $tile['anchor'] }}" class="block rounded-2xl p-3.5" style="border:1px solid {{ $lengkap ? '#bfe8cf' : '#e7eef6' }};background:{{ $lengkap ? '#f4fbf6' : '#fff' }}">
          <div class="flex items-center justify-between gap-2 mb-1">
            <span class="text-[13px] font-bold text-navy">{{ $tile['judul'] }}</span>
            @if ($lengkap)
              <span class="w-5 h-5 rounded-full grid place-items-center" style="background:#22a35a"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"/></svg></span>
            @else
              <span class="w-5 h-5 rounded-full grid place-items-center" style="border:1px solid #c6d8ea;color:#7c8ba1;font-size:10.5px;font-weight:800">!</span>
            @endif
          </div>
          <p class="text-[11.5px] text-slate-500 mb-2">{{ $tile['ket'] }}</p>
          <div class="flex items-center justify-between text-[11.5px] mb-1">
            <span class="font-semibold" style="color:{{ $lengkap ? '#177a43' : '#5b6470' }}">{{ $tile['isi'] }}/{{ $tile['total'] }} {{ $lengkap ? 'lengkap' : 'terisi' }}</span>
            @if (! $lengkap)<span class="text-slate-400">klik untuk isi</span>@endif
          </div>
          <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
            <div class="h-full" style="width:{{ $persenTile }}%;background:{{ $lengkap ? '#22a35a' : '#fdd100' }}"></div>
          </div>
        </a>
      @endforeach
    </div>
    <p class="text-[11.5px] text-slate-400 mt-3">Dihitung dari data tersimpan — tekan "Simpan Data" agar isian baru ikut terhitung.</p>
  </div>
</section>
@if ($registrasi->status === 'tes' || $ujian)
<x-seksi judul="Kartu Tes & Tes CBT" id="kartu">
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
<x-seksi judul="Formulir Pendaftaran Murid Baru" id="formulir">
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
      <b>Lengkapi Formulir Pendaftaran Murid Baru</b> seperti formulir resmi sekolah — mulai dari <b>Biodata Lengkap Anak (dengan pas foto)</b>, lalu <b>Biodata Orang Tua/Wali</b>.
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
    <p id="data-pribadi" class="md:col-span-2 scroll-mt-24 flex items-center justify-between gap-3 text-[11.5px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">
      <span>1. Biodata Lengkap Anak (Siswa)</span>
      <span class="chip" style="text-transform:none;letter-spacing:0">{{ $isiPribadi }}/{{ count($kolPribadi) }} terisi</span>
    </p>

    <div id="foto-anak" class="md:col-span-2 scroll-mt-24 rounded-2xl p-4" style="background:#f8fafd;border:1px dashed #c6d8ea">
      <div class="flex flex-col sm:flex-row gap-4 items-start">
        <div class="flex-none">
          <img id="foto-pratinjau" src="{{ $formulir->foto_latar_merah ? $formulir->foto_url : '' }}" alt="Pratinjau pas foto anak" class="rounded-xl border object-cover" style="width:96px;height:128px;border-color:#dbe7f4;{{ $formulir->foto_latar_merah ? '' : 'display:none' }}">
          <div id="foto-kosong" class="rounded-xl border grid place-items-center text-center" style="width:96px;height:128px;border-color:#dbe7f4;background:#fff;color:#94a3b8;font-size:10.5px;line-height:1.35;{{ $formulir->foto_latar_merah ? 'display:none' : '' }}">Belum ada<br>foto</div>
        </div>
        <div class="flex-1 min-w-0">
          <label class="block mb-1.5 font-medium text-navy">Pas foto anak (latar merah) <span class="text-slate-400 font-normal">— wajib saat diserahkan, JPG/PNG maks 4 MB</span></label>
          <input type="file" name="foto" accept="image/*" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-[13px] text-slate-600" style="background:#fff">
          <p class="text-[12px] text-slate-500 mt-1.5">Hanya <b class="text-navy">foto calon murid</b> — foto orang tua/wali tidak perlu. Pratinjau muncul otomatis setelah foto dipilih. Boleh simpan draf dulu bila foto belum siap.</p>
        </div>
      </div>
    </div>

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

    <p id="data-orang-tua" class="md:col-span-2 scroll-mt-24 flex items-center justify-between gap-3 text-[11.5px] font-bold uppercase tracking-wider text-slate-500 mt-3 mb-0.5">
      <span>2. Biodata Orang Tua / Wali Murid</span>
      <span class="chip" style="text-transform:none;letter-spacing:0">{{ $isiOrtu }}/{{ count($kolOrtu) }} terisi</span>
    </p>

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

    <div class="md:col-span-2 flex flex-wrap gap-3 items-center">
      <button name="aksi" value="simpan" formnovalidate class="rounded-xl px-6 py-3 font-semibold text-[13.5px]" style="background:#fff;color:#1f3a5f;border:1px solid #c6d8ea">Simpan Data (draf)</button>
      <button name="aksi" value="serahkan" class="bg-navy text-white rounded-xl px-7 py-3 font-bold">Simpan &amp; Serahkan</button>
      <span class="text-[12px] text-slate-500">"Serahkan" wajib lengkap semua kolom + pas foto — status otomatis ke Tahap tes.</span>
    </div>
  </form>

  <script>
    (function () {
      var input = document.querySelector('#foto-anak input[type=file]');
      var img = document.getElementById('foto-pratinjau');
      var kosong = document.getElementById('foto-kosong');
      if (!input || !img) return;
      input.addEventListener('change', function () {
        var f = input.files && input.files[0];
        if (!f) return;
        var r = new FileReader();
        r.onload = function (e) {
          img.src = e.target.result;
          img.style.display = 'block';
          if (kosong) kosong.style.display = 'none';
        };
        r.readAsDataURL(f);
      });
    })();
  </script>
</x-seksi>
@endif

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
