{{--
  Satu seksi beranda berisi SLIDER TULISAN berkategori:
  foto utama + judul tulisan (muncul saat kursor diarahkan / saat kartu disentuh) + klik menuju tulisannya.
  Parameter: b (baris BerandaBagian), tulisan (koleksi Post), kabut (bool), contoh (bool)
--}}
@php
    $kategori = $b->kategori;
    $namaKategori = $kategori ? $kategori->nama : 'Tulisan';
    $lebih = $kategori ? url('/berita?kategori=' . $kategori->slug) : url('/berita');
@endphp
<section class="ld-seksi {{ !empty($kabut) ? 'ld-kabut' : '' }}" id="seksi-{{ $b->id }}">
  <div class="ld-wrap">
    <div class="ld-judul-blok">
      @if (filled($b->subjudul))
        <h3 class="ld-kicker">{{ $b->subjudul }}</h3>
      @endif
      <h2 class="ld-judul">{{ $b->judul ?: $namaKategori }}</h2>
      @if (filled($b->teks))
        <p class="ld-lead">{!! $b->teks !!}</p>
      @endif
      @if (!empty($contoh))
        <p class="ld-catatan">Contoh tampilan — tulisan berkategori <b>{{ $namaKategori }}</b> belum ada.</p>
      @endif
    </div>

    @if ($tulisan->count())
      <div class="ld-slider" data-slider>
        <div class="ld-rel" data-rel>
          @foreach ($tulisan as $t)
            <figure class="ld-slide">
              <a class="ld-kartu" href="{{ url('/berita/' . $t->slug) }}">
                <span class="ld-gambar">
                  @if ($t->gambar_url)
                    <img src="{{ $t->gambar_url }}" alt="{{ $t->judul }}" loading="lazy">
                  @else
                    <span class="ld-tanpa-gambar">{{ $t->judul }}</span>
                  @endif
                  <span class="ld-lapisan">
                    <span class="ld-lapisan-kategori">{{ $namaKategori }}</span>
                    <span class="ld-lapisan-judul">{{ $t->judul }}</span>
                    <span class="ld-lapisan-tanggal">{{ $t->tanggal_indonesia }}</span>
                  </span>
                </span>
                <figcaption>{{ $t->judul }}</figcaption>
              </a>
            </figure>
          @endforeach
        </div>

        @if ($tulisan->count() > 1)
          <button type="button" class="ld-panah ld-panah--mundur" data-mundur aria-label="Tulisan sebelumnya">‹</button>
          <button type="button" class="ld-panah ld-panah--maju" data-maju aria-label="Tulisan berikutnya">›</button>
          <div class="ld-titik" data-titik></div>
        @endif
      </div>
    @else
      <div class="ld-kosong">
        <div class="ld-kosong-ikon" aria-hidden="true">📝</div>
        <div class="ld-kosong-judul">Belum ada tulisan berkategori {{ $namaKategori }}</div>
        <p>Seksi ini terisi otomatis dari <b>tulisan + gambar utama</b> yang kategorinya
           <b>{{ $namaKategori }}</b>. Tambah tulisan di panel → Berita/Tulisan, pilih kategorinya,
           lalu foto utamanya akan tampil di sini.</p>
      </div>
    @endif

    <div class="ld-tautan-baris">
      <a class="ld-tautan" href="{{ $lebih }}">Lihat semua tulisan {{ $namaKategori }} <span aria-hidden="true">→</span></a>
    </div>
  </div>
</section>
