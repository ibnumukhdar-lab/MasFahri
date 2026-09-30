{{--
  Isi popup "Pilih dari galeri".

  $foto   : koleksi App\Models\GalleryPhoto
  $target : jalur state Livewire yang diisi, mis. 'data.gambar'
  $bentuk : 'berkas' → kolom unggah (state harus berupa DAFTAR berkas) · 'teks' → kolom teks/select (state berupa teks)

  Catatan penting: FileUpload Filament menuntut $wire.set(...) diberi ARRAY.
  Kalau diberi teks, panel menampilkan galat "Terjadi kesalahan saat mencoba memuat halaman ini".
--}}
@php
    $target = $target ?? 'data.pilih_galeri';
    $bentuk = $bentuk ?? 'teks';

    // Foto yang sedang dipakai pada bagian ini (kalau ada) → diberi tanda "Dipakai".
    $nilai = data_get($this ?? null, 'data.gambar')
        ?? data_get($this ?? null, 'data.gambar_sampul')
        ?? data_get($this ?? null, 'data.pilih_galeri');
    $terpilih = collect(is_array($nilai) ? $nilai : [$nilai])
        ->filter(fn ($v) => is_string($v) && trim($v) !== '')
        ->map(fn ($v) => ltrim(trim($v), '/'))
        ->all();

    // Judul "Dokumentasi" tidak berguna untuk membedakan foto → pakai nama berkasnya.
    $namaFoto = function ($f) {
        $judul = trim((string) $f->judul);
        $berkas = basename((string) $f->berkas);
        if ($judul === '' || in_array(mb_strtolower($judul), ['dokumentasi', 'foto', 'gambar', 'img'], true)) {
            return ['utama' => $berkas, 'kecil' => null];
        }

        return ['utama' => $judul, 'kecil' => $judul === $berkas ? null : $berkas];
    };
@endphp

<style>
  .pg-bungkus{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
  .pg-atas{display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-bottom:14px}
  .pg-cari{flex:1;min-width:180px;border:1px solid #e6ecf5;border-radius:11px;padding:10px 13px;font-size:13px;color:#212529;background:#fff}
  .pg-cari:focus{outline:none;border-color:#234288;box-shadow:0 0 0 3px rgba(35,66,136,.12)}
  .pg-info{font-size:11.5px;color:#667282;font-weight:600}
  .pg-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;max-height:56vh;overflow-y:auto;padding:2px}
  @media (min-width:640px){.pg-grid{grid-template-columns:repeat(4,minmax(0,1fr))}}
  @media (min-width:1024px){.pg-grid{grid-template-columns:repeat(5,minmax(0,1fr))}}
  .pg-kartu{position:relative;display:flex;flex-direction:column;overflow:hidden;border:1px solid #e6ecf5;border-radius:14px;
    background:#fff;text-align:left;cursor:pointer;padding:0;transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease}
  .pg-kartu:hover{transform:translateY(-2px);border-color:#fdd100;box-shadow:0 16px 28px -20px rgba(35,66,136,.65)}
  .pg-kartu img{width:100%;aspect-ratio:4/3;object-fit:cover;display:block;background:#e9f7fe}
  .pg-teks{padding:7px 9px 9px}
  .pg-utama{display:block;font-size:11.5px;font-weight:700;color:#212529;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .pg-kecil{display:block;font-size:10px;color:#8b98a8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:1px}
  .pg-pakai{position:absolute;top:7px;right:7px;background:#fdd100;color:#234288;font-size:10px;font-weight:800;
    border-radius:999px;padding:3px 8px;opacity:0;transition:opacity .18s ease}
  .pg-kartu:hover .pg-pakai{opacity:1}
  .pg-kartu--dipakai{border-color:#234288;box-shadow:0 0 0 2px rgba(35,66,136,.18)}
  .pg-kartu--dipakai .pg-pakai{opacity:1;background:#234288;color:#fff}
  .pg-kosong{border:1px dashed #c9d6ea;border-radius:14px;padding:24px;text-align:center;font-size:13px;color:#667282;line-height:1.7}
  .pg-taketemu{grid-column:1/-1;border:1px dashed #c9d6ea;border-radius:14px;padding:20px;text-align:center;font-size:13px;color:#667282}
</style>

<div class="pg-bungkus" x-data="{ cari: '' }">
  <div class="pg-atas">
    <input type="search" class="pg-cari" x-model="cari" placeholder="Cari nama foto… (mis. asrama, prestasi, richa)">
    <span class="pg-info">{{ $foto->count() }} foto terbaru · klik untuk memakai</span>
  </div>

  @if ($foto->isEmpty())
    <div class="pg-kosong">
      Galeri masih kosong. Unggah foto lewat menu <b>Galeri</b>, atau langsung pada kolom unggah di atas.
    </div>
  @else
    <div class="pg-grid">
      @foreach ($foto as $f)
        @php
          $n = $namaFoto($f);
          $dipakai = in_array(ltrim((string) $f->berkas, '/'), $terpilih, true);
        @endphp
        <button type="button"
                class="pg-kartu {{ $dipakai ? 'pg-kartu--dipakai' : '' }}"
                title="{{ $n['utama'] }}{{ $n['kecil'] ? ' · '.$n['kecil'] : '' }}"
                data-cari="{{ Str::lower($n['utama'].' '.($n['kecil'] ?? '').' '.$f->berkas) }}"
                x-show="cari === '' || $el.dataset.cari.includes(cari.toLowerCase())"
                x-on:click="$wire.set(@js($target), @js($bentuk === 'berkas' ? [$f->berkas] : $f->berkas)); $wire.unmountAction()">
          <img src="{{ $f->url }}" alt="{{ $n['utama'] }}" loading="lazy">
          <span class="pg-teks">
            <span class="pg-utama">{{ $n['utama'] }}</span>
            @if ($n['kecil'])<span class="pg-kecil">{{ $n['kecil'] }}</span>@endif
          </span>
          <span class="pg-pakai">{{ $dipakai ? 'Dipakai' : 'Pakai' }}</span>
        </button>
      @endforeach

      <div class="pg-taketemu" x-show="cari !== '' && $root.querySelectorAll('.pg-kartu:not([style*=&quot;display: none&quot;])').length === 0">
        Tidak ada foto yang cocok dengan “<span x-text="cari"></span>”.
      </div>
    </div>
  @endif
</div>
