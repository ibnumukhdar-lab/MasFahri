@extends('layouts.publik')
@section('judul', 'Ekstrakurikuler — SMA IT Arafah Boarding School')
@section('deskripsi', 'Ekstrakurikuler SMA IT Arafah Boarding School Sampit: daftar kegiatan, foto kegiatan, dan informasi terbaru.')

@section('isi')
@include('partials.hero', ['kecil' => 'Kesiswaan', 'judul' => 'Ekstrakurikuler', 'sub' => 'Wadah pengembangan bakat, kepemimpinan, dan kebugaran siswa di luar jam pelajaran.'])

<section class="wrap py-10 md:py-16">
  <style>
    .eks-h2{font-family:'Manrope',system-ui,sans-serif;font-size:22px;line-height:1.28;font-weight:800;letter-spacing:-.02em;color:#234288;margin:0}
    @media (min-width:768px){.eks-h2{font-size:28px}}
    .eks-garis{display:block;width:52px;height:3px;border-radius:3px;background:#fdd100;margin:12px 0 0}
    .eks-blok{margin-bottom:44px}
    .eks-lead{font-size:14.5px;line-height:1.75;color:#667282;margin:14px 0 0;max-width:780px}
    /* kartu ekskul (dengan foto kegiatannya) */
    .eks-grid{display:grid;gap:16px;margin-top:26px}
    @media (min-width:640px){.eks-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (min-width:1024px){.eks-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
    .eks-kartu{display:block;background:#fff;border:1px solid #e6ecf5;border-radius:20px;overflow:hidden;
      transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease}
    .eks-kartu:hover{transform:translateY(-3px);border-color:#fdd100;box-shadow:0 22px 40px -30px rgba(35,66,136,.6)}
    .eks-foto{display:block;position:relative;background:#e9f7fe}
    .eks-foto img{width:100%;aspect-ratio:16/10;object-fit:cover;display:block}
    .eks-ikon{display:grid;place-items:center;aspect-ratio:16/10;font-size:34px}
    .eks-isi{display:block;padding:18px 20px 20px}
    .eks-nama{display:block;font-family:'Manrope',system-ui,sans-serif;font-size:16px;font-weight:800;color:#234288;line-height:1.35}
    .eks-ket{display:block;font-size:13.5px;line-height:1.7;color:#667282;margin-top:7px}
    .eks-jumlah{display:inline-block;margin-top:12px;font-family:'Manrope',system-ui,sans-serif;font-size:12px;font-weight:800;
      color:#234288;background:#e9f7fe;border-radius:999px;padding:5px 11px}
    /* kartu tulisan berkategori */
    .eks-postingan{display:grid;gap:20px;margin-top:26px}
    @media (min-width:700px){.eks-postingan{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (min-width:1024px){.eks-postingan{grid-template-columns:repeat(3,minmax(0,1fr))}}
    .eks-tulisan{display:block;background:#fff;border:1px solid #e6ecf5;border-radius:22px;overflow:hidden;
      transition:transform .18s ease,box-shadow .18s ease}
    .eks-tulisan:hover{transform:translateY(-3px);box-shadow:0 24px 46px -34px rgba(35,66,136,.6)}
    .eks-tulisan img{width:100%;aspect-ratio:16/10;object-fit:cover;display:block;background:#e9f7fe}
    .eks-tulisan-isi{display:block;padding:18px 20px 22px}
    .eks-tanggal{display:block;font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#8b98a8;font-family:'Manrope',system-ui,sans-serif}
    .eks-judul-tulisan{display:block;font-family:'Manrope',system-ui,sans-serif;font-size:16.5px;font-weight:800;color:#234288;line-height:1.35;margin:9px 0 8px}
    .eks-ringkas{display:block;font-size:13.5px;line-height:1.7;color:#667282}
    .eks-baca{display:inline-block;margin-top:14px;font-family:'Manrope',system-ui,sans-serif;font-size:12.5px;font-weight:800;color:#234288}
    .eks-baca i{display:inline-grid;place-items:center;width:22px;height:22px;border-radius:999px;background:#fdd100;
      color:#234288;font-style:normal;font-size:12px;margin-left:7px}
    .eks-tautan-baris{margin-top:24px}
    .eks-tautan{display:inline-block;font-family:'Manrope',system-ui,sans-serif;font-size:14px;font-weight:800;
      color:#234288;background:#fdd100;border-radius:12px;padding:14px 22px}
    .eks-kosong{border:1px dashed #c9d6ea;border-radius:18px;padding:24px;margin-top:26px;text-align:center;
      font-size:13.5px;line-height:1.75;color:#667282;background:#fff}
    .eks-isi-halaman{margin-top:20px;background:#fff;border:1px solid #e6ecf5;border-radius:22px;padding:24px 20px;font-size:15px;line-height:1.85;color:#3d5670}
    @media (min-width:768px){.eks-isi-halaman{padding:34px 38px}}
    .eks-isi-halaman h2,.eks-isi-halaman h3{font-family:'Manrope',system-ui,sans-serif;color:#234288;font-weight:800;margin:0 0 10px}
    .eks-isi-halaman p{margin:0 0 14px}
    .eks-isi-halaman img{border-radius:16px;max-width:100%;height:auto}
  </style>

  <div class="eks-blok">
    <h2 class="eks-h2">Daftar Ekstrakurikuler</h2>
    <span class="eks-garis"></span>
    <p class="eks-lead">Klik salah satu untuk melihat foto kegiatannya.</p>

    @if ($daftar->count())
      <div class="eks-grid">
        @foreach ($daftar as $e)
          @php $jumlah = $e->photos_count ?? $e->photos->count(); @endphp
          <a class="eks-kartu" href="{{ url('/ekskul/' . $e->slug) }}">
            <span class="eks-foto">
              @if ($e->sampul_url)
                <img src="{{ $e->sampul_url }}" alt="{{ $e->nama }}" loading="lazy">
              @else
                <span class="eks-ikon">{{ $e->ikon ?: '🏅' }}</span>
              @endif
            </span>
            <span class="eks-isi">
              <span class="eks-nama">{{ $e->nama }}</span>
              @if ($e->keterangan)<span class="eks-ket">{{ $e->keterangan }}</span>@endif
              <span class="eks-jumlah">{{ $jumlah }} foto kegiatan →</span>
            </span>
          </a>
        @endforeach
      </div>
    @else
      <div class="eks-kosong">Daftar ekstrakurikuler belum diisi. Tambahkan dari panel → <b>Konten → Ekstrakurikuler</b>.</div>
    @endif
  </div>

  <div class="eks-blok" id="tulisan">
    <h2 class="eks-h2">Kabar &amp; Informasi Ekskul</h2>
    <span class="eks-garis"></span>
    <p class="eks-lead">
      Tulisan terbaru berkategori <b>{{ $kategori->nama ?? 'Ekstrakurikuler' }}</b> — diisi dari panel
      (<b>Tulisan</b> → centang kategori <b>{{ $kategori->nama ?? 'Ekstrakurikuler' }}</b> + gambar utama).
    </p>

    @if ($postingan->count())
      <div class="eks-postingan">
        @foreach ($postingan as $t)
          <a class="eks-tulisan" href="{{ url('/berita/' . $t->slug) }}">
            @if ($t->gambar_url)<img src="{{ $t->gambar_url }}" alt="{{ $t->judul }}" loading="lazy">@endif
            <span class="eks-tulisan-isi">
              <span class="eks-tanggal">{{ $t->tanggal_indonesia }}</span>
              <span class="eks-judul-tulisan">{{ $t->judul }}</span>
              <span class="eks-ringkas">{{ $t->ringkas }}</span>
              <span class="eks-baca">Baca tulisan <i>→</i></span>
            </span>
          </a>
        @endforeach
      </div>
      @if ($kategori)
        <div class="eks-tautan-baris">
          <a class="eks-tautan" href="{{ url('/berita?kategori=' . $kategori->slug) }}">Lihat semua tulisan {{ $kategori->nama }} <span aria-hidden="true">→</span></a>
        </div>
      @endif
    @else
      <div class="eks-kosong">
        Belum ada tulisan berkategori <b>{{ $kategori->nama ?? 'Ekstrakurikuler' }}</b>.<br>
        Tambahkan lewat panel → <b>Tulisan</b> → centang kategori <b>{{ $kategori->nama ?? 'Ekstrakurikuler' }}</b>, lalu unggah <b>gambar utama</b>.
      </div>
    @endif
  </div>

  @if ($page && $page->body)
    <div class="eks-isi-halaman">{!! $page->body !!}</div>
  @endif
</section>
@endsection
