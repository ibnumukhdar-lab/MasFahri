{{--
  Seksi beranda gaya Thursina: judul di tengah (kicker kapital + judul besar + lead),
  lalu slider foto berkategori selebar kolom.
  Parameter: id, kicker, judul, lead, chips[], kategori, kabut (bool), catatan, foto (koleksi)
--}}
<section class="ld-seksi {{ !empty($kabut) ? 'ld-kabut' : '' }}" id="{{ $id }}">
  <div class="ld-wrap">
    <div class="ld-judul-blok">
      <h3 class="ld-kicker">{{ $kicker }}</h3>
      <h2 class="ld-judul">{{ $judul }}</h2>
      <p class="ld-lead">{!! $lead !!}</p>
      @if (!empty($catatan))
        <p class="ld-catatan">{!! $catatan !!}</p>
      @endif
    </div>

    @if (!empty($chips))
      <ul class="ld-chips">
        @foreach ($chips as $c)
          <li>{{ $c }}</li>
        @endforeach
      </ul>
    @endif

    <div class="ld-slider" data-slider>
      @if ($foto->count())
        <div class="ld-rel" data-rel>
          @foreach ($foto as $f)
            <figure class="ld-slide">
              <a href="{{ $f->url }}" target="_blank" rel="noopener">
                <span class="ld-gambar"><img src="{{ $f->url }}" alt="{{ $f->judul ?: $judul }}" loading="lazy"></span>
                <figcaption>{{ $f->judul ?: $judul }}</figcaption>
              </a>
            </figure>
          @endforeach
        </div>

        @if ($foto->count() > 1)
          <button type="button" class="ld-panah ld-panah--mundur" data-mundur aria-label="Foto sebelumnya">‹</button>
          <button type="button" class="ld-panah ld-panah--maju" data-maju aria-label="Foto berikutnya">›</button>
          <div class="ld-titik" data-titik></div>
        @endif
      @else
        <div class="ld-kosong">
          <div class="ld-kosong-ikon" aria-hidden="true">🖼️</div>
          <div class="ld-kosong-judul">Foto {{ strtolower($kicker) }} belum diunggah</div>
          <p>Bagian ini otomatis terisi begitu foto diunggah di panel — album tinggal dipilih kategorinya
             <b>{{ $kategori }}</b>.</p>
        </div>
      @endif
    </div>

    <div class="ld-tautan-baris">
      <a class="ld-tautan" href="{{ $lebih ?? url('/galeri') }}">Lihat semua foto <span aria-hidden="true">→</span></a>
    </div>
  </div>
</section>
