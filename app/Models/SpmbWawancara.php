<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Nilai wawancara minat & bakat (3 aspek). */
class SpmbWawancara extends Model
{
    protected $table = 'spmb_wawancara';

    protected $fillable = [
        'registration_id',
        'nilai_minat', 'nilai_bakat', 'nilai_komunikasi',
        'tanggal', 'penilai', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public const ASPEK = [
        'nilai_minat' => 'Minat',
        'nilai_bakat' => 'Bakat',
        'nilai_komunikasi' => 'Komunikasi',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /** Nilai wawancara = rata-rata aspek yang sudah diisi. */
    public function getSkorWawancaraAttribute(): ?int
    {
        $isi = [];
        foreach (array_keys(self::ASPEK) as $k) {
            if ($this->{$k} !== null && $this->{$k} !== '') {
                $isi[] = (int) $this->{$k};
            }
        }

        return $isi ? (int) round(array_sum($isi) / count($isi)) : null;
    }

    public function getLengkapAttribute(): bool
    {
        foreach (array_keys(self::ASPEK) as $k) {
            if ($this->{$k} === null) {
                return false;
            }
        }

        return true;
    }
}
