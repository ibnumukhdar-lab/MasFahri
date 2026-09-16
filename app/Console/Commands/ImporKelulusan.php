<?php

namespace App\Console\Commands;

use App\Models\Graduate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Impor data kelulusan dari CSV hasil ekspor Google Sheet.
 * Kolom yang dikenali (baris pertama = judul kolom, huruf besar/kecil bebas):
 *   nisn, nama, kelas, status, pesan, tahun_ajaran
 *
 *   php artisan smaita:kelulusan "D:/smaita-web/kelulusan.csv" --tahun=2025/2026
 *   php artisan smaita:kelulusan "D:/smaita-web/kelulusan.csv" --tahun=2025/2026 --jalan
 */
class ImporKelulusan extends Command
{
    protected $signature = 'smaita:kelulusan {berkas : path CSV} {--tahun=2025/2026} {--jalan : benar-benar simpan}';

    protected $description = 'Impor data kelulusan (NISN) dari CSV agar halaman pengumuman kelulusan memakai data sendiri';

    public function handle(): int
    {
        $berkas = $this->argument('berkas');
        if (! File::exists($berkas)) {
            $this->error("Berkas tidak ada: $berkas");
            return self::FAILURE;
        }

        $tahun = $this->option('tahun');
        $baris = array_map('str_getcsv', file($berkas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        if (count($baris) < 2) {
            $this->error('CSV kosong atau hanya berisi judul kolom.');
            return self::FAILURE;
        }

        $judul = array_map(fn ($h) => strtolower(trim((string) $h)), array_shift($baris));
        $kolom = fn (string $nama) => array_search($nama, $judul, true);

        foreach (['nisn', 'nama'] as $wajib) {
            if ($kolom($wajib) === false) {
                $this->error("Kolom wajib '$wajib' tidak ditemukan. Judul kolom yang terbaca: " . implode(', ', $judul));
                return self::FAILURE;
            }
        }

        $siap = [];
        foreach ($baris as $b) {
            $nisn = trim((string) ($b[$kolom('nisn')] ?? ''));
            $nama = trim((string) ($b[$kolom('nama')] ?? ''));
            if ($nisn === '' || $nama === '') continue;
            $ambil = fn (string $k) => ($kolom($k) !== false && isset($b[$kolom($k)])) ? trim((string) $b[$kolom($k)]) : null;
            $siap[$nisn] = [
                'nama' => $nama,
                'kelas' => $ambil('kelas'),
                'status' => strtolower($ambil('status') ?: 'lulus') === 'lulus' ? 'lulus' : 'tidak_lulus',
                'pesan' => $ambil('pesan'),
                'tahun_ajaran' => $ambil('tahun_ajaran') ?: $tahun,
                'tampil' => true,
            ];
        }

        $this->info('Baris siap: ' . count($siap) . ' (tahun ajaran ' . $tahun . ')');
        foreach (array_slice(array_keys($siap), 0, 3, true) as $nisn) {
            $this->line("  contoh: $nisn → {$siap[$nisn]['nama']} ({$siap[$nisn]['status']})");
        }

        if (! $this->option('jalan')) {
            $this->warn('Mode pratinjau. Tambahkan --jalan untuk menyimpan ke database.');
            return self::SUCCESS;
        }

        foreach ($siap as $nisn => $data) {
            Graduate::updateOrCreate(['nisn' => $nisn, 'tahun_ajaran' => $data['tahun_ajaran']], $data);
        }
        $this->info('Tersimpan. Total data kelulusan: ' . Graduate::count());
        return self::SUCCESS;
    }
}
