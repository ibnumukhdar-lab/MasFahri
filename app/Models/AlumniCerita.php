<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AlumniCerita extends Model
{
    protected $table = 'alumni_cerita';

    protected $fillable = [
        'nama', 'slug', 'wa', 'tahun_lulus', 'kelas_akhir', 'status_saat_ini', 'status_keterangan',
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

    protected static function booted(): void
    {
        // isi slug dari nama bila kosong (dipakai URL halaman cerita)
        static::saving(function (AlumniCerita $cerita) {
            if (empty($cerita->slug) && ! empty($cerita->nama)) {
                $cerita->slug = static::slugUnik($cerita->nama);
            }
        });
    }

    public static function slugUnik(string $nama, ?int $kecuali = null): string
    {
        $dasar = Str::slug($nama) ?: 'alumni';
        $slug = $dasar;
        $i = 2;
        while (static::where('slug', $slug)->when($kecuali, fn ($q) => $q->where('id', '!=', $kecuali))->exists()) {
            $slug = $dasar . '-' . $i++;
        }

        return $slug;
    }

    /** Isi cerita untuk halaman khusus (paragraf per bagian). */
    public function getBagianCeritaAttribute(): array
    {
        return array_values(array_filter(array_map('trim', explode("\n", trim((string) $this->pesan)))));
    }

    public function getFotoUrlAttribute(): string
    {
        return $this->foto ? '/media/' . $this->foto : '/media/logo/logo-96.png';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status_saat_ini] ?? '';
    }

    public function getStatusRangkumAttribute(): string
    {
        $label = $this->status_label;
        if (! $label) {
            return '';
        }

        return $this->status_keterangan ? "{$label} — {$this->status_keterangan}" : $label;
    }

    public function getUrlHalamanAttribute(): string
    {
        return '/alumni-cerita/' . $this->slug;
    }

    public function getIstagramLinkAttribute(): ?string
    {
        return $this->instagram ? 'https://instagram.com/' . trim($this->instagram, '@') : null;
    }

    public function scopeTampil($q)
    {
        return $q->where('tampil', true)->whereNotNull('diverifikasi_pada');
    }
}
