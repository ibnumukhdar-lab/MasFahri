@extends('layouts.publik')
@section('judul', 'Dashboard Pendaftar — SPMB SMA IT Arafah')
@section('isi')
@include('partials.hero', ['kecil' => 'Area Pendaftar SPMB', 'judul' => 'Halo, ' . $registrasi->nama_siswa . ' 👋', 'sub' => 'Nomor pendaftaran '.$registrasi->nomor.' — status: '.(\App\Models\Registration::STATUS[$registrasi->status] ?? $registrasi->status).'. Lengkapi formulir dan biodata di bawah ini.'])

@php
  $kolPribadi = ['nama_panggilan', 'nisn', 'nik', 'golongan_darah', 'tempat_lahir', 'tanggal_lahir', 'suku_bangsa', 'anak_ke', 'dari_bersaudara', 'alamat_jalan', 'kelurahan', 'kecamatan'];
  $kolAyah = ['ayah_nama', 'ayah_ttl', 'ayah_pendidikan', 'ayah_pekerjaan', 'ayah_penghasilan', 'ayah_telepon'];
  $kolIbu = ['ibu_nama', 'ibu_ttl', 'ibu_pendidikan', 'ibu_pekerjaan', 'ibu_penghasilan', 'ibu_telepon'];
  $kolOrtu = array_merge($kolAyah, $kolIbu);
  $isiPribadi = collect($kolPribadi)->filter(fn ($k) => filled($formulir->{$k}))->count();
  $isiAyah = collect($kolAyah)->filter(fn ($k) => filled($formulir->{$k}))->count();
  $isiIbu = collect($kolIbu)->filter(fn ($k) => filled($formulir->{$k}))->count();
  $isiOrtu = $isiAyah + $isiIbu;
  $adaFoto = filled($formulir->foto_latar_merah);
  $bobotForm = count($kolPribadi) + count($kolOrtu) + 1;
  $isiForm = $isiPribadi + $isiOrtu + ($adaFoto ? 1 : 0);
  $persenTotal = (int) round(100 * $isiForm / $bobotForm);
  $formulirTampil = $formulir->terkirim_pada || in_array($registrasi->status, ['dihubungi', 'lengkapi_formulir', 'tes'], true);
  $statusTiles = [];
  if ($formulirTampil) {
      $statusTiles[] = ['judul' => 'Biodata anak', 'ket' => 'Identitas calon murid', 'isi' => $isiPribadi, 'total' => count($kolPribadi), 'anchor' => '#data-pribadi', 'ikon' => 'anak'];
      $statusTiles[] = ['judul' => 'Foto anak', 'ket' => 'Latar merah — wajib', 'isi' => $adaFoto ? 1 : 0, 'total' => 1, 'anchor' => '#foto-anak', 'ikon' => 'foto'];
      $statusTiles[] = ['judul' => 'Biodata orang tua', 'ket' => 'Ayah & ibu / wali', 'isi' => $isiOrtu, 'total' => count($kolOrtu), 'anchor' => '#data-orang-tua', 'ikon' => 'ortu'];
  }
  $lingkar = 213.63;
@endphp

