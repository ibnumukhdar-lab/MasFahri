<?php

namespace App\Console\Commands;

use App\Models\GalleryPhoto;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

/**
 * Pindahkan gambar lama WordPress ke server sendiri.
 * Mengganti URL https://smaitarafah.sch.id/wp-content/uploads/... menjadi
 * /media/unggahan/<nama> di badan tulisan, gambar sampul, halaman, dan galeri,
 * sekaligus mengunduh berkas yang belum ada.
 *
 *   php artisan smaita:media-lokal            (lihat rencana saja)
 *   php artisan smaita:media-lokal --jalan    (kerjakan)
 */
class MediaLokal extends Command
{
    protected $signature = 'smaita:media-lokal {--jalan : benar-benar unduh & ganti URL} {--pola=wp-content/uploads : pola URL lama yang diganti}';

    protected $description = 'Ganti URL gambar WordPress lama menjadi berkas lokal di public/media/unggahan';

    public function handle(): int
    {
        $jalan = (bool) $this->option('jalan');
        $tujuan = public_path('media/unggahan');
        File::ensureDirectoryExists($tujuan);

        $pola = '#https?://(?:www\.)?smaitarafah\.sch\.id/wp-content/uploads/([^\s"\'<>)]+)#i';
        $unduh = [];
        $ganti = 0;

        $callback = function (string $teks) use (&$unduh, &$ganti) {
            return preg_replace_callback('#https?://(?:www\.)?smaitarafah\.sch\.id/wp-content/uploads/[^\s"\'<>)]+#i', function ($m) use (&$unduh, &$ganti) {
                $nama = basename(parse_url($m[0], PHP_URL_PATH));
                if ($nama === '') return $m[0];
                $unduh[$m[0]] = $nama;
                $ganti++;
                return asset('media/unggahan/' . $nama);
            }, $teks);
        };

        // ---- tulisan ----
        foreach (Post::all() as $post) {
            $body = $callback((string) $post->body);
            $sampul = $post->gambar_sampul;
            if ($sampul && str_contains($sampul, 'wp-content/uploads')) {
                $nama = basename(parse_url($sampul, PHP_URL_PATH));
                $unduh[$sampul] = $nama;
                $sampul = 'unggahan/' . $nama;
                $ganti++;
            }
            if ($jalan && ($body !== $post->body || $sampul !== $post->gambar_sampul)) {
                $post->update(['body' => $body, 'gambar_sampul' => $sampul]);
            }
        }

        // ---- halaman ----
        foreach (Page::all() as $page) {
            $body = $callback((string) $page->body);
            if ($jalan && $body !== $page->body) $page->update(['body' => $body]);
        }

        $this->info(sprintf('URL gambar lama ditemukan: %d (%d berkas unik)', $ganti, count($unduh)));

        if (! $jalan) {
            $this->warn('Mode pratinjau. Jalankan ulang dengan --jalan untuk mengunduh & mengganti.');
            return self::SUCCESS;
        }

        $ok = 0; $gagal = 0;
        foreach ($unduh as $url => $nama) {
            if (File::exists($tujuan . '/' . $nama)) { $ok++; continue; }
            try {
                $isi = Http::timeout(60)->get($url)->body();
                if ($isi === '') { $gagal++; continue; }
                File::put($tujuan . '/' . $nama, $isi);
                $ok++;
            } catch (\Throwable $e) {
                $this->warn("gagal: $url");
                $gagal++;
            }
        }
        $this->info("Berkas siap: $ok, gagal: $gagal (folder public/media/unggahan)");

        // ---- pastikan foto galeri juga menunjuk berkas lokal yang ada ----
        $hilang = 0;
        foreach (GalleryPhoto::all() as $foto) {
            if (! File::exists($tujuan . '/' . basename($foto->berkas))) {
                $this->warn('foto galeri tanpa berkas: ' . $foto->berkas);
                $hilang++;
            }
        }
        if ($hilang) $this->warn("$hilang foto galeri belum punya berkas — unggah lewat panel admin.");

        return self::SUCCESS;
    }
}
