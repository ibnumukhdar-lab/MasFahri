<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbFormulir extends Model
{
    protected $table = 'spmb_formulir';

    protected $fillable = [
        'registration_id', 'nama_panggilan', 'nisn', 'nik', 'tempat_lahir', 'tanggal_lahir',
        'golongan_darah', 'suku_bangsa', 'anak_ke', 'dari_bersaudara',
        'alamat_jalan', 'kelurahan', 'kecamatan',
        'ayah_nama', 'ayah_ttl', 'ayah_pendidikan', 'ayah_pekerjaan', 'ayah_penghasilan', 'ayah_telepon',
        'ibu_nama', 'ibu_ttl', 'ibu_pendidikan', 'ibu_pekerjaan', 'ibu_penghasilan', 'ibu_telepon',
        'foto_latar_merah', 'disimpan_pada', 'terkirim_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'disimpan_pada' => 'datetime',
            'terkirim_pada' => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /** URL publik pas foto latar merah (disk media). */
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto_latar_merah ? asset('media/' . $this->foto_latar_merah) : null;
    }
}