<style>
  /* ---- Gaya khusus halaman formulir pendaftar (scoped, tidak mengubah layout publik) ---- */
  .sf-label{display:block;font-size:12.5px;font-weight:600;color:#1f3a5f;margin:0 0 6px;line-height:1.4}
  .sf-wajib{color:#e0a400;font-size:15px;line-height:1;margin-left:2px}
  .sf-note{display:block;font-size:11.5px;font-weight:400;color:#94a3b8;margin-top:1px}
  .sf-input{display:block;width:100%;height:46px;border:1px solid #dfe8f1;border-radius:12px;background:#fff;
    padding:0 14px;font-size:15px;line-height:1.4;color:#1f2937;transition:border-color .15s,box-shadow .15s,background .15s}
  textarea.sf-input{height:auto;padding:12px 14px}
  .sf-input::placeholder{color:#a9b5c3;font-weight:400}
  .sf-input:hover{border-color:#c6d8ea;background:#fcfdff}
  .sf-input:focus{outline:none;border-color:#1f3a5f;background:#fff;box-shadow:0 0 0 4px rgba(31,58,95,.10)}
  .sf-input:valid:not(:placeholder-shown){border-color:#cfdded}
  .sf-kartu{background:#fff;border:1px solid #e7eef6;border-radius:20px;overflow:hidden;
    box-shadow:0 18px 44px -36px rgba(31,58,95,.55)}
  .sf-kepala{position:relative;overflow:hidden;color:#fff;
    background:linear-gradient(125deg,#1b3455 0%,#1f3a5f 45%,#2b5288 100%);padding:18px 22px}
  .sf-kepala::after{content:"";position:absolute;right:-46px;top:-58px;width:170px;height:170px;border-radius:50%;
    background:radial-gradient(circle,rgba(253,209,0,.20) 0%,rgba(253,209,0,0) 72%)}
  .sf-kepala::before{content:"";position:absolute;right:56px;bottom:-70px;width:120px;height:120px;border-radius:50%;
    border:1px solid rgba(255,255,255,.14)}
  .sf-eyebrow{font-size:10.5px;font-weight:800;letter-spacing:.18em;text-transform:uppercase;color:rgba(255,255,255,.62)}
  .sf-kepala-isi{position:relative;z-index:1}
  .sf-donat{position:relative;flex:none;width:78px;height:78px}
  .sf-donat svg{transform:rotate(-90deg)}
  .sf-donat-angka{position:absolute;inset:0;display:grid;place-items:center;font-size:19px;font-weight:800;color:#fff;letter-spacing:-.02em}
  .sf-sub{display:flex;align-items:center;gap:11px;flex-wrap:wrap;padding:14px 20px;border-bottom:1px solid #eef3f9;background:#fbfcfe}
  .sf-ava{flex:none;width:34px;height:34px;border-radius:11px;display:grid;place-items:center;font-size:12.5px;font-weight:800;
    color:#1f3a5f;background:#eef4fb;border:1px solid #dbe7f4}
  .sf-ava.emas{color:#7a5c00;background:#fff6d6;border-color:#f0e2ab}
  .sf-badan{padding:20px 20px 22px}
  .sf-grid{display:grid;grid-template-columns:1fr;gap:14px 16px}
  @media (min-width:768px){.sf-grid{grid-template-columns:1fr 1fr}}
  .sf-lebar{grid-column:1 / -1}
  .sf-foto{display:flex;flex-direction:column;gap:14px;align-items:flex-start;padding:15px;border-radius:16px;
    background:linear-gradient(180deg,#f8fbff 0%,#f4f8fd 100%);border:1px solid #e2ecf7}
  @media (min-width:640px){.sf-foto{flex-direction:row}}
  .sf-bingkai{flex:none;position:relative;width:99px;height:132px;border-radius:12px;overflow:hidden;background:#fff;
    border:1px dashed #c6d8ea;display:grid;place-items:center}
  .sf-bingkai img{width:100%;height:100%;object-fit:cover;display:block}
  .sf-bingkai-kosong{text-align:center;font-size:10.5px;line-height:1.35;color:#93a3b8;padding:0 4px}
  .sf-file{width:100%;font-size:13px;color:#5b6470;border:1px solid #dfe8f1;background:#fff;border-radius:12px;padding:7px 9px}
  .sf-file::file-selector-button{margin-right:10px;padding:8px 13px;border-radius:9px;border:1px solid #dbe7f4;
    background:#eef4fb;color:#1f3a5f;font-weight:700;font-size:12.5px;cursor:pointer;font-family:inherit}
  .sf-file::file-selector-button:hover{background:#e3edf9}
  .sf-pil{position:relative;display:inline-flex;align-items:center;gap:7px;cursor:pointer;min-width:0}
  .sf-pil input{position:absolute;opacity:0;width:1px;height:1px;margin:0}
  .sf-pil span{display:block;padding:10px 13px;border:1px solid #dfe8f1;border-radius:12px;background:#fff;
    font-size:13px;font-weight:500;color:#475569;transition:all .15s;white-space:nowrap}
  .sf-pil:hover span{border-color:#c6d8ea;background:#fbfdff}
  .sf-pil input:checked + span{border-color:#1f3a5f;background:#eef4fb;color:#1f3a5f;font-weight:700;
    box-shadow:inset 0 0 0 1px #1f3a5f}
  .sf-pil input:focus-visible + span{box-shadow:0 0 0 4px rgba(31,58,95,.12)}
  .sf-aksi{display:flex;gap:10px;flex-direction:column;padding:18px 20px 20px;background:#fbfcfe;border-top:1px solid #eef3f9}
  @media (min-width:640px){.sf-aksi{flex-direction:row;align-items:center}}
  .sf-tombol{display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:13px;font-weight:700;
    font-size:14px;padding:13px 22px;cursor:pointer;border:1px solid transparent;transition:all .15s;font-family:inherit}
  .sf-tombol-draf{background:#fff;color:#1f3a5f;border-color:#cfdeee}
  .sf-tombol-draf:hover{background:#f2f7fc;border-color:#b9cfe5}
  .sf-tombol-utama{background:linear-gradient(135deg,#1f3a5f 0%,#2b5288 100%);color:#fff;
    box-shadow:0 12px 24px -16px rgba(31,58,95,.9)}
  .sf-tombol-utama:hover{filter:brightness(1.06)}
  .sf-tombol svg{flex:none}
  .sf-tile{display:block;border-radius:16px;padding:14px;border:1px solid #e7eef6;background:#fff;transition:all .15s}
  .sf-tile:hover{border-color:#c6d8ea;transform:translateY(-1px);box-shadow:0 10px 24px -20px rgba(31,58,95,.7)}
  .sf-tile.ok{background:#f5fbf7;border-color:#bfe8cf}
  .sf-tile-ikon{flex:none;width:30px;height:30px;border-radius:10px;display:grid;place-items:center;background:#eef4fb;color:#1f3a5f}
  .sf-tile.ok .sf-tile-ikon{background:#dcf5e6;color:#14875a}
  .sf-info{display:flex;justify-content:space-between;gap:12px;padding:9px 0;border-bottom:1px dashed #eef3f9;font-size:13px}
  .sf-info:last-child{border-bottom:0}
  .sf-info dt{color:#7c8ba1;flex:none}
  .sf-info dd{font-weight:600;color:#1f3a5f;text-align:right;word-break:break-word}
  .sf-galgal{display:flex;flex-wrap:wrap;gap:8px}
  .sf-catatan{display:flex;gap:10px;font-size:12.5px;line-height:1.55;color:#5b4a00;background:#fffbe8;
    border:1px solid #f2e3ac;border-radius:13px;padding:12px 14px}
  .sf-catatan svg{flex:none;margin-top:1px;color:#b58a00}
  .sf-error{border-radius:13px;border:1px solid #f6c9d0;background:#fff5f6;padding:13px 15px;font-size:13px;color:#8d2233}
  .sf-sukses{border-radius:13px;border:1px solid #bfe8cf;background:#f4fbf6;padding:13px 15px;font-size:13px;color:#136b43}
</style>

<section class="wrap pt-8">
  <div class="sf-kartu max-w-3xl mx-auto">
    <div class="sf-kepala">
      <div class="sf-kepala-isi flex items-center gap-5 justify-between">
        <div class="min-w-0">
          <p class="sf-eyebrow">Ringkasan pendaftaran</p>
          <p class="text-[17px] md:text-[19px] font-extrabold mt-1 tracking-tight">{{ $registrasi->nomor }}</p>
          <div class="flex flex-wrap items-center gap-2 mt-2">
            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full" style="background:rgba(253,209,0,.16);color:#ffe483;border:1px solid rgba(253,209,0,.34)">
              <span class="w-1.5 h-1.5 rounded-full" style="background:#fdd100"></span>
              {{ \App\Models\Registration::STATUS[$registrasi->status] ?? $registrasi->status }}
            </span>
            <span class="text-[11px] text-white/70">Tahun pelajaran {{ $registrasi->tahun_ajaran }}</span>
          </div>
        </div>
        <div class="sf-donat" title="Progres pengisian formulir">
          <svg width="78" height="78" viewBox="0 0 78 78">
            <circle cx="39" cy="39" r="34" fill="none" stroke="rgba(255,255,255,.16)" stroke-width="7"></circle>
            <circle cx="39" cy="39" r="34" fill="none" stroke="#fdd100" stroke-width="7" stroke-linecap="round"
              stroke-dasharray="{{ $lingkar }}" stroke-dashoffset="{{ round($lingkar * (1 - $persenTotal / 100), 2) }}"></circle>
          </svg>
          <span class="sf-donat-angka">{{ $persenTotal }}%</span>
        </div>
      </div>
    </div>

    <div class="p-5 md:p-6">
      <div class="flex flex-wrap items-end justify-between gap-2 mb-3">
        <div>
          <p class="sf-eyebrow" style="color:#94a3b8">Progres pengisian</p>
          <h2 class="text-[18px] md:text-[20px] font-extrabold text-navy mt-0.5">Yang sudah &amp; belum diisi</h2>
        </div>
        <p class="text-[12px] text-slate-500">{{ $isiForm }} dari {{ $bobotForm }} kolom terisi</p>
      </div>
      <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden mb-5">
        <div class="h-full rounded-full transition-all" style="width:{{ $persenTotal }}%;background:{{ $persenTotal >= 100 ? 'linear-gradient(90deg,#14875a,#22a35a)' : 'linear-gradient(90deg,#1f3a5f,#3d5987 60%,#fdd100)' }}"></div>
      </div>

      @if ($statusTiles)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          @foreach ($statusTiles as $tile)
            @php $lengkap = $tile['isi'] >= $tile['total']; $persenTile = (int) round(100 * $tile['isi'] / $tile['total']); @endphp
            <a href="{{ $tile['anchor'] }}" class="sf-tile {{ $lengkap ? 'ok' : '' }}">
              <div class="flex items-center gap-3 mb-2.5">
                <span class="sf-tile-ikon">
                  @if ($tile['ikon'] === 'anak')
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.6"/><path d="M4.5 20c.6-4 3.6-6 7.5-6s6.9 2 7.5 6"/></svg>
                  @elseif ($tile['ikon'] === 'foto')
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="14" rx="3"/><circle cx="12" cy="13" r="3.4"/><path d="M8 6l1.4-2h5.2L16 6"/></svg>
                  @else
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8.5" r="3.2"/><path d="M3 19c.5-3.4 3-5.2 6-5.2s5.5 1.8 6 5.2"/><path d="M16 5.6a3 3 0 010 5.6"/><path d="M18 19c-.2-2-.9-3.6-2-4.7 2.9-.3 5 1.5 5 4.7z"/></svg>
                  @endif
                </span>
                <span class="text-[13px] font-bold text-navy flex-1 min-w-0">{{ $tile['judul'] }}</span>
                @if ($lengkap)
                  <span class="w-5 h-5 rounded-full grid place-items-center flex-none" style="background:#22a35a"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"/></svg></span>
                @endif
              </div>
              <p class="text-[11.5px] text-slate-500 mb-2">{{ $tile['ket'] }}</p>
              <div class="flex items-center justify-between text-[11.5px] mb-1.5">
                <span class="font-semibold" style="color:{{ $lengkap ? '#177a43' : '#5b6470' }}">{{ $tile['isi'] }}/{{ $tile['total'] }} {{ $lengkap ? 'lengkap' : 'terisi' }}</span>
                @if (! $lengkap)<span class="text-slate-400">klik untuk isi</span>@endif
              </div>
              <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full rounded-full" style="width:{{ $persenTile }}%;background:{{ $lengkap ? '#22a35a' : '#fdd100' }}"></div>
              </div>
            </a>
          @endforeach
        </div>
        <p class="text-[11.5px] text-slate-400 mt-3">Dihitung dari data tersimpan — tekan "Simpan Data" agar isian baru ikut terhitung.</p>
      @endif
    </div>
  </div>
</section>

@if ($registrasi->status === 'tes' || $ujian)
<section id="kartu" class="wrap py-12 md:py-14" style="scroll-margin-top:88px">
  <div class="max-w-3xl mx-auto">
    <h2 class="text-[22px] md:text-[27px] font-extrabold text-navy mb-2">Kartu Tes &amp; Tes CBT</h2>
    <div class="h-[3px] w-14 bg-navy-line rounded mb-7"></div>
  <div class="kartu overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-5" style="background:linear-gradient(125deg,#f8fbff,#eef4fb);border-bottom:1px solid #e7eef6">
      <div>
        <p class="sf-eyebrow" style="color:#7c8ba1">Kode login tes (CBT)</p>
        <p class="text-[24px] font-black text-navy mt-1" style="font-family:'Courier New',monospace;letter-spacing:.24em">{{ $registrasi->kode_tes ?: '—' }}</p>
      </div>
      <span class="chip">{{ $ujian ? ($ujian->status === 'selesai' ? 'Tes: selesai — skor '.$ujian->skor : 'Tes: sedang berjalan') : 'Tes: belum dikerjakan' }}</span>
    </div>
    <div class="p-6 md:p-7">
      <p class="text-[13px] text-slate-600 mb-4">Kode ini juga tercetak di kartu tes. Simpan baik-baik — tanpa kode ini tes tidak bisa dibuka.</p>
      <div class="sf-galgal">
        <a href="{{ route('spmb.pendaftar.kartu') }}" class="sf-tombol sf-tombol-draf">Lihat Kartu Tes (Cetak)</a>
        <a href="{{ route('spmb.pendaftar.tes') }}" class="sf-tombol sf-tombol-utama">{{ $ujian?->status === 'selesai' ? 'Lihat Hasil Tes' : 'Buka Tes CBT' }}</a>
      </div>
    </div>
  </div>
</div>
</section>
@endif

@if ($formulirTampil)
<section id="formulir" class="wrap py-12 md:py-14" style="scroll-margin-top:88px">
  <div class="max-w-3xl mx-auto">
    <h2 class="text-[22px] md:text-[27px] font-extrabold text-navy mb-2">Formulir Pendaftaran Murid Baru</h2>
    <div class="h-[3px] w-14 bg-navy-line rounded mb-7"></div>
  @if ($errors->any())
    <div class="sf-error mb-5">
      <p class="font-bold mb-1">Periksa isian berikut:</p>
      <ul class="list-disc pl-5 space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  @if ($formulir->terkirim_pada)
    <div class="sf-sukses mb-5 flex" items-start gap-2.5">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 flex-none"><path d="M4 12.5l5 5L20 6.5"/></svg>
      <span><b>Formulir sudah diserahkan</b> pada {{ $formulir->terkirim_pada->format('d M Y H:i') }}. Bila ada yang perlu diperbaiki, hubungi panitia — perubahan setelah diserahkan bisa memengaruhi jadwal tes.</span>
    </div>
  @else
    <div class="sf-catatan mb-5">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 10.5v6M12 7.6v.1"/></svg>
      <span>Isi seperti <b>formulir resmi sekolah</b>: biodata anak (dengan pas foto), lalu ayah dan ibu. Belum siap semua? Tekan <b>Simpan Data</b> dulu — isian tersimpan sebagai draf. Bila sudah lengkap, tekan <b>Simpan &amp; Serahkan</b>; status otomatis masuk <b>Tahap tes</b>.</span>
    </div>
  @endif

  <form action="{{ route('spmb.pendaftar.formulir') }}" method="post" enctype="multipart/form-data" class="space-y-5 max-w-3xl">
    @csrf

    {{-- ============ 1. BIODATA ANAK ============ --}}
    <div class="sf-kartu" id="data-pribadi" style="scroll-margin-top:88px">
      <div class="sf-sub">
        <span class="sf-ava">1</span>
        <div class="min-w-0 flex-1">
          <p class="text-[14.5px] font-extrabold text-navy leading-tight">Biodata lengkap anak (siswa)</p>
          <p class="text-[11.5px] text-slate-500">Sesuai akta kelahiran &amp; kartu keluarga</p>
        </div>
        <span class="chip" style="text-transform:none;letter-spacing:0">{{ $isiPribadi }}/{{ count($kolPribadi) }} terisi</span>
      </div>

      <div class="sf-badan">
        <div id="foto-anak" class="sf-foto mb-5" style="scroll-margin-top:88px">
          <div class="sf-bingkai">
            <img id="foto-pratinjau" src="{{ $formulir->foto_latar_merah ? $formulir->foto_url : '' }}" alt="Pratinjau pas foto anak" class="{{ $formulir->foto_latar_merah ? '' : 'hidden' }}">
            <div id="foto-kosong" class="sf-bingkai-kosong {{ $formulir->foto_latar_merah ? 'hidden' : '' }}">Belum ada<br>foto</div>
          </div>
          <div class="flex-1 min-w-0">
            <p class="sf-label">Pas foto anak <span class="sf-note">Latar merah · JPG/PNG maks 4 MB · hanya foto calon murid</span></p>
            <input type="file" name="foto" accept="image/*" class="sf-file">
            <p class="text-[12px] text-slate-500 mt-2 leading-relaxed">Pratinjau muncul otomatis setelah foto dipilih. Boleh <b class="text-navy">Simpan Data</b> dulu bila foto belum siap.</p>
          </div>
        </div>

        <div class="sf-grid">
          <x-spmb-field name="nama_panggilan" label="Nama panggilan" :value="old('nama_panggilan', $formulir->nama_panggilan)" req placeholder="Nama sehari-hari" />
          <x-spmb-field name="nisn" label="NISN" note="10 angka" :value="old('nisn', $formulir->nisn ?: $biodata->nisn)" req inputmode="numeric" maxlength="10" placeholder="10 digit angka" />
          <x-spmb-field name="nik" label="NIK" note="16 angka, sesuai KK" :value="old('nik', $formulir->nik ?: $biodata->nik)" req inputmode="numeric" maxlength="16" placeholder="16 digit angka" />
          <div>
            <label for="f-golongan_darah" class="sf-label">Golongan darah<span class="sf-wajib">&bull;</span></label>
            <select id="f-golongan_darah" name="golongan_darah" required class="sf-input">
              <option value="">— Pilih —</option>
              @foreach (['A', 'B', 'AB', 'O', '-'] as $gol)
                <option value="{{ $gol }}" @selected(old('golongan_darah', $formulir->golongan_darah) === $gol)>{{ $gol === '-' ? 'Tidak tahu / belum cek' : $gol }}</option>
              @endforeach
            </select>
          </div>
          <x-spmb-field name="tempat_lahir" label="Tempat lahir" :value="old('tempat_lahir', $formulir->tempat_lahir ?: $biodata->tempat_lahir)" req placeholder="Contoh: Sampit" />
          <x-spmb-field name="tanggal_lahir" type="date" label="Tanggal lahir" :value="old('tanggal_lahir', ($formulir->tanggal_lahir ?? $biodata->tanggal_lahir)?->format('Y-m-d'))" req />
          <x-spmb-field name="suku_bangsa" label="Suku bangsa" :value="old('suku_bangsa', $formulir->suku_bangsa)" req placeholder="Contoh: Dayak / Jawa / Banjar" />
          <div>
            <p class="sf-label">Anak ke &amp; jumlah saudara<span class="sf-wajib">&bull;</span></p>
            <div class="flex items-center gap-2">
              <input name="anak_ke" value="{{ old('anak_ke', $formulir->anak_ke ?: $biodata->anak_ke) }}" required inputmode="numeric" placeholder="Anak ke-" class="sf-input">
              <span class="text-[12px] text-slate-400 flex-none">dari</span>
              <input name="dari_bersaudara" value="{{ old('dari_bersaudara', $formulir->dari_bersaudara ?: $biodata->jumlah_saudara) }}" required inputmode="numeric" placeholder="Jumlah" class="sf-input">
            </div>
          </div>
          <x-spmb-field name="alamat_jalan" label="Alamat — jalan & nomor rumah" :value="old('alamat_jalan', $formulir->alamat_jalan)" req placeholder="Contoh: Jalan Setia Usaha No. 04" col="sf-lebar" />
          <x-spmb-field name="kelurahan" label="Kelurahan / desa" :value="old('kelurahan', $formulir->kelurahan)" req placeholder="Nama kelurahan" />
          <x-spmb-field name="kecamatan" label="Kecamatan" :value="old('kecamatan', $formulir->kecamatan)" req placeholder="Nama kecamatan" />
        </div>
      </div>
    </div>

    {{-- ============ 2. AYAH ============ --}}
    <div class="sf-kartu" id="data-orang-tua" style="scroll-margin-top:88px">
      <div class="sf-sub">
        <span class="sf-ava emas">2</span>
        <div class="min-w-0 flex-1">
          <p class="text-[14.5px] font-extrabold text-navy leading-tight">Data ayah</p>
          <p class="text-[11.5px] text-slate-500">Orang tua / wali murid</p>
        </div>
        <span class="chip" style="text-transform:none;letter-spacing:0">{{ $isiAyah }}/{{ count($kolAyah) }} terisi</span>
      </div>
      <div class="sf-badan">
        <div class="sf-grid">
          <x-spmb-field name="ayah_nama" label="Nama lengkap ayah" :value="old('ayah_nama', $formulir->ayah_nama ?: $biodata->nama_ayah)" req placeholder="Sesuai KTP" />
          <x-spmb-field name="ayah_ttl" label="Tempat, tanggal lahir" :value="old('ayah_ttl', $formulir->ayah_ttl)" req placeholder="Contoh: Sampit, 12 Mei 1980" />
          <x-spmb-field name="ayah_pendidikan" label="Pendidikan terakhir" :value="old('ayah_pendidikan', $formulir->ayah_pendidikan)" req placeholder="Contoh: SMA / S1" />
          <x-spmb-field name="ayah_pekerjaan" label="Pekerjaan" :value="old('ayah_pekerjaan', $formulir->ayah_pekerjaan ?: $biodata->pekerjaan_ayah)" req placeholder="Contoh: Karyawan swasta" />
          <div class="sf-lebar">
            <p class="sf-label">Penghasilan per bulan<span class="sf-wajib">&bull;</span></p>
            <div class="flex flex-wrap gap-2">
              @foreach (['<2jt' => 'a. &lt; Rp 2 juta', '2-5jt' => 'b. Rp 2–5 juta', '>5jt' => 'c. &gt; Rp 5 juta'] as $val => $lab)
                <label class="sf-pil"><input type="radio" name="ayah_penghasilan" value="{{ $val }}" @checked(old('ayah_penghasilan', $formulir->ayah_penghasilan) === $val) required><span>{!! $lab !!}</span></label>
              @endforeach
            </div>
          </div>
          <x-spmb-field name="ayah_telepon" label="Nomor telepon / HP" :value="old('ayah_telepon', $formulir->ayah_telepon)" req inputmode="tel" placeholder="08xxxxxxxxxx" />
        </div>
      </div>
    </div>

    {{-- ============ 3. IBU ============ --}}
    <div class="sf-kartu">
      <div class="sf-sub">
        <span class="sf-ava emas">3</span>
        <div class="min-w-0 flex-1">
          <p class="text-[14.5px] font-extrabold text-navy leading-tight">Data ibu</p>
          <p class="text-[11.5px] text-slate-500">Orang tua / wali murid</p>
        </div>
        <span class="chip" style="text-transform:none;letter-spacing:0">{{ $isiIbu }}/{{ count($kolIbu) }} terisi</span>
      </div>
      <div class="sf-badan">
        <div class="sf-grid">
          <x-spmb-field name="ibu_nama" label="Nama lengkap ibu" :value="old('ibu_nama', $formulir->ibu_nama ?: $biodata->nama_ibu)" req placeholder="Sesuai KTP" />
          <x-spmb-field name="ibu_ttl" label="Tempat, tanggal lahir" :value="old('ibu_ttl', $formulir->ibu_ttl)" req placeholder="Contoh: Sampit, 3 Maret 1983" />
          <x-spmb-field name="ibu_pendidikan" label="Pendidikan terakhir" :value="old('ibu_pendidikan', $formulir->ibu_pendidikan)" req placeholder="Contoh: SMA / S1" />
          <x-spmb-field name="ibu_pekerjaan" label="Pekerjaan" :value="old('ibu_pekerjaan', $formulir->ibu_pekerjaan ?: $biodata->pekerjaan_ibu)" req placeholder="Contoh: Ibu rumah tangga" />
          <div class="sf-lebar">
            <p class="sf-label">Penghasilan per bulan<span class="sf-wajib">&bull;</span></p>
            <div class="flex flex-wrap gap-2">
              @foreach (['<2jt' => 'a. &lt; Rp 2 juta', '2-5jt' => 'b. Rp 2–5 juta', '>5jt' => 'c. &gt; Rp 5 juta'] as $val => $lab)
                <label class="sf-pil"><input type="radio" name="ibu_penghasilan" value="{{ $val }}" @checked(old('ibu_penghasilan', $formulir->ibu_penghasilan) === $val) required><span>{!! $lab !!}</span></label>
              @endforeach
            </div>
          </div>
          <x-spmb-field name="ibu_telepon" label="Nomor telepon / HP" :value="old('ibu_telepon', $formulir->ibu_telepon)" req inputmode="tel" placeholder="08xxxxxxxxxx" />
        </div>
      </div>
    </div>

    {{-- ============ AKSI ============ --}}
    <div class="sf-kartu">
      <div class="sf-aksi flex-wrap">
        <div class="flex-1 min-w-[190px]">
          <p class="text-[13px] font-bold text-navy">Terisi {{ $isiForm }} dari {{ $bobotForm }} kolom ({{ $persenTotal }}%)</p>
          <p class="text-[11.5px] text-slate-500 mt-0.5">"Serahkan" wajib lengkap semua kolom + pas foto — status otomatis ke Tahap tes.</p>
        </div>
        <button type="submit" name="aksi" value="simpan" formnovalidate class="sf-tombol sf-tombol-draf">Simpan Data (draf)</button>
        <button type="submit" name="aksi" value="serahkan" class="sf-tombol sf-tombol-utama">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"/></svg>
          Simpan &amp; Serahkan
        </button>
      </div>
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
          img.classList.remove('hidden');
          if (kosong) kosong.classList.add('hidden');
        };
        r.readAsDataURL(f);
      });
    })();
  </script>
</div>
</section>
@endif

<section class="wrap py-12 md:py-14" style="scroll-margin-top:88px">
  <div class="max-w-3xl mx-auto">
    <h2 class="text-[22px] md:text-[27px] font-extrabold text-navy mb-2">Data Formulir Anda</h2>
    <div class="h-[3px] w-14 bg-navy-line rounded mb-7"></div>
  <div class="kartu overflow-hidden max-w-md">
    <dl class="px-5">
      <div class="sf-info"><dt>Nomor pendaftaran</dt><dd>{{ $registrasi->nomor }}</dd></div>
      <div class="sf-info"><dt>Nama wali</dt><dd>{{ $registrasi->nama_wali }}</dd></div>
      <div class="sf-info"><dt>Jalur</dt><dd>{{ $registrasi->minat_program ?: '-' }}</dd></div>
      <div class="sf-info"><dt>Tahun ajaran</dt><dd>{{ $registrasi->tahun_ajaran }}</dd></div>
      <div class="sf-info"><dt>Status</dt><dd><span class="chip">{{ \App\Models\Registration::STATUS[$registrasi->status] ?? $registrasi->status }}</span></dd></div>
    </dl>
  </div>
  <form action="{{ route('spmb.pendaftar.keluar') }}" method="post" class="mt-5">@csrf<button class="border border-slate-300 text-slate-600 rounded-xl px-5 py-2.5 font-semibold text-[13.5px]">Keluar dari Area Pendaftar</button></form>
</div>
</section>
@endsection
