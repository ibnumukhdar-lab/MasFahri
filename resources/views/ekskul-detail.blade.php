@extends('layouts.publik')
@section('judul', $ekskul->nama . ' — Ekstrakurikuler SMA IT Arafah Boarding School')
@section('deskripsi', \Illuminate\Support\Str::limit(strip_tags((string) $ekskul->keterangan), 155) ?: 'Ekstrakurikuler ' . $ekskul->nama . ' SMA IT Arafah Boarding School Sampit.')

@section('isi')
@include('partials.hero', ['kecil' => 'Ekstrakurikuler', 'judul' => $ekskul->nama, 'sub' => $ekskul->keterangan])

<section class="wrap py-10 md:py-16">
  <style>
    .ek-galeri{display:grid;gap:14px}
    @media (min-width:640px){.ek-galeri{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (min-width:1024px){.ek-galeri{grid-template-columns:repeat(3,minmax(0,1fr))}}
    .ek-foto{display:block;position:relative;border-radius:20px;overflow:hidden;border:1px solid #e6ecf5;background:#e9f7fe;
      transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease}
    .ek-foto:hover{transform:translateY(-3px);border-color:#fdd100;box-shadow:0 22px 40px -30px rgba(35,66,136,.6)}
    .ek-foto img{width:100%;aspect-ratio:4/3;object-fit:cover;display:block}
    .ek-ket{display:block;background:#fff;padding:11px 14px;font-family:'Manrope',system-ui,sans-serif;font-size:13px;
      font-weight:700;color:#234288;line-height:1.45}
    .ek-kosong{border:1px dashed #c9d6ea;border-radius:20px;padding:30px 22px;text-align:center;font-size:13.5px;
      line-height:1.75;color:#667282;background:#fff}
    .ek-kembali{display:inline-block;margin-top:26px;font-family:'Manrope',system-ui,sans-serif;font-size:13.5px;font-weight:800;color:#234288}
    .ek-kembali:hover{color:#182c47}
    .ek-blok{margin-top:40px}
    .ek-h2{font-family:'Manrope',system-ui,sans-serif;font-size:20px;font-weight:800;color:#234288;margin:0 0 4px}
    .ek-garis{display:block;width:52px;height:3px;border-radius:3px;background:#fdd100;margin:12px 0 22px}
    .ek-tulisan{display:grid;gap:14px}
    @media (min-width:768px){.ek-tulisan{grid-template-columns:repeat(3,minmax(0,1fr))}}
    .ek-tulisan-kartu{display:block;background:#fff;border:1px solid #e6ecf5;border-radius:18px;overflow:hidden}
    .ek-tulisan-kartu img{width:100%;aspect-ratio:16/10;object-fit:cover;display:block;background:#e9f7fe}
    .ek-tulisan-isi{display:block;padding:14px 16px 18px}
    .ek-tanggal{display:block;font-size:10.5px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#8b98a8}
    .ek-judul{display:block;font-family:'Manrope',system-ui,sans-serif;font-size:15px;font-weight:800;color:#234288;line-height:1.35;margin-top:7px}
  </style>

  @if ($ekskul->photos->count())
    <div class="ek-galeri">
      @foreach ($ekskul->photos as $f)
        <a class="ek-foto" href="{{ $f->url }}" target="_blank" rel="noopener">
          <img src="{{ $f->url }}" alt="{{ $f->judul ?: $ekskul->nama }}" loading="lazy">
          @if ($f->judul)<span class="ek-ket">{{ $f->judul }}</span>@endif
        </a>
      @endforeach
    </div>
  @else
    <div class="ek-kosong">
      Foto kegiatan <b>{{ $ekskul->nama }}</b> belum diunggah.<br>
      Tambahkan dari panel → <b>Konten → Ekstrakurikuler → {{ $ekskul->nama }} → bagian Foto kegiatan → Tambah foto</b>.
    </div>
  @endif

  @if ($postingan->count())
    <div class="ek-blok">
      <h2 class="ek-h2">Kabar {{ $ekskul->nama }}</h2>
      <span class="ek-garis"></span>
      <div class="ek-tulisan">
        @foreach ($postingan as $t)
          <a class="ek-tulisan-kartu" href="{{ url('/berita/' . $t->slug) }}">
            @if ($t->gambar_url)<img src="{{ $t->gambar_url }}" alt="{{ $t->judul }}" loading="lazy">@endif
            <span class="ek-tulisan-isi">
              <span class="ek-tanggal">{{ $t->tanggal_indonesia }}</span>
              <span class="ek-judul">{{ $t->judul }}</span>
            </span>
          </a>
        @endforeach
      </div>
    </div>
  @endif

  <a class="ek-kembali" href="{{ url('/ekskul') }}"><span aria-hidden="true">←</span> Semua ekstrakurikuler</a>
</section>
@endsection
