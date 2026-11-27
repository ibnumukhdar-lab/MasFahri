<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Nilai BTQ (Baca Tulis Al-Qur'an) + Praktek Sholat.
 * Tiap aspek dinilai dari beberapa kriteria 0–100; nilai aspek = rata-rata kriteria.
 */
class SpmbNilaiQuran extends Model
{
    protected $table = 'spmb_nilai_quran';

    /** Kriteria per aspek — dipakai form panel & rekap. */
    public const ASPEK = [
        'baca' => [
            'label' => "Baca Al-Qur'an",
            'kriteria' => [
                'baca_tajwid' => 'Tajwid',
                'baca_makhraj' => 'Makhraj',
                'baca_kelancaran' => 'Kelancaran',
            ],
        ],
        'tulis' => [
            'label' => 'Tulis Arab',
            'kriteria' => [
                'tulis_ketepatan' => 'Ketepatan huruf',
                'tulis_kerapian' => 'Keindahan / kerapian tulisan',
            ],
        ],
        'sholat' => [
            'label' => 'Praktek Sholat',
            'kriteria' => [
                'sholat_bacaan' => 'Bacaan sholat',
                'sholat_fiqih' => 'Pemahaman fiqih',
                'sholat_gerakan' => 'Kesesuaian gerakan',
            ],
        ],
    ];

    protected $fillable = [
        'registration_id',
        'nilai_baca', 'nilai_tulis',
        'baca_tajwid', 'baca_makhraj', 'baca_kelancaran',
        'tulis_ketepatan', 'tulis_kerapian',
        'sholat_bacaan', 'sholat_fiqih', 'sholat_gerakan',
        'catatan', 'penilai',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /** Rata-rata kriteria (null bila belum ada satu pun diisi). */
    private function rataKriteria(array $kolom): ?int
    {
        $isi = [];
        foreach ($kolom as $k) {
            if ($this->{$k} !== null && $this->{$k} !== '') {
                $isi[] = (int) $this->{$k};
            }
        }

        return $isi ? (int) round(array_sum($isi) / count($isi)) : null;
    }

    /** Nilai aspek Baca Al-Qur'an (fallback ke kolom lama nilai_baca). */
    public function getSkorBacaAttribute(): ?int
    {
        return $this->rataKriteria(['baca_tajwid', 'baca_makhraj', 'baca_kelancaran'])
            ?? ($this->nilai_baca !== null ? (int) $this->nilai_baca : null);
    }

    /** Nilai aspek Tulis Arab (fallback ke kolom lama nilai_tulis). */
    public function getSkorTulisAttribute(): ?int
    {
        return $this->rataKriteria(['tulis_ketepatan', 'tulis_kerapian'])
            ?? ($this->nilai_tulis !== null ? (int) $this->nilai_tulis : null);
    }

    /** Nilai aspek Praktek Sholat. */
    public function getSkorSholatAttribute(): ?int
    {
        return $this->rataKriteria(['sholat_bacaan', 'sholat_fiqih', 'sholat_gerakan']);
    }

    /** Nilai BTQ keseluruhan = rata-rata aspek yang sudah dinilai. */
    public function getSkorBtqAttribute(): ?int
    {
        $aspek = array_filter([$this->skor_baca, $this->skor_tulis, $this->skor_sholat], fn ($v) => $v !== null);

        return $aspek ? (int) round(array_sum($aspek) / count($aspek)) : null;
    }

    /** Apakah semua kriteria sudah terisi. */
    public function getLengkapAttribute(): bool
    {
        foreach (self::ASPEK as $aspek) {
            foreach (array_keys($aspek['kriteria']) as $k) {
                if ($this->{$k} === null) {
                    return false;
                }
            }
        }

        return true;
    }
}
