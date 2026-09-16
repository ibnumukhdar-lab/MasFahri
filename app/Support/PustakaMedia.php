<?php

namespace App\Support;

use App\Models\GalleryAlbum;
use App\Models\GalleryPhoto;
use Illuminate\Support\Str;

/**
 * Pustaka media bersama: semua gambar yang diunggah lewat postingan atau halaman
 * otomatis terdaftar di album galeri "Unggahan Tulisan & Halaman", sehingga bisa
 * dipakai ulang lewat tombol "Pilih dari galeri".
 */
class PustakaMedia
{
    public const ALBUM_SLUG = 'unggahan-tulisan-halaman';

    public static function album(): GalleryAlbum
    {
        return GalleryAlbum::firstOrCreate(
            ['slug' => self::ALBUM_SLUG],
            ['judul' => 'Unggahan Tulisan & Halaman', 'terbit' => true, 'keterangan' => 'Gambar yang diunggah lewat editor postingan/halaman.']
        );
    }

    /** Ambil nama berkas relatif disk media dari sebuah path atau URL. */
    public static function berkas(?string $nilai): ?string
    {
        if (! $nilai) return null;
        $bersih = Str::of($nilai)->replace(['\\', '"', "'"], '')->trim();
        if ($bersih->contains('unggahan/')) {
            $nama = basename(parse_url((string) $bersih, PHP_URL_PATH) ?: (string) $bersih);
            return $nama ? 'unggahan/' . $nama : null;
        }
        return null;
    }

    /** Daftarkan semua gambar yang dirujuk sebuah teks/HTML ke album pustaka. */
    public static function daftarkan(array|string|null $sumber, ?string $keterangan = null): int
    {
        $isi = is_array($sumber) ? implode("\n", array_filter($sumber)) : (string) $sumber;
        if (trim($isi) === '') return 0;

        preg_match_all('#(?:media/)?unggahan/[^\s"\'<>)]+\.(?:jpe?g|png|webp|gif|avif)#i', $isi, $m);
        if (! $m[0]) return 0;

        $album = self::album();
        $jumlah = 0;
        foreach (array_unique($m[0]) as $temu) {
            $berkas = 'unggahan/' . basename($temu);
            $ada = GalleryPhoto::where('gallery_album_id', $album->id)->where('berkas', $berkas)->exists();
            if ($ada) continue;
            GalleryPhoto::create([
                'gallery_album_id' => $album->id,
                'berkas' => $berkas,
                'judul' => $keterangan ? Str::limit($keterangan, 90) : 'Unggahan ' . now()->format('d/m/Y'),
                'urut' => (int) GalleryPhoto::where('gallery_album_id', $album->id)->max('urut') + 1,
            ]);
            $jumlah++;
        }
        return $jumlah;
    }
}
