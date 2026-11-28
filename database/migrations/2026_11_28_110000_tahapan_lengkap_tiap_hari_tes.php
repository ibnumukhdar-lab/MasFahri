<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Satu HARI TES = seluruh tahapan dijalankan (TPA, BTQ, wawancara).
 * Migrasi ini melengkapi jadwal yang sudah ada agar tiap hari tes berisi
 * ketiga tahapan — panitia cukup menyesuaikan jamnya di panel.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('spmb_jadwal_tes')) {
            return;
        }

        $tahapan = [
            ['jenis' => 'btq', 'nama' => 'Baca Tulis Al-Qur\'an', 'jam_mulai' => '10:30', 'jam_selesai' => '11:30', 'urut' => 2, 'keterangan' => 'Praktik membaca, menulis, dan hafalan bersama penguji.'],
            ['jenis' => 'wawancara', 'nama' => 'Wawancara minat & bakat', 'jam_mulai' => '13:00', 'jam_selesai' => '15:00', 'urut' => 3, 'keterangan' => 'Wawancara bersama pendidik.'],
        ];

        // Ambil tiap (gelombang, tanggal) yang sudah punya jadwal akademik,
        // lalu lengkapi tahapan yang belum ada pada hari itu.
        $hari = DB::table('spmb_jadwal_tes')
            ->select('gelombang_id', 'tanggal', 'tempat')
            ->groupBy('gelombang_id', 'tanggal')
            ->get();

        foreach ($hari as $h) {
            foreach ($tahapan as $t) {
                $ada = DB::table('spmb_jadwal_tes')
                    ->where('gelombang_id', $h->gelombang_id)
                    ->whereDate('tanggal', $h->tanggal)
                    ->where('jenis', $t['jenis'])
                    ->exists();

                if ($ada) {
                    continue;
                }

                DB::table('spmb_jadwal_tes')->insert([
                    'gelombang_id' => $h->gelombang_id,
                    'jenis' => $t['jenis'],
                    'nama' => $t['nama'],
                    'tanggal' => $h->tanggal,
                    'jam_mulai' => $t['jam_mulai'],
                    'jam_selesai' => $t['jam_selesai'],
                    'tempat' => $h->tempat,
                    'keterangan' => $t['keterangan'],
                    'urut' => $t['urut'],
                    'aktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Tidak menghapus apa pun: baris yang ditambahkan bisa dihapus dari panel.
    }
};
