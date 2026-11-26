<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbSoal extends Model
{
    protected $table = 'spmb_soal';

    protected $fillable = ['paket_id', 'urut', 'teks', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e', 'kunci'];

    public function paket(): BelongsTo
    {
        return $this->belongsTo(SpmbPaketSoal::class, 'paket_id');
    }

    /** Pilihan tersedia sebagai [huruf => teks]. */
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
