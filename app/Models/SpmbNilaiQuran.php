<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * KERANGKA input nilai baca tulis Al-Qur'an oleh panitia (fitur penuh menyusul).
 */
class SpmbNilaiQuran extends Model
{
    protected $table = 'spmb_nilai_quran';

    protected $fillable = ['registration_id', 'nilai_baca', 'nilai_tulis', 'catatan', 'penilai'];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
