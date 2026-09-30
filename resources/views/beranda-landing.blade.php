@extends('layouts.publik')

@section('judul', \App\Models\Setting::ambil('nama_sekolah', 'SMA IT Arafah Boarding School').' — Beriman, Berakhlak, Cerdas')
@section('deskripsi', 'Website resmi '.\App\Models\Setting::ambil('nama_sekolah', 'SMA IT Arafah Boarding School').' — Sampit, Kotawaringin Timur: prestasi, kegiatan pembelajaran, fasilitas, Student Root, diniyah, dan alumni.')

@section('isi')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=DM+Sans:wght@400;500;700&display=swap');

  /* ========== BERANDA — gaya Thursina IIBS, isi dari panel (Konten → Halaman Beranda) ==========
     navy #234288 · kuning #fdd100 · biru muda #e9f7fe · teks #212529 / #667282 */
  .ld-seksi,.ld-hero{--navy:#234288;--emas:#fdd100;--muda:#e9f7fe;--tua:#212529;--lembut:#667282;--garis:#e6ecf5}
  .ld-seksi{padding:58px 0;scroll-margin-top:84px;font-family:'DM Sans',system-ui,sans-serif;color:var(--lembut)}
  @media (min-width:768px){.ld-seksi{padding:96px 0}}
  .ld-wrap{max-width:1160px;margin:0 auto;padding:0 20px}
  .ld-kabut{background:#e9f7fe}
  .ld-judul-blok{text-align:center;max-width:860px;margin:0 auto}
  .ld-kicker{font-family:'Manrope',system-ui,sans-serif;font-size:13px;font-weight:800;letter-spacing:.16em;
    text-transform:uppercase;color:var(--tua);margin:0 0 16px}
  @media (min-width:768px){.ld-kicker{font-size:15px}}
  .ld-judul{font-family:'Manrope',system-ui,sans-serif;font-size:30px;line-height:1.12;font-weight:800;
    letter-spacing:-.03em;color:var(--navy);margin:0}
  @media (min-width:768px){.ld-judul{font-size:48px}}
  .ld-lead{font-size:15px;line-height:1.75;color:var(--lembut);margin:22px auto 0;max-width:820px}
  @media (min-width:768px){.ld-lead{font-size:16.5px}}
  .ld-catatan{font-size:12.5px;line-height:1.7;color:#8b98a8;margin:12px 0 0}
  /* ---------- slider tulisan ---------- */
  .ld-slider{position:relative;min-width:0;margin-top:34px}
  .ld-rel{display:flex;gap:18px;overflow-x:auto;scroll-snap-type:x mandatory;scroll-behavior:smooth;
    padding:4px 4px 12px;scrollbar-width:none;-ms-overflow-style:none}
  .ld-rel::-webkit-scrollbar{display:none}
  .ld-slide{flex:0 0 86%;margin:0;scroll-snap-align:center}
  @media (min-width:640px){.ld-slide{flex-basis:55%}}
  @media (min-width:1024px){.ld-slide{flex-basis:39%}}
  .ld-tanpa-gambar{display:grid;place-items:center;aspect-ratio:4/3;padding:26px;text-align:center;
    font-family:'Manrope',system-ui,sans-serif;font-weight:800;font-size:16px;line-height:1.35;color:#234288;background:#e9f7fe}
  .ld-kartu{display:block}
  .ld-gambar{position:relative;display:block;border-radius:26px;overflow:hidden;background:var(--muda);
    border:1px solid var(--garis);box-shadow:0 24px 50px -38px rgba(35,66,136,.65)}
  .ld-gambar img{width:100%;aspect-ratio:4/3;object-fit:cover;display:block;transition:transform .55s ease}
  .ld-kartu:hover .ld-gambar img,.ld-kartu:focus-visible .ld-gambar img{transform:scale(1.045)}
  /* lapisan info: muncul saat kursor diarahkan / kartu difokus */
  .ld-lapisan{position:absolute;inset:0;display:flex;flex-direction:column;justify-content:flex-end;gap:7px;
    padding:20px 18px;opacity:0;transition:opacity .28s ease;
    background:linear-gradient(180deg,rgba(18,34,72,0) 34%,rgba(15,30,62,.86) 76%,rgba(12,24,52,.95) 100%)}
  .ld-kartu:hover .ld-lapisan,.ld-kartu:focus-visible .ld-lapisan,.ld-kartu:active .ld-lapisan{opacity:1}
  .ld-lapisan-kategori{font-family:'Manrope',system-ui,sans-serif;font-size:10.5px;font-weight:800;
    letter-spacing:.15em;text-transform:uppercase;color:#fdd100}
  .ld-lapisan-judul{font-family:'Manrope',system-ui,sans-serif;font-size:15.5px;font-weight:800;color:#fff;line-height:1.32}
  .ld-lapisan-tanggal{font-size:11.5px;color:rgba(255,255,255,.75)}
  .ld-slide figcaption{font-family:'Manrope',system-ui,sans-serif;font-size:13.5px;font-weight:700;color:var(--navy);
    margin-top:12px;line-height:1.45}
  @media (hover:hover){.ld-slide figcaption{display:none}}
  .ld-panah{position:absolute;top:36%;width:44px;height:44px;border-radius:999px;border:1px solid var(--garis);
    background:#fff;color:var(--navy);font-size:21px;font-weight:800;line-height:1;display:grid;place-items:center;
    cursor:pointer;box-shadow:0 14px 30px -18px rgba(35,66,136,.7);z-index:2}
  .ld-panah--mundur{left:2px}.ld-panah--maju{right:2px}
  .ld-titik{display:flex;gap:7px;justify-content:center;margin-top:16px}
  .ld-titik i{width:8px;height:8px;border-radius:999px;background:#c9d6ea;transition:.2s}
  .ld-titik i.aktif{background:var(--emas);width:24px}
  .ld-kosong{border:1px dashed #c9d6ea;border-radius:26px;background:#fff;padding:34px 22px;text-align:center;
    color:var(--lembut);font-size:13.5px;line-height:1.7;margin-top:34px}
  .ld-kosong-ikon{font-size:26px}
  .ld-kosong-judul{font-family:'Manrope',system-ui,sans-serif;font-weight:800;color:var(--navy);font-size:15px;margin:8px 0 4px}
  .ld-tautan-baris{text-align:center;margin-top:26px}
  .ld-tautan{display:inline-block;font-family:'Manrope',system-ui,sans-serif;font-size:14px;font-weight:800;
    color:var(--navy);background:var(--emas);border-radius:12px;padding:14px 22px}
  .ld-tautan:hover{filter:brightness(1.05)}
  /* ---------- hero ---------- */
  .ld-hero{position:relative;padding:10px;font-family:'DM Sans',system-ui,sans-serif}
  @media (min-width:768px){.ld-hero{padding:14px}}
  .ld-hero-bingkai{position:relative;border-radius:28px;overflow:hidden;min-height:70vh;display:flex;
    align-items:center;justify-content:center;background:#234288}
  @media (min-width:768px){.ld-hero-bingkai{border-radius:36px;min-height:78vh}}
  .ld-hero .latar{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
  .ld-hero-lapis{position:absolute;inset:0;
    background:linear-gradient(180deg,rgba(18,34,72,.5) 0%,rgba(18,34,72,.72) 52%,rgba(12,24,52,.95) 100%)}
  .ld-hero-isi{position:relative;z-index:2;width:100%;text-align:center;padding:70px 22px 58px;color:#fff}
  @media (min-width:768px){.ld-hero-isi{padding:96px 26px 74px}}
  .ld-kecil{display:inline-block;font-family:'Manrope',system-ui,sans-serif;font-size:11.5px;font-weight:800;
    letter-spacing:.16em;text-transform:uppercase;color:#fdd100}
  @media (min-width:768px){.ld-kecil{font-size:13px}}
  .ld-hero h1{font-family:'Manrope',system-ui,sans-serif;color:#fff;font-size:29px;line-height:1.12;font-weight:800;
    letter-spacing:-.03em;margin:14px auto 0;max-width:22ch}
  @media (min-width:768px){.ld-hero h1{font-size:52px;max-width:26ch}}
  .ld-hero p{color:rgba(255,255,255,.88);font-size:14.5px;margin:16px auto 0;max-width:60ch;line-height:1.75}
  @media (min-width:768px){.ld-hero p{font-size:16px}}
  .ld-tombol-baris{display:flex;flex-wrap:wrap;gap:12px;margin-top:26px;justify-content:center}
  .ld-tombol{font-family:'Manrope',system-ui,sans-serif;font-size:14.5px;font-weight:800;padding:16px 26px;
    border-radius:12px;border:1px solid transparent;cursor:pointer;display:inline-flex;align-items:center;gap:10px}
  .ld-tombol--emas{background:#fdd100;color:#234288}
  .ld-tombol--emas:hover{filter:brightness(1.06)}
  .ld-tombol--garis{background:transparent;color:#fff;border-color:rgba(255,255,255,.55)}
  .ld-tombol--garis:hover{background:rgba(255,255,255,.12)}
  .ld-percaya{display:grid;grid-template-columns:repeat(2,1fr);gap:20px 12px;justify-content:center;
    margin:38px auto 0;max-width:760px}
  @media (min-width:768px){.ld-percaya{grid-template-columns:repeat(4,1fr);gap:0}}
  .ld-percaya>div{text-align:center;padding:0 10px}
  @media (min-width:768px){.ld-percaya>div+div{border-left:1px solid rgba(255,255,255,.22)}}
  .ld-percaya b{display:block;font-family:'Manrope',system-ui,sans-serif;color:#fff;font-weight:800;
    font-size:22px;line-height:1.1}
  @media (min-width:768px){.ld-percaya b{font-size:26px}}
  .ld-percaya span{display:block;color:rgba(255,255,255,.72);font-size:11.5px;font-weight:600;
    letter-spacing:.09em;text-transform:uppercase;margin-top:7px}
  /* ---------- berita ---------- */
  .ld-berita{display:grid;gap:20px;margin-top:38px}
  @media (min-width:768px){.ld-berita{grid-template-columns:repeat(3,1fr);gap:24px}}
  .ld-berita-kartu{display:block;background:#fff;border:1px solid var(--garis);border-radius:24px;overflow:hidden;
    transition:transform .2s ease,box-shadow .2s ease}
  .ld-berita-kartu:hover{transform:translateY(-3px);box-shadow:0 26px 50px -38px rgba(35,66,136,.6)}
  .ld-berita-kartu img{width:100%;aspect-ratio:16/10;object-fit:cover;display:block;background:var(--muda)}
  .ld-berita-isi{padding:18px 20px 22px}
  .ld-berita-tanggal{font-size:11.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#8b98a8}
  .ld-berita-judul{font-family:'Manrope',system-ui,sans-serif;font-size:17px;font-weight:800;color:var(--navy);
    line-height:1.35;margin:9px 0 8px}
  .ld-berita-ringkas{font-size:13.5px;line-height:1.7;color:var(--lembut)}
  .ld-berita-baca{display:inline-block;margin-top:14px;font-family:'Manrope',system-ui,sans-serif;font-size:12.5px;
    font-weight:800;color:var(--navy)}
  .ld-berita-baca i{display:inline-grid;place-items:center;width:22px;height:22px;border-radius:999px;
    background:var(--emas);color:var(--navy);font-style:normal;font-size:12px;margin-left:7px}
  /* ---------- SPMB ---------- */
  .ld-biru{background:#234288;color:#fff}
  .ld-biru .ld-kicker{color:#fdd100}
  .ld-biru .ld-judul{color:#fff}
  .ld-biru .ld-lead{color:rgba(255,255,255,.85)}
  .ld-langkah{display:grid;gap:14px;margin-top:38px}
  @media (min-width:768px){.ld-langkah{grid-template-columns:repeat(4,1fr)}}
  .ld-langkah>div{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.18);border-radius:22px;padding:22px 20px}
  .ld-langkah b{display:block;font-family:'Manrope',system-ui,sans-serif;color:#fff;font-size:14.5px}
  .ld-langkah span{display:block;color:rgba(255,255,255,.8);font-size:13px;margin-top:8px;line-height:1.65}
  /* ---------- modal video ---------- */
  .ld-modal{position:fixed;inset:0;background:rgba(10,20,35,.9);z-index:60;display:none;align-items:center;justify-content:center;padding:16px}
  .ld-modal.buka{display:flex}
  .ld-modal-kotak{width:100%;max-width:900px;background:#000;border-radius:22px;overflow:hidden;position:relative}
  .ld-modal-kotak iframe{width:100%;aspect-ratio:16/9;display:block;border:0}
  .ld-modal-tutup{position:absolute;top:-42px;right:0;background:transparent;border:0;color:#fff;
    font-family:'Manrope',system-ui,sans-serif;font-size:14px;font-weight:800;cursor:pointer}
</style>

{{-- ============ 1. HERO — isinya diatur di panel: Konten → Halaman Beranda → bagian "Hero" ============ --}}
@php
    $fotoHero = $hero && $hero->gambar_url ? $hero->gambar_url : ($postingan->first()->tulisan->first()->gambar_url ?? null);
    $videoId = null;
    if ($hero && filled($hero->video) && preg_match('~(?:youtu\.be/|[?&]v=|/embed/|/shorts/)([A-Za-z0-9_-]{6,})~', (string) $hero->video, $m)) {
        $videoId = $m[1];
    }
    $tautanHero = $hero && filled($hero->tombol_tautan)
        ? (str_starts_with((string) $hero->tombol_tautan, 'http') ? $hero->tombol_tautan : url($hero->tombol_tautan))
        : '#spmb';
@endphp

<section class="ld-hero">
  <div class="ld-hero-bingkai">
    @if ($fotoHero)
      <img class="latar" src="{{ $fotoHero }}" alt="{{ \App\Models\Setting::ambil('nama_sekolah', 'SMA IT Arafah') }}">
    @endif
    <div class="ld-hero-lapis"></div>
    <div class="ld-hero-isi">
      @if ($hero && filled($hero->subjudul))
        <span class="ld-kecil">{{ $hero->subjudul }}</span>
      @endif
      <h1>{{ $hero->judul ?? 'Membentuk Generasi Modern yang Beriman, Berakhlak, dan Cerdas' }}</h1>
      @if ($hero && filled($hero->teks))
        <p>{!! $hero->teks !!}</p>
      @endif
      <div class="ld-tombol-baris">
        @if ($videoId)
          <button type="button" class="ld-tombol ld-tombol--emas" data-buka-video>▶ Tonton Video Profil</button>
        @endif
        @if ($hero && filled($hero->tombol_teks))
          <a class="ld-tombol ld-tombol--garis" href="{{ $tautanHero }}">{{ $hero->tombol_teks }}</a>
        @endif
      </div>
      @if ($hero && $hero->butir)
        <div class="ld-percaya">
          @foreach (array_slice($hero->butir, 0, 4) as $b)
            <div><b>{{ $b['judul'] ?? '' }}</b><span>{{ $b['teks'] ?? '' }}</span></div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
</section>

{{-- ============ 2-8. SEKSI SLIDER TULISAN BERKATEGORI ============ --}}
@foreach ($postingan as $i => $b)
  @include('landing.seksi', ['b' => $b, 'tulisan' => $b->tulisan, 'kabut' => $i % 2 === 1, 'contoh' => $b->contoh ?? false])
@endforeach

{{-- ============ 9. BERITA TERBARU ============ --}}
@if ($beritaRow && $berita->count())
  <section class="ld-seksi ld-kabut" id="seksi-{{ $beritaRow->id }}">
    <div class="ld-wrap">
      <div class="ld-judul-blok">
        @if (filled($beritaRow->subjudul))<h3 class="ld-kicker">{{ $beritaRow->subjudul }}</h3>@endif
        <h2 class="ld-judul">{{ $beritaRow->judul ?: 'Berita Terbaru' }}</h2>
        @if (filled($beritaRow->teks))<p class="ld-lead">{!! $beritaRow->teks !!}</p>@endif
      </div>
      <div class="ld-berita">
        @foreach ($berita as $t)
          <a class="ld-berita-kartu" href="{{ url('/berita/' . $t->slug) }}">
            @if ($t->gambar_url)<img src="{{ $t->gambar_url }}" alt="{{ $t->judul }}" loading="lazy">@endif
            <div class="ld-berita-isi">
              <div class="ld-berita-tanggal">{{ $t->tanggal_indonesia }}</div>
              <div class="ld-berita-judul">{{ $t->judul }}</div>
              <div class="ld-berita-ringkas">{{ $t->ringkas }}</div>
              <span class="ld-berita-baca">Baca tulisan <i>→</i></span>
            </div>
          </a>
        @endforeach
      </div>
      <div class="ld-tautan-baris">
        <a class="ld-tautan" href="{{ url('/berita') }}">Lihat semua berita <span aria-hidden="true">→</span></a>
      </div>
    </div>
  </section>
@endif

{{-- ============ 10. INFO SPMB ============ --}}
@if ($spmb)
  <section class="ld-seksi ld-biru" id="spmb">
    <div class="ld-wrap">
      <div class="ld-judul-blok">
        @if (filled($spmb->subjudul))<h3 class="ld-kicker">{{ $spmb->subjudul }}</h3>@endif
        <h2 class="ld-judul">{{ $spmb->judul ?: 'Daftarkan Putra/Putri Anda' }}</h2>
        @if (filled($spmb->teks))<p class="ld-lead">{!! $spmb->teks !!}</p>@endif
      </div>

      @if ($spmb->butir)
        <div class="ld-langkah">
          @foreach (array_slice($spmb->butir, 0, 4) as $b)
            <div><b>{{ $b['judul'] ?? '' }}</b><span>{{ $b['teks'] ?? '' }}</span></div>
          @endforeach
        </div>
      @endif

      <div class="ld-tombol-baris" style="margin-top:34px">
        <a class="ld-tombol ld-tombol--emas" href="{{ $spmb->tombol_tautan ? url($spmb->tombol_tautan) : url('/spmb') }}">
          {{ $spmb->tombol_teks ?: 'Isi Formulir SPMB' }}
        </a>
        <a class="ld-tombol ld-tombol--garis" href="{{ url('/spmb/status') }}">Cek Status Pendaftaran</a>
        @if ($waAdmin)
          <a class="ld-tombol ld-tombol--garis" href="https://wa.me/{{ preg_replace('/\D/', '', $waAdmin) }}" target="_blank" rel="noopener">Tanya lewat WhatsApp</a>
        @endif
      </div>
    </div>
  </section>
@endif

{{-- ============ bagian lain yang masih aktif (bentuk lama) ============ --}}
@foreach ($lain as $b)
  @include('beranda.bagian', ['b' => $b])
@endforeach

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
  // ---------- slider tulisan: panah + titik ----------
  document.querySelectorAll('[data-slider]').forEach(function (kotak) {
    var rel = kotak.querySelector('[data-rel]');
    if (!rel) return;
    var maju = kotak.querySelector('[data-maju]');
    var mundur = kotak.querySelector('[data-mundur]');
    var titik = kotak.querySelector('[data-titik]');
    var slide = rel.querySelectorAll('.ld-slide');

    function lebar() { return slide.length ? slide[0].getBoundingClientRect().width + 18 : 0; }
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

  // ---------- video profil: dimuat hanya saat diketuk ----------
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
