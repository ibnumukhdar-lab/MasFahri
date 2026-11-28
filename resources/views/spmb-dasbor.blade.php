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

  // ==== tahap tes & hasil ====
  $progres = $progresTes ?? ['total' => 0, 'selesai' => 0, 'tuntas' => false, 'rata' => null];
  $tesTpaSelesai = ($progres['total'] ?? 0) > 0 && ! empty($progres['tuntas']);
  $tesBtqSelesai = $quran && $quran->lengkap;
  $tesWawancaraSelesai = $wawancara && $wawancara->lengkap;
  $semuaTesSelesai = $tesTpaSelesai && $tesBtqSelesai && $tesWawancaraSelesai;
  $tesIntiSelesai = $tesTpaSelesai && $tesBtqSelesai;

  $lengkapData = $formulir->terkirim_pada && $persenTotal >= 100;
  $modeUbah = request()->boolean('ubah') || ! $lengkapData;

  $tglUmum = $tanggalPengumuman ? \Carbon\Carbon::parse($tanggalPengumuman) : null;
  $sudahDiumumkan = $tglUmum ? now()->startOfDay()->gte($tglUmum->copy()->startOfDay()) : false;
  $sisaHari = $tglUmum ? (int) now()->startOfDay()->diffInDays($tglUmum->copy()->startOfDay(), false) : null;
  $statusFinal = in_array($registrasi->status, ['diterima', 'ditolak'], true);

  $nilaiTpa = $progres['rata'] ?? null;
  $nilaiBtq = $quran?->skor_btq;
  $nilaiWawancara = $wawancara?->skor_wawancara;
  $kumpulanNilai = collect([$nilaiTpa, $nilaiBtq, $nilaiWawancara])->filter(fn ($v) => $v !== null);
  $nilaiAkhir = $kumpulanNilai->count() ? (int) round($kumpulanNilai->avg()) : null;

  // ==== status terintegrasi (data → tes → hasil) ====
  $statusIntegrasi = match (true) {
      $registrasi->status === 'diterima' => ['label' => 'Diterima', 'dot' => '#35d07f', 'bg' => 'rgba(34,163,90,.20)', 'fg' => '#b9f3cf', 'bd' => 'rgba(53,208,127,.42)'],
      $registrasi->status === 'ditolak' => ['label' => 'Tidak diterima', 'dot' => '#f87171', 'bg' => 'rgba(220,38,38,.20)', 'fg' => '#ffc9c9', 'bd' => 'rgba(248,113,113,.42)'],
      ! $lengkapData => ['label' => 'Lengkapi data', 'dot' => '#fdd100', 'bg' => 'rgba(253,209,0,.16)', 'fg' => '#ffe483', 'bd' => 'rgba(253,209,0,.34)'],
      $semuaTesSelesai && $sudahDiumumkan => ['label' => 'Menunggu keputusan panitia', 'dot' => '#93c5fd', 'bg' => 'rgba(147,197,253,.16)', 'fg' => '#dbeafe', 'bd' => 'rgba(147,197,253,.36)'],
      $semuaTesSelesai => ['label' => 'Menunggu pengumuman', 'dot' => '#93c5fd', 'bg' => 'rgba(147,197,253,.16)', 'fg' => '#dbeafe', 'bd' => 'rgba(147,197,253,.36)'],
      $tesIntiSelesai => ['label' => 'Wawancara menyusul', 'dot' => '#fdd100', 'bg' => 'rgba(253,209,0,.16)', 'fg' => '#ffe483', 'bd' => 'rgba(253,209,0,.34)'],
      default => ['label' => 'Tahap tes', 'dot' => '#fdd100', 'bg' => 'rgba(253,209,0,.16)', 'fg' => '#ffe483', 'bd' => 'rgba(253,209,0,.34)'],
  };

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
  .sf-bingkai-kosong{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;
    width:100%;height:100%;padding:0 4px;text-align:center;font-size:9.5px;line-height:1.3;color:#a87777;
    background:linear-gradient(180deg,#fff8f8 0%,#ffeded 100%)}
  .sf-bingkai-kosong svg{color:#d3adad;flex:none}
  .sf-bingkai-kosong b{font-size:9.5px;font-weight:700;color:#b06868;letter-spacing:.02em}
  /* kelas hidden harus menang atas display:flex/block di atas */
  .sf-bingkai img.hidden,.sf-bingkai-kosong.hidden{display:none}
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

  .sf-cek{display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px dashed #eef3f9;font-size:13px}
  .sf-cek:last-child{border-bottom:0}
  .sf-cek-ikon{flex:none;width:22px;height:22px;border-radius:50%;display:grid;place-items:center;background:#eef4fb;color:#b6c2d1}
  .sf-cek.ok .sf-cek-ikon{background:#22a35a;color:#fff}
  .sf-cek-nama{font-weight:600;color:#1f3a5f;flex:1;min-width:0}
  .sf-cek-info{font-size:11.5px;color:#94a3b8;flex:none}
  .sf-cek.ok .sf-cek-info{color:#14875a;font-weight:700}
  .sf-tes{display:flex;gap:12px;padding:12px 0;border-bottom:1px dashed #eef3f9;align-items:flex-start}
  .sf-tes:last-child{border-bottom:0}
  .sf-tes-ikon{flex:none;width:34px;height:34px;border-radius:11px;display:grid;place-items:center;background:#eef4fb;color:#1f3a5f;font-weight:800;font-size:11px}
  .sf-tes.ok .sf-tes-ikon{background:#dcf5e6;color:#14875a}
  .sf-tes-judul{font-weight:700;color:#1f3a5f;font-size:13.5px}
  .sf-tes-ket{font-size:12px;color:#7c8ba1;margin-top:2px;line-height:1.45}
  .sf-centang{flex:none;width:22px;height:22px;border-radius:50%;display:grid;place-items:center;background:#eef2f7;color:#c3ccd8;margin-top:2px}
  .sf-centang.ok{background:#22a35a;color:#fff}
  .sf-hasil-lulus{background:linear-gradient(125deg,#0f6b3f,#14875a);color:#fff;border-radius:16px;padding:20px}
  .sf-nilai-tabel{width:100%;border-collapse:collapse;font-size:13px}
  .sf-nilai-tabel td{padding:8px 4px;border-bottom:1px dashed #e8eef6}
  .sf-nilai-tabel tr:last-child td{border-bottom:0}
  .sf-nilai-tabel td:last-child{text-align:right;font-weight:800;color:#1f3a5f}
  .sf-tombol-kecil{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;padding:8px 13px;border-radius:10px;text-decoration:none}

  /* ---- tab halaman pendaftar ---- */
  .sf-tabs{display:flex;gap:5px;background:#fff;border:1px solid #e7eef6;border-radius:14px;padding:5px;
    position:sticky;top:74px;z-index:30;box-shadow:0 10px 24px -22px rgba(31,58,95,.6);overflow-x:auto}
  .sf-tabs::-webkit-scrollbar{display:none}
  .sf-tab{flex:1 0 auto;white-space:nowrap;border:0;background:transparent;padding:9px 12px;border-radius:10px;
    font-size:12.5px;font-weight:700;color:#64748b;cursor:pointer;transition:background .15s,color .15s}
  .sf-tab:hover{background:#f4f8fd;color:#1f3a5f}
  .sf-tab.aktif{background:#1f3a5f;color:#fff}
  .sf-led{display:inline-block;width:6px;height:6px;border-radius:50%;background:#fdd100;margin-left:5px;vertical-align:middle}
  .sf-panel{display:none}
  .sf-panel.tampil{display:block}
  @media (max-width:640px){
    .sf-tabs{top:66px;gap:3px;padding:4px}
    .sf-tab{font-size:11.5px;padding:8px 9px}
  }
</style>

<section class="wrap pt-8">
  <div class="sf-kartu max-w-3xl mx-auto">
    <div class="sf-kepala">
      <div class="sf-kepala-isi flex items-center gap-5 justify-between">
        <div class="min-w-0">
          <p class="sf-eyebrow">Ringkasan pendaftaran</p>
          <p class="text-[17px] md:text-[19px] font-extrabold mt-1 tracking-tight">{{ $registrasi->nomor }}</p>
          <div class="flex flex-wrap items-center gap-2 mt-2">
            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full"
                  style="background:{{ $statusIntegrasi['bg'] }};color:{{ $statusIntegrasi['fg'] }};border:1px solid {{ $statusIntegrasi['bd'] }}">
              <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $statusIntegrasi['dot'] }}"></span>
              {{ $statusIntegrasi['label'] }}
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

    </div>
  </div>
</section>

<div class="wrap pt-6">
  <div class="max-w-3xl mx-auto">
    <div class="sf-tabs" id="sf-tabs" role="tablist" aria-label="Bagian dashboard">
      <button type="button" class="sf-tab aktif" data-tab="ringkasan" role="tab">Ringkasan</button>
      <button type="button" class="sf-tab" data-tab="tes" role="tab">Tes</button>
      <button type="button" class="sf-tab" data-tab="hasil" role="tab">Hasil</button>
      <button type="button" class="sf-tab" data-tab="data" role="tab">Data</button>
    </div>
  </div>
</div>

<div class="sf-panel tampil" data-panel="ringkasan">

{{-- ================= KELENGKAPAN DATA ================= --}}
<section class="wrap pt-4">
  <div class="max-w-3xl mx-auto">
    <div class="sf-kartu">
      <div class="p-5 md:p-6">
        <div class="flex items-end justify-between gap-2 mb-3">
          <div>
            <p class="sf-eyebrow" style="color:#94a3b8">Ringkasan</p>
            <h2 class="text-[18px] md:text-[20px] font-extrabold text-navy mt-0.5">Kelengkapan Data</h2>
          </div>
          <span class="text-[11px] font-bold px-2.5 py-1 rounded-full" style="background:{{ $persenTotal >= 100 ? '#e7f6ec' : '#fff8e8' }};color:{{ $persenTotal >= 100 ? '#166534' : '#8a5a00' }}">
            {{ $isiForm }}/{{ $bobotForm }} kolom
          </span>
        </div>

        @foreach ([
          ['Biodata siswa', $isiPribadi, count($kolPribadi), '#data-pribadi'],
          ['Foto latar merah', $adaFoto ? 1 : 0, 1, '#foto-anak'],
          ['Data ayah', $isiAyah, count($kolAyah), '#data-orang-tua'],
          ['Data ibu', $isiIbu, count($kolIbu), '#data-orang-tua'],
        ] as [$judulCek, $isiCek, $totalCek, $anchorCek])
          @php $okCek = $isiCek >= $totalCek; @endphp
          <div class="sf-cek {{ $okCek ? 'ok' : '' }}">
            <span class="sf-cek-ikon">
              @if ($okCek)
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"/></svg>
              @else
                <span style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
              @endif
            </span>
            <span class="sf-cek-nama">{{ $judulCek }}</span>
            <span class="sf-cek-info">{{ $okCek ? 'lengkap' : $isiCek.'/'.$totalCek }}</span>
            @if (! $okCek)<a href="{{ $anchorCek }}" class="text-[11px] underline" style="color:#b58a00">isi</a>@endif
          </div>
        @endforeach

        <div class="sf-cek {{ $formulir->terkirim_pada ? 'ok' : '' }}">
          <span class="sf-cek-ikon">
            @if ($formulir->terkirim_pada)
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"/></svg>
            @else
              <span style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
            @endif
          </span>
          <span class="sf-cek-nama">Formulir diserahkan</span>
          <span class="sf-cek-info">{{ $formulir->terkirim_pada ? 'terkirim '.$formulir->terkirim_pada->format('d M Y') : 'belum' }}</span>
          @if (! $formulir->terkirim_pada)<a href="#formulir" class="text-[11px] underline" style="color:#b58a00">isi</a>@endif
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ================= TES MASUK ================= --}}
<section class="wrap pt-4">
  <div class="max-w-3xl mx-auto">
    <div class="sf-kartu">
      <div class="p-5 md:p-6">
        <div class="flex items-end justify-between gap-2 mb-1">
          <div>
            <p class="sf-eyebrow" style="color:#94a3b8">Seleksi</p>
            <h2 class="text-[18px] md:text-[20px] font-extrabold text-navy mt-0.5">Tes Masuk</h2>
          </div>
          @if ($semuaTesSelesai)
            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full" style="background:#e7f6ec;color:#166534">Semua tes selesai</span>
          @elseif ($tesIntiSelesai)
            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full" style="background:#fff8e8;color:#8a5a00">TPA &amp; BTQ selesai · wawancara menyusul</span>
          @endif
        </div>
        <p class="text-[12px] text-slate-500 mb-3">Tiga tahap yang harus dilalui.</p>

        @php
          $daftarTes = [
            [
              'kode' => 'TPA',
              'judul' => 'Tes Potensi Akademik (CBT)',
              'selesai' => $tesTpaSelesai,
              'tanggal' => $tanggalTes,
              'ket' => ($progres['total'] ?? 0) > 0
                  ? $progres['selesai'].' / '.$progres['total'].' paket soal selesai'
                  : 'Paket soal belum dibuka panitia',
            ],
            [
              'kode' => 'BTQ',
              'judul' => "Baca Tulis Al-Qur'an (BTQ)",
              'selesai' => $tesBtqSelesai,
              'tanggal' => $tanggalTes,
              'ket' => $tesBtqSelesai
                  ? 'Nilai baca, tulis, dan praktek sholat sudah diisi penguji'
                  : ($quran ? 'Menunggu penilaian penguji BTQ' : 'Nilai diisi Ustadz/Ustadzah penguji'),
            ],
            [
              'kode' => 'WWC',
              'judul' => 'Wawancara Minat dan Bakat',
              'selesai' => $tesWawancaraSelesai,
              'tanggal' => $wawancara?->tanggal ?? $tanggalTes,
              'ket' => $tesWawancaraSelesai
                  ? 'Sudah diwawancarai dan dinilai penguji'
                  : 'Sistem penilaian wawancara menyusul — panitia akan menjadwalkan',
            ],
          ];
        @endphp

        @foreach ($daftarTes as $t)
          <div class="sf-tes {{ $t['selesai'] ? 'ok' : '' }}">
            <span class="sf-tes-ikon">{{ $t['kode'] }}</span>
            <div class="flex-1 min-w-0">
              <p class="sf-tes-judul">{{ $t['judul'] }}</p>
              <p class="sf-tes-ket">
                {{ $t['ket'] }}
                @if (! $t['selesai'] && $t['tanggal'])
                  <span class="block">Dijadwalkan {{ \Carbon\Carbon::parse($t['tanggal'])->translatedFormat('l, j F Y') }} — persiapkan diri.</span>
                @endif
              </p>
            </div>
            <span class="sf-centang {{ $t['selesai'] ? 'ok' : '' }}">
              @if ($t['selesai'])
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5l5 5L20 6.5"/></svg>
              @endif
            </span>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

</div>

<div class="sf-panel" data-panel="hasil">

{{-- ================= HASIL TES ================= --}}
<section class="wrap pt-4">
  <div class="max-w-3xl mx-auto">
    <div class="sf-kartu">
      <div class="p-5 md:p-6">
        <p class="sf-eyebrow" style="color:#94a3b8">Pengumuman</p>
        <h2 class="text-[18px] md:text-[20px] font-extrabold text-navy mt-0.5 mb-3">Hasil Tes</h2>

        @if (! $tglUmum)
          <p class="text-[13px] text-slate-500">Tanggal pengumuman belum ditetapkan panitia.</p>
        @elseif (! $sudahDiumumkan)
          <div class="rounded-xl p-4" style="background:#f4f8fd;border:1px solid #dbe7f4">
            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500 mb-1">Diumumkan pada</p>
            <p class="font-extrabold text-navy text-[15px]">{{ $tglUmum->translatedFormat('l, j F Y') }}</p>
            @if ($sisaHari !== null && $sisaHari > 0)
              <p class="text-[12.5px] text-slate-600 mt-1">Sekitar {{ $sisaHari }} hari lagi. Hasil belum dibuka.</p>
            @endif
          </div>
        @elseif ($statusFinal && $registrasi->status === 'diterima')
          <div class="sf-hasil-lulus">
            <p class="text-[10px] font-bold uppercase tracking-[.18em]" style="color:rgba(255,255,255,.75)">Hasil seleksi</p>
            <p class="text-[26px] font-extrabold mt-1 mb-1">LULUS / DITERIMA</p>
            <p class="text-[12.5px]" style="color:rgba(255,255,255,.85)">Selamat! Anda dinyatakan diterima di SMA IT Arafah.</p>
          </div>

          <div class="mt-4 rounded-xl p-4" style="background:#f8fbff;border:1px solid #e2ecf7">
            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400 mb-2">Rincian nilai</p>
            <table class="sf-nilai-tabel">
              <tr><td>Tes Potensi Akademik</td><td>{{ $nilaiTpa ?? '—' }}</td></tr>
              <tr><td>Baca Tulis Al-Qur'an</td><td>{{ $nilaiBtq ?? '—' }}</td></tr>
              <tr><td>Wawancara Minat dan Bakat</td><td>{{ $nilaiWawancara ?? '—' }}</td></tr>
              <tr><td style="font-weight:800;color:#1f3a5f">Nilai akhir</td><td style="font-size:16px">{{ $nilaiAkhir ?? '—' }}</td></tr>
            </table>
          </div>

          @if ($registrasi->pesan_admin)
            <p class="text-[12.5px] text-slate-600 mt-3 leading-relaxed">{{ $registrasi->pesan_admin }}</p>
          @endif

          <a href="{{ route('spmb.pendaftar.bukti') }}" target="_blank"
             class="sf-tombol-kecil mt-4" style="background:#1f3a5f;color:#fff">
            Cetak Bukti Kelulusan
          </a>
        @elseif ($statusFinal)
          <div class="rounded-xl p-4" style="background:#fdf2f2;border:1px solid #f3d1d1">
            <p class="text-[10px] font-bold uppercase tracking-wide" style="color:#9b1c1c">Hasil seleksi</p>
            <p class="text-[20px] font-extrabold mt-1" style="color:#9b1c1c">TIDAK DITERIMA</p>
            <p class="text-[12.5px] text-slate-600 mt-1">{{ $registrasi->pesan_admin ?: 'Terima kasih atas keikutsertaan Anda.' }}</p>
          </div>
        @else
          <p class="text-[13px] text-slate-500">Hasil sedang diproses panitia. Silakan cek kembali.</p>
        @endif
      </div>
    </div>
  </div>
</section>

</div>

<div class="sf-panel" data-panel="tes">

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
      <span class="chip">
        @if (($progresTes['total'] ?? 0) > 0)
          Tes Akademik: {{ $progresTes['selesai'] }} / {{ $progresTes['total'] }} paket selesai
          @if (! empty($progresTes['tuntas'])) — tuntas @endif
        @else
          Tes: belum ada paket dibuka
        @endif
      </span>
    </div>
    <div class="p-6 md:p-7">
      <p class="text-[13px] text-slate-600 mb-4">Kode ini juga tercetak di kartu tes. Simpan baik-baik — tanpa kode ini tes tidak bisa dibuka.</p>
      <div class="sf-galgal">
        <a href="{{ route('spmb.pendaftar.kartu') }}" class="sf-tombol sf-tombol-draf">Lihat Kartu Tes (Cetak)</a>
        <a href="{{ route('spmb.pendaftar.tes') }}" class="sf-tombol sf-tombol-utama">{{ ! empty($progresTes['tuntas']) ? 'Tes CBT Selesai' : (($progresTes['selesai'] ?? 0) > 0 ? 'Lanjutkan Tes CBT' : 'Buka Tes CBT') }}</a>
      </div>

      @if (! empty($progresTes['tuntas']))
        <div class="rounded-xl p-4 mt-5" style="background:#eef6f2;border:1px solid #cfe6da">
          <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-800 mb-1">Tahap berikutnya</p>
          <p class="font-extrabold text-navy text-[14.5px] mb-0.5">Tes BTQ (Baca Tulis Al-Qur'an)</p>
          <p class="text-[12.5px] text-slate-600">Seluruh paket Tes Akademik sudah tuntas. Menuju pos BTQ dan tunjukkan kartu tes kepada Ustadz/Ustadzah penguji. Nilai diisi oleh penguji.</p>
        </div>
      @endif
    </div>
  </div>
</div>
</section>
@endif

</div>

<div class="sf-panel" data-panel="data">

@if ($formulirTampil)
@if ($modeUbah)
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
            <div id="foto-kosong" class="sf-bingkai-kosong {{ $formulir->foto_latar_merah ? 'hidden' : '' }}">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="8.4" r="3.7"/>
                <path d="M4.6 20.6c.7-4.2 3.7-6.4 7.4-6.4s6.7 2.2 7.4 6.4"/>
              </svg>
              <span>Belum ada<br>foto</span>
              <b>3 &times; 4</b>
            </div>
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
@else
{{-- ================= PROFIL (BACA SAJA) ================= --}}
<section class="wrap py-8 md:py-10">
  <div class="max-w-3xl mx-auto">
    <div class="sf-kartu">
      <div class="p-5 md:p-6">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
          <div>
            <p class="sf-eyebrow" style="color:#94a3b8">Data pendaftar</p>
            <h2 class="text-[19px] md:text-[21px] font-extrabold text-navy mt-0.5">Profil &amp; Formulir</h2>
          </div>
          <a href="{{ route('spmb.pendaftar.dasbor', ['ubah' => 1]) }}#formulir"
             class="sf-tombol-kecil" style="background:#1f3a5f;color:#fff">Perbarui data</a>
        </div>

        <div class="flex flex-col sm:flex-row gap-4">
          <div style="flex:none">
            @if ($formulir->foto_latar_merah)
              <img src="{{ asset('media/'.$formulir->foto_latar_merah) }}" alt="Foto {{ $registrasi->nama_siswa }}"
                   style="width:104px;height:138px;object-fit:cover;border-radius:12px;border:1px solid #e2ecf7">
            @else
              <div style="width:104px;height:138px;border-radius:12px;border:1px dashed #c6d8ea;display:grid;place-items:center;font-size:10px;color:#a87777;text-align:center;padding:6px">Foto belum ada</div>
            @endif
          </div>

          <div style="flex:1;min-width:0">
            <p class="text-[16px] font-extrabold text-navy">{{ $registrasi->nama_siswa }}</p>
            <p class="text-[12px] text-slate-500 mb-2">{{ $registrasi->nomor }} · {{ $registrasi->tahun_ajaran }}</p>
            <dl>
              <div class="sf-info"><dt>Nama panggilan</dt><dd>{{ $formulir->nama_panggilan ?: '—' }}</dd></div>
              <div class="sf-info"><dt>NISN</dt><dd>{{ $formulir->nisn ?: ($biodata->nisn ?: '—') }}</dd></div>
              <div class="sf-info"><dt>NIK</dt><dd>{{ $formulir->nik ?: ($biodata->nik ?: '—') }}</dd></div>
              <div class="sf-info"><dt>Tempat, tanggal lahir</dt><dd>{{ trim(($formulir->tempat_lahir ?: $biodata->tempat_lahir ?: '').', '.($formulir->tanggal_lahir?->format('d M Y') ?: ''), ', ') ?: '—' }}</dd></div>
              <div class="sf-info"><dt>Golongan darah</dt><dd>{{ $formulir->golongan_darah ?: '—' }}</dd></div>
              <div class="sf-info"><dt>Suku</dt><dd>{{ $formulir->suku_bangsa ?: '—' }}</dd></div>
              <div class="sf-info"><dt>Anak ke</dt><dd>{{ $formulir->anak_ke ?: '—' }} dari {{ $formulir->dari_bersaudara ?: '—' }}</dd></div>
              <div class="sf-info"><dt>Alamat</dt><dd>{{ trim(($formulir->alamat_jalan ?: '').', '.($formulir->kelurahan ?: '').', '.($formulir->kecamatan ?: ''), ', ') ?: '—' }}</dd></div>
              <div class="sf-info"><dt>Sekolah asal</dt><dd>{{ $registrasi->sekolah_asal ?: '—' }}</dd></div>
              <div class="sf-info"><dt>WhatsApp</dt><dd>{{ $registrasi->whatsapp }}</dd></div>
            </dl>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
          <div>
            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400 mb-1">Data ayah</p>
            <dl>
              <div class="sf-info"><dt>Nama</dt><dd>{{ $formulir->ayah_nama ?: ($biodata->nama_ayah ?: '—') }}</dd></div>
              <div class="sf-info"><dt>TTL</dt><dd>{{ $formulir->ayah_ttl ?: '—' }}</dd></div>
              <div class="sf-info"><dt>Pendidikan</dt><dd>{{ $formulir->ayah_pendidikan ?: '—' }}</dd></div>
              <div class="sf-info"><dt>Pekerjaan</dt><dd>{{ $formulir->ayah_pekerjaan ?: ($biodata->pekerjaan_ayah ?: '—') }}</dd></div>
              <div class="sf-info"><dt>Penghasilan</dt><dd>{{ $formulir->ayah_penghasilan ?: '—' }}</dd></div>
              <div class="sf-info"><dt>Telepon</dt><dd>{{ $formulir->ayah_telepon ?: '—' }}</dd></div>
            </dl>
          </div>
          <div>
            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400 mb-1">Data ibu</p>
            <dl>
              <div class="sf-info"><dt>Nama</dt><dd>{{ $formulir->ibu_nama ?: ($biodata->nama_ibu ?: '—') }}</dd></div>
              <div class="sf-info"><dt>TTL</dt><dd>{{ $formulir->ibu_ttl ?: '—' }}</dd></div>
              <div class="sf-info"><dt>Pendidikan</dt><dd>{{ $formulir->ibu_pendidikan ?: '—' }}</dd></div>
              <div class="sf-info"><dt>Pekerjaan</dt><dd>{{ $formulir->ibu_pekerjaan ?: ($biodata->pekerjaan_ibu ?: '—') }}</dd></div>
              <div class="sf-info"><dt>Penghasilan</dt><dd>{{ $formulir->ibu_penghasilan ?: '—' }}</dd></div>
              <div class="sf-info"><dt>Telepon</dt><dd>{{ $formulir->ibu_telepon ?: '—' }}</dd></div>
            </dl>
          </div>
        </div>

        <p class="text-[11.5px] text-slate-400 mt-4">
          Data ini sudah diserahkan{{ $formulir->terkirim_pada ? ' pada '.$formulir->terkirim_pada->format('d M Y H:i') : '' }}.
          Perubahan hanya bila diperlukan — tekan “Perbarui data”.
        </p>
      </div>
    </div>
  </div>
</section>
@endif
@endif

<div class="wrap pb-10">
  <div class="max-w-3xl mx-auto">
    <form action="{{ route('spmb.pendaftar.keluar') }}" method="post">
      @csrf
      <button class="border border-slate-300 text-slate-600 rounded-xl px-5 py-2.5 font-semibold text-[13.5px]">Keluar dari Area Pendaftar</button>
    </form>
  </div>
</div>
</div>

<script>
(function () {
  function siapkan() {
    var bar = document.getElementById('sf-tabs');
    if (!bar) return;

    var tombol = [].slice.call(bar.querySelectorAll('[data-tab]'));
    var panel = [].slice.call(document.querySelectorAll('.sf-panel'));
    var peta = { formulir: 'data', 'data-pribadi': 'data', 'foto-anak': 'data', 'data-orang-tua': 'data', kartu: 'tes' };

    function pilih(nama) {
      tombol.forEach(function (b) { b.classList.toggle('aktif', b.getAttribute('data-tab') === nama); });
      panel.forEach(function (x) { x.classList.toggle('tampil', x.getAttribute('data-panel') === nama); });
    }

    tombol.forEach(function (b) {
      b.addEventListener('click', function () {
        var nama = b.getAttribute('data-tab');
        pilih(nama);
        try { history.replaceState(null, '', '#' + nama); } catch (e) {}
      });
    });

    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
      a.addEventListener('click', function () {
        var tujuan = peta[a.getAttribute('href').replace('#', '')];
        if (tujuan) pilih(tujuan);
      });
    });

    function dariHash() {
      var hash = (location.hash || '').replace('#', '');
      var nama = peta[hash] || hash;
      if (['ringkasan', 'tes', 'hasil', 'data'].indexOf(nama) >= 0) pilih(nama);
    }

    dariHash();
    window.addEventListener('hashchange', dariHash);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', siapkan);
  } else {
    siapkan();
  }
  // jaring pengaman bila hash diterapkan setelah skrip berjalan
  window.addEventListener('load', function () { setTimeout(siapkan, 0); });
})();
</script>
@endsection
