<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbBiodata extends Model
{
    protected $table = 'spmb_biodata';

    protected $fillable = [
        'registration_id', 'nik', 'nisn', 'tempat_lahir', 'tanggal_lahir', 'alamat',
        'anak_ke', 'jumlah_saudara', 'nama_ayah', 'pekerjaan_ayah', 'nama_ibu',
        'pekerjaan_ibu', 'telepon_rumah', 'hobi', 'foto',
    ];

    protected function casts(): array
    {
        return ['tanggal_lahir' => 'date'];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /** Persen lengkap biodata (berapa field terisi dari total). */
    public function getPersenAttribute(): int
    {
        $kolom = ['nik', 'nisn', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'anak_ke', 'jumlah_saudara',
            'nama_ayah', 'pekerjaan_ayah', 'nama_ibu', 'pekerjaan_ibu', 'telepon_rumah', 'hobi'];

        $terisi = collect($kolom)->filter(fn ($k) => filled($this->{$k}))->count();

        return (int) round(100 * $terisi / count($kolom));
    }
}
