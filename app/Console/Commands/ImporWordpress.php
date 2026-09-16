<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\GalleryAlbum;
use App\Models\GalleryPhoto;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use App\Models\TeacherLink;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Impor konten dari WordPress (hasil ekspor: smaita-export.json).
 *   php artisan smaita:impor "D:/smaita-web/smaita-export.json" --media
 */
class ImporWordpress extends Command
{
    protected $signature = 'smaita:impor {berkas : path JSON hasil ekspor} {--media : unduh juga gambar yang dirujuk konten} {--halaman : impor halaman profil} {--tulisan : impor tulisan}';

    protected $description = 'Impor kategori, tulisan, halaman, tautan guru, dan media dari ekspor WordPress smaitarafah.sch.id';

    /** halaman WordPress yang MASIH dipakai (sisanya = arsip, tidak diimpor) */
    private array $halamanDipakai = ['tentang-sma-it-arafah', 'visi', 'kurikulum', 'tahfizh', 'diniyah', 'galeri', 'toolsguru', 'ekskul'];

    public function handle(): int
    {
        $berkas = $this->argument('berkas');
        if (! File::exists($berkas)) {
            $this->error("Berkas tidak ada: $berkas");
            return self::FAILURE;
        }

        $data = json_decode(File::get($berkas), true, 512, JSON_THROW_ON_ERROR);
        $semua = $this->option('halaman') === false && $this->option('tulisan') === false;
        $media = [];

        // ---------- kategori ----------
        foreach ($data['kategori'] ?? [] as $k) {
            Category::updateOrCreate(['slug' => $k['slug']], [
                'nama' => $k['nama'],
                'deskripsi' => $k['deskripsi'] ?? null,
                'warna' => match ($k['slug']) {
                    'berita' => 'sky', 'gagasan' => 'emerald', 'prestasi' => 'amber', 'literasi' => 'violet', default => 'slate',
                },
            ]);
        }
        $this->info('Kategori: ' . Category::count());

        // ---------- tulisan ----------
        if ($semua || $this->option('tulisan')) {
            $n = 0;
            foreach ($data['tulisan'] ?? [] as $t) {
                $gambar = $t['gambar'] ?? '';
                if ($gambar) $media[$gambar] = true;

                $post = Post::updateOrCreate(['wp_id' => $t['wp_id']], [
                    'judul' => $t['judul'],
                    'slug' => $t['slug'] ?: 'tulisan-' . $t['wp_id'],
                    'ringkasan' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $t['isi']))), 180),
                    'body' => $this->rapikan($t['isi'] ?? ''),
                    'status' => 'terbit',
                    'terbit_pada' => $t['tanggal'],
                    'gambar_sampul' => $gambar,
                ]);
                $id = Category::whereIn('nama', $t['kategori'] ?? [])->pluck('id');
                $post->categories()->sync($id);
                $n++;
            }
            $this->info("Tulisan: $n");
        }

        // ---------- halaman ----------
        if ($semua || $this->option('halaman')) {
            $n = 0;
            foreach ($data['halaman'] ?? [] as $h) {
                if (! in_array($h['slug'], $this->halamanDipakai, true)) continue;
                $blok = $h['blocks_unik'] ?? [];
                $html = $this->blokKeHtml($blok, $media);
                $urut = array_search($h['slug'], $this->halamanDipakai, true);
                Page::updateOrCreate(['slug' => $h['slug']], [
                    'judul' => $h['judul'],
                    'body' => $html,
                    'blocks' => $blok,
                    'status' => 'terbit',
                    'urut' => is_int($urut) ? $urut : 99,
                    'tampil_di_menu' => in_array($h['slug'], ['tentang-sma-it-arafah', 'visi', 'kurikulum', 'tahfizh', 'diniyah'], true),
                ]);
                $n++;
            }
            $this->info("Halaman: $n (dari " . count($data['halaman'] ?? []) . " halaman WordPress; sisanya arsip)");
        }

        // ---------- tautan guru ----------
        $urut = 0;
        foreach ($data['tautan_guru'] ?? [] as $u) {
            TeacherLink::updateOrCreate(['url' => $u], [
                'judul' => TeacherLink::where('url', $u)->value('judul') ?: $this->tebakJudul($u, $urut),
                'kelompok' => 'Perangkat & Panduan',
                'urut' => $urut++,
            ]);
        }
        $this->info('Tautan guru: ' . TeacherLink::count());

        // ---------- pengaturan dasar ----------
        Setting::simpan([
            'nama_sekolah' => $data['situs']['nama'] ?? 'SMA IT Arafah Boarding School',
            'tagline' => $data['situs']['tagline'] ?? 'Beriman, Berakhlak, Cerdas',
            'alamat' => 'Jalan Setia Usaha No. 4, Sampit — Kabupaten Kotawaringin Timur, Kalimantan Tengah',
            'wa_admin' => '6285356002524',
            'tahun_ajaran' => '2026/2027',
        ]);

        // ---------- galeri (satu album dari foto yang benar-benar ada) ----------
        $album = GalleryAlbum::firstOrCreate(['slug' => 'kegiatan-prestasi'], [
            'judul' => 'Kegiatan & Prestasi Siswa', 'tanggal' => now()->toDateString(), 'terbit' => true,
        ]);
        $i = 0;
        foreach (array_slice(array_keys($media), 0, 12) as $url) {
            GalleryPhoto::firstOrCreate(
                ['gallery_album_id' => $album->id, 'berkas' => 'unggahan/' . basename(parse_url($url, PHP_URL_PATH))],
                ['judul' => 'Dokumentasi', 'urut' => $i++]
            );
        }
        $this->info('Foto galeri dicatat: ' . $album->photos()->count() . ' (berkas menyusul lewat --media)');

        if ($this->option('media')) {
            $this->unduhMedia(array_keys($media));
        }

        $this->newLine();
        $this->info('Selesai. Jalankan: php artisan smaita:impor ... --media untuk mengunduh gambarnya.');
        return self::SUCCESS;
    }

    /** Buang sisa markup Elementor yang tidak perlu. */
    private function rapikan(string $html): string
    {
        $html = preg_replace('#<(script|style|iframe|form|input|svg)\b.*?</\1>#is', '', $html);
        $html = preg_replace('#<(script|style|iframe|form|input|svg)\b[^>]*>#is', '', (string) $html);
        $html = preg_replace('#\s(?:class|id|aria-[a-z\-]+|data-[a-z\-]+|dir|role)="[^"]*"#i', '', (string) $html);
        $html = preg_replace('#style="(?!text-align)[^"]*"#i', '', (string) $html);
        return trim((string) $html);
    }

    /** Ubah daftar blok semantik jadi HTML rapi untuk halaman statis. */
    private function blokKeHtml(array $blok, array &$media): string
    {
        $keluar = '';
        foreach ($blok as $b) {
            if (($b['t'] ?? '') === 'section') {
                $keluar .= '<section>' . $this->blokKeHtml($b['blocks'] ?? [], $media) . '</section>';
                continue;
            }
            $keluar .= match ($b['t']) {
                'heading' => '<h' . ($b['lv'] ?? 2) . '>' . e($b['text'] ?? '') . '</h' . ($b['lv'] ?? 2) . '>',
                'text' => $this->rapikan($b['html'] ?? ''),
                'button' => ! empty($b['url']) ? '<p><a class="tombol" href="' . e($b['url']) . '">' . e($b['text'] ?? '') . '</a></p>' : '',
                'image' => $this->gambar($b['url'] ?? '', $media, $b['alt'] ?? ''),
                'gallery' => '<div class="galeri">' . implode('', array_map(fn ($g) => $this->gambar($g['url'] ?? '', $media), $b['images'] ?? [])) . '</div>',
                'card' => '<article class="kartu"><h3>' . e($b['title'] ?? '') . '</h3><p>' . e($b['text'] ?? '') . '</p></article>',
                'list' => '<ul>' . implode('', array_map(fn ($i) => '<li>' . e($i) . '</li>', $b['items'] ?? [])) . '</ul>',
                'video' => ! empty($b['url']) ? '<p><a class="tombol" href="' . e($b['url']) . '">Tonton video</a></p>' : '',
                default => '',
            };
        }
        return $keluar;
    }

    private function gambar(string $url, array &$media, string $alt = ''): string
    {
        if ($url === '') return '';
        $media[$url] = true;
        $nama = basename(parse_url($url, PHP_URL_PATH));
        return '<img src="' . e(asset('media/unggahan/' . $nama)) . '" alt="' . e($alt) . '" loading="lazy">';
    }

    /** Unduh gambar yang dirujuk konten ke public/media/unggahan. */
    private function unduhMedia(array $urls): void
    {
        $tujuan = public_path('media/unggahan');
        File::ensureDirectoryExists($tujuan);
        $ok = 0;
        foreach ($urls as $url) {
            if (! is_string($url) || ! str_starts_with($url, 'http')) continue;
            $nama = basename(parse_url($url, PHP_URL_PATH));
            if ($nama === '' || File::exists($tujuan . '/' . $nama)) { $ok++; continue; }
            try {
                $isi = Http::timeout(45)->get($url)->body();
                if ($isi !== '') { File::put($tujuan . '/' . $nama, $isi); $ok++; }
            } catch (\Throwable $e) {
                $this->warn("gagal unduh: $url");
            }
        }
        $this->info("Media terunduh: $ok berkas di public/media/unggahan");
    }

    private function tebakJudul(string $url, int $urut): string
    {
        return match (true) {
            str_contains($url, 'canva.site') => 'AI Prompt Generator #' . ($urut + 1),
            str_contains($url, 'kemdikbud') => 'Ruang GTK Kemendikbud',
            str_contains($url, 'drive.google.com/file') => 'Berkas Panduan Guru',
            str_contains($url, 'folders') => 'Folder Perangkat Pembelajaran',
            str_contains($url, 'spreadsheets') => 'Student Roots Aggregator',
            str_contains($url, 'document') => 'Dokumen Panduan',
            default => 'Tautan Guru',
        };
    }
}
