<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Soal di bank soal — bisa dipakai ulang di paket mana pun. */
class SpmbBankSoal extends Model
{
    protected $table = 'spmb_bank_soal';

    protected $fillable = [
        'mata_pelajaran', 'teks',
        'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e',
        'kunci', 'tingkat', 'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public const TINGKAT = [
        'mudah' => 'Mudah',
        'sedang' => 'Sedang',
        'sulit' => 'Sulit',
    ];

    public const MAPEL = [
        'Matematika', 'Bahasa Indonesia', 'IPA', 'IPS', 'Bahasa Inggris',
        'Bahasa Arab', 'Tahfizh', 'Fiqih', 'SKI', "Qur'an Hadis", 'Akidah Akhlak',
        'PPKn', 'Seni Budaya', 'PJOK', 'Informatika',
    ];

    /** Pilihan jawaban [huruf => teks]. */
    public function getPilihanAttribute(): array
    {
        $hasil = [];
        foreach (['A' => $this->opsi_a, 'B' => $this->opsi_b, 'C' => $this->opsi_c, 'D' => $this->opsi_d, 'E' => $this->opsi_e] as $huruf => $teks) {
            if (filled($teks)) {
                $hasil[$huruf] = $teks;
            }
        }

        return $hasil;
    }
}
