<?php

namespace App\Console\Commands;

use App\Support\AkunSiakad;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Tarik akun SIAKAD ke tabel users panel /kelola.
 *
 *   php artisan siakad:tarik-akun --cek   (hanya uji koneksi & hitung akun)
 *   php artisan siakad:tarik-akun         (salin semua akun, peran bawaan panitia_spmb)
 */
class TarikAkunSiakad extends Command
{
    protected $signature = 'siakad:tarik-akun {--cek : hanya uji koneksi ke basis data SIAKAD}';

    protected $description = 'Salin akun SIAKAD ke panel /kelola (peran bawaan: Panitia SPMB)';

    public function handle(): int
    {
        if ($this->option('cek')) {
            try {
                $jumlah = DB::connection('siakad')->table('users')->count();
                $contoh = DB::connection('siakad')->table('users')->orderBy('id')->limit(3)->pluck('email')->all();

                $this->info("Koneksi SIAKAD OK — {$jumlah} akun. Contoh: ".implode(', ', $contoh));

                return self::SUCCESS;
            } catch (\Throwable $e) {
                $this->error('Koneksi SIAKAD GAGAL: '.$e->getMessage());

                return self::FAILURE;
            }
        }

        $hasil = AkunSiakad::tarikSemua();

        if ($hasil['galat']) {
            $this->error('Gagal menarik akun: '.$hasil['galat']);

            return self::FAILURE;
        }

        $this->info("Total {$hasil['total']} akun SIAKAD — {$hasil['baru']} baru, {$hasil['diperbarui']} diperbarui.");

        return self::SUCCESS;
    }
}
