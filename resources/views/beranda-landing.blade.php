@extends('layouts.publik')

@section('judul', \App\Models\Setting::ambil('nama_sekolah', 'SMA IT Arafah Boarding School').' — Beriman, Berakhlak, Cerdas')
@section('deskripsi', 'Website resmi '.\App\Models\Setting::ambil('nama_sekolah', 'SMA IT Arafah Boarding School').' — Sampit, Kotawaringin Timur: prestasi, kegiatan pembelajaran, fasilitas, Student Root, diniyah, dan alumni.')

@section('isi')
<style>
  /* ================== BERANDA GAYA LANDING PAGE ================== */
  .ld-wrap{max-width:1120px;margin:0 auto;padding:0 20px}
  .ld-seksi{padding:52px 0;scroll-margin-top:84px}
  .ld-hero{scroll-margin-top:0}
  @media (min-width:768px){.ld-seksi{padding:74px 0}}
  .ld-kabut{background:#f2f6fb}
  .ld-dua{display:grid;gap:22px}
  .ld-dua>*{min-width:0}
  @media (min-width:900px){.ld-dua{grid-template-columns:.85fr 1.15fr;gap:38px;align-items:center}}
  .ld-kicker{display:inline-block;font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#1f3a5f;background:#eef4fb;border:1px solid #dbe7f4;border-radius:999px;padding:5px 12px}
  .ld-judul{font-size:23px;line-height:1.22;font-weight:800;color:#1f3a5f;margin:14px 0 0;letter-spacing:-.01em}
  @media (min-width:768px){.ld-judul{font-size:31px}}
  .ld-lead{font-size:14.5px;line-height:1.75;color:#55657a;margin:12px 0 0}
  .ld-catatan{font-size:12.5px;line-height:1.7;color:#7b8ea6;margin:12px 0 0}
  .ld-chips{display:flex;flex-wrap:nowrap;gap:8px;margin:16px 0 0;padding:0 0 4px;list-style:none;overflow-x:auto;scrollbar-width:none;-ms-overflow-style:none}
  .ld-chips::-webkit-scrollbar{display:none}
  .ld-chips li{flex:none;font-size:12px;font-weight:600;color:#3d5670;background:#fff;border:1px solid #e7eef6;border-radius:999px;padding:6px 12px;white-space:nowrap}
  @media (min-width:768px){.ld-chips{flex-wrap:wrap;overflow:visible;padding:0}.ld-chips li{white-space:normal}}
  .ld-tautan{display:inline-block;margin-top:18px;font-size:13.5px;font-weight:700;color:#1f3a5f;border-bottom:2px solid #c6d8ea;padding-bottom:2px}
  .ld-slider{position:relative;min-width:0}
  .ld-rel{display:flex;gap:12px;overflow-x:auto;scroll-snap-type:x mandatory;scroll-behavior:smooth;padding:4px 4px 10px;scrollbar-width:none;-ms-overflow-style:none}
  .ld-rel::-webkit-scrollbar{display:none}
  .ld-slide{flex:0 0 86%;margin:0;scroll-snap-align:center}
  @media (min-width:640px){.ld-slide{flex-basis:60%}}
  @media (min-width:1024px){.ld-slide{flex-basis:47%}}
  .ld-slide a{display:block;border-radius:18px;overflow:hidden;border:1px solid #e7eef6;background:#eef4fb;box-shadow:0 22px 44px -32px rgba(31,58,95,.55)}
  .ld-slide img{width:100%;aspect-ratio:4/3;object-fit:cover;display:block}
  .ld-slide figcaption{background:#fff;padding:10px 13px;font-size:12.5px;font-weight:700;color:#1f3a5f;line-height:1.45}
  .ld-panah{position:absolute;top:38%;width:38px;height:38px;border-radius:999px;border:1px solid #dbe7f4;background:rgba(255,255,255,.96);color:#1f3a5f;font-size:20px;font-weight:800;line-height:1;display:grid;place-items:center;cursor:pointer;box-shadow:0 12px 26px -16px rgba(31,58,95,.7);z-index:2}
  .ld-panah--mundur{left:-4px}.ld-panah--maju{right:-4px}
  .ld-titik{display:flex;gap:6px;justify-content:center;margin-top:10px}
  .ld-titik i{width:7px;height:7px;border-radius:999px;background:#c6d8ea;transition:.2s}
  .ld-titik i.aktif{background:#1f3a5f;width:20px}
  .ld-kosong{border:1px dashed #c6d8ea;border-radius:18px;background:#fff;padding:26px 20px;text-align:center;color:#55657a;font-size:13px;line-height:1.7}
  .ld-kosong-ikon{font-size:26px}
  .ld-kosong-judul{font-weight:800;color:#1f3a5f;font-size:14px;margin:8px 0 4px}
  /* hero */
  .ld-hero{position:relative;min-height:76vh;display:flex;align-items:flex-end;overflow:hidden;background:#152c49}
  .ld-hero .latar{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
  .ld-hero-lapis{position:absolute;inset:0;background:linear-gradient(180deg,rgba(21,44,73,.20) 0%,rgba(21,44,73,.72) 58%,rgba(15,32,55,.95) 100%)}
  .ld-hero-isi{position:relative;z-index:2;width:100%;padding:70px 20px 46px}
  .ld-hero h1{color:#fff;font-size:27px;line-height:1.18;font-weight:800;max-width:22ch;letter-spacing:-.01em}
  @media (min-width:768px){.ld-hero h1{font-size:44px}}
  .ld-hero p{color:rgba(255,255,255,.88);font-size:14px;margin-top:12px;max-width:54ch;line-height:1.7}
  .ld-kecil{display:inline-block;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#fff;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.35);border-radius:999px;padding:6px 12px}
  .ld-tombol-baris{display:flex;flex-wrap:wrap;gap:10px;margin-top:20px}
  .ld-tombol{font-size:13.5px;font-weight:700;padding:12px 20px;border-radius:999px;border:1px solid transparent;cursor:pointer;display:inline-block}
  .ld-tombol--putih{background:#fff;color:#1f3a5f}
  .ld-tombol--garis{background:transparent;color:#fff;border-color:rgba(255,255,255,.6)}
  .ld-percaya{display:flex;flex-wrap:wrap;gap:16px;margin-top:22px;color:rgba(255,255,255,.85);font-size:12.5px;font-weight:600}
  .ld-percaya b{color:#fff}
  /* spmb */
  .ld-biru{background:#152c49;color:#fff}
  .ld-langkah{display:grid;gap:12px;margin-top:24px}
  @media (min-width:768px){.ld-langkah{grid-template-columns:repeat(4,1fr)}}
  .ld-langkah>div{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.16);border-radius:16px;padding:14px}
  .ld-langkah b{display:block;color:#fff;font-size:13px}
  .ld-langkah span{display:block;color:rgba(255,255,255,.8);font-size:12px;margin-top:6px;line-height:1.6}
  /* modal video */
  .ld-modal{position:fixed;inset:0;background:rgba(10,20,35,.88);z-index:60;display:none;align-items:center;justify-content:center;padding:16px}
  .ld-modal.buka{display:flex}
  .ld-modal-kotak{width:100%;max-width:860px;background:#000;border-radius:16px;overflow:hidden;position:relative}
  .ld-modal-kotak iframe{width:100%;aspect-ratio:16/9;display:block;border:0}
  .ld-modal-tutup{position:absolute;top:-40px;right:0;background:transparent;border:0;color:#fff;font-size:14px;font-weight:700;cursor:pointer}
</style>

@php $fotoHero = $heroFoto ?: ($seksi[0]['foto'][0]->url ?? null); @endphp

{{-- ============ 1. HERO ============ --}}
<section class="ld-hero">
  @if ($fotoHero)
    <img class="latar" src="{{ $fotoHero }}" alt="Dokumentasi {{ \App\Models\Setting::ambil('nama_sekolah', 'SMA IT Arafah') }}">
  @endif
  <div class="ld-hero-lapis"></div>
  <div class="ld-hero-isi">
    <div class="ld-wrap" style="padding:0">
      <span class="ld-kecil">Boarding School · Sampit, Kotawaringin Timur</span>
      <h1>Membentuk Generasi Modern yang Beriman, Berakhlak, dan Cerdas</h1>
      <p>SMA Islam Terpadu Arafah Boarding School — belajar akademik, diniyah, dan karakter dalam satu asrama.</p>
      <div class="ld-tombol-baris">
        @if ($videoId)
          <button type="button" class="ld-tombol ld-tombol--putih" data-buka-video>▶ Tonton Video Profil</button>
        @endif
        <a class="ld-tombol ld-tombol--garis" href="#spmb">Info SPMB</a>
      </div>
      <div class="ld-percaya">
        <span><b>Terakreditasi A</b></span>
        <span>Berdiri <b>2019</b></span>
        <span><b>{{ $jumlahSiswa }}</b> siswa aktif</span>
        <span><b>24</b> kamar asrama</span>
      </div>
    </div>
  </div>
</section>

{{-- ============ 2-8. SEKSI BERGALERI ============ --}}
@foreach ($seksi as $s)
  @include('landing.seksi', $s)
@endforeach

{{-- ============ 9. INFO SPMB ============ --}}
<section class="ld-seksi ld-biru" id="spmb">
  <div class="ld-wrap">
    <span class="ld-kecil">Penerimaan Murid Baru {{ $tahunAjaran }}</span>
    <h2 class="ld-judul" style="color:#fff">Daftarkan Putra/Putri Anda</h2>
    <p class="ld-lead" style="color:rgba(255,255,255,.85)">
      Pendaftaran dibuka sepanjang tahun dengan sistem waiting list, tersedia jalur prestasi.
      Isi formulir dari HP, tim kami menghubungi lewat WhatsApp.
    </p>

    <div class="ld-langkah">
      <div><b>1. Isi formulir</b><span>Data wali &amp; calon murid — dari HP maupun komputer.</span></div>
      <div><b>2. Verifikasi admin</b><span>Admin sekolah menghubungi lewat WhatsApp.</span></div>
      <div><b>3. Tes &amp; wawancara</b><span>Tes baca Al-Qur'an, akademik dasar, wawancara wali.</span></div>
      <div><b>4. Pengumuman</b><span>Hasil seleksi diumumkan lewat sistem dan WhatsApp.</span></div>
    </div>

    <div class="ld-tombol-baris" style="margin-top:26px">
      <a class="ld-tombol ld-tombol--putih" href="{{ url('/spmb') }}">Isi Formulir SPMB</a>
      <a class="ld-tombol ld-tombol--garis" href="{{ url('/spmb/status') }}">Cek Status Pendaftaran</a>
      @if ($waAdmin)
        <a class="ld-tombol ld-tombol--garis" href="https://wa.me/{{ preg_replace('/\D/', '', $waAdmin) }}" target="_blank" rel="noopener">Tanya lewat WhatsApp</a>
      @endif
    </div>
  </div>
</section>

{{-- ============ MODAL VIDEO PROFIL ============ --}}
@if ($videoId)
  <div class="ld-modal" id="ldModal" role="dialog" aria-modal="true" aria-label="Video profil sekolah">
    <div class="ld-modal-kotak">
      <button type="button" class="ld-modal-tutup" data-tutup-video>Tutup ✕</button>
      <div id="ldModalIsi"></div>
    </div>
  </div>
@endif

<script>
(function () {
  // ---------- slider foto ----------
  document.querySelectorAll('[data-slider]').forEach(function (kotak) {
    var rel = kotak.querySelector('[data-rel]');
    if (!rel) return;
    var maju = kotak.querySelector('[data-maju]');
    var mundur = kotak.querySelector('[data-mundur]');
    var titik = kotak.querySelector('[data-titik]');
    var slide = rel.querySelectorAll('.ld-slide');

    function lebar() { return slide.length ? slide[0].getBoundingClientRect().width + 12 : 0; }
    if (maju) maju.addEventListener('click', function () { rel.scrollBy({ left: lebar(), behavior: 'smooth' }); });
    if (mundur) mundur.addEventListener('click', function () { rel.scrollBy({ left: -lebar(), behavior: 'smooth' }); });

    if (titik) {
      slide.forEach(function (_, i) {
        var d = document.createElement('i');
        if (i === 0) d.classList.add('aktif');
        d.addEventListener('click', function () { rel.scrollTo({ left: i * lebar(), behavior: 'smooth' }); });
        titik.appendChild(d);
      });
      rel.addEventListener('scroll', function () {
        var pos = Math.round(rel.scrollLeft / lebar());
        Array.prototype.forEach.call(titik.children, function (d, i) { d.classList.toggle('aktif', i === pos); });
      }, { passive: true });
    }
  });

  // ---------- video profil (dimuat hanya saat diketuk) ----------
  var modal = document.getElementById('ldModal');
  var isi = document.getElementById('ldModalIsi');
  var id = '{{ $videoId }}';
  if (modal && isi && id) {
    document.querySelectorAll('[data-buka-video]').forEach(function (b) {
      b.addEventListener('click', function () {
        isi.innerHTML = '<iframe src="https://www.youtube.com/embed/' + id + '?autoplay=1&rel=0" title="Video Profil" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        modal.classList.add('buka');
      });
    });
    var tutup = function () { modal.classList.remove('buka'); isi.innerHTML = ''; };
    var tombolTutup = modal.querySelector('[data-tutup-video]');
    if (tombolTutup) tombolTutup.addEventListener('click', tutup);
    modal.addEventListener('click', function (e) { if (e.target === modal) tutup(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') tutup(); });
  } else if (modal) {
    modal.remove();
  }
})();
</script>
@endsection
