<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumniCerita extends Model
{
    protected $table = 'alumni_cerita';

    protected $fillable = [
        'nama', 'wa', 'tahun_lulus', 'kelas_akhir', 'status_saat_ini', 'status_keterangan',
        'prestasi', 'pesan', 'instagram', 'foto', 'tampil', 'token', 'diverifikasi_pada',
    ];

    protected $casts = ['tampil' => 'boolean'];

    public const STATUS = [
        'kuliah' => 'Kuliah',
        'bekerja' => 'Bekerja',
        'wirausaha' => 'Wirausaha',
        'tahfizh' => 'Melanjutkan hafalan',
        'lainnya' => 'Lainnya',
    ];

    public function getFotoUrlAttribute(): string
    {
        return $this->foto ? '/media/' . $this->foto : '/media/logo/logo-96.png';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status_saat_ini] ?? '';
    }

    /** Baris di kartu: status + keterangan. */
    public function getStatusRangkumAttribute(): string
    {
        $label = $this->status_label;
        if (! $label) {
            return '';
        }

        return $this->status_keterangan ? "{$label} — {$this->status_keterangan}" : $label;
    }

    public function scopeTampil($q)
    {
        return $q->where('tampil', true)->whereNotNull('diverifikasi_pada');
    }
}
