<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbPaketSoal extends Model
{
    protected $table = 'spmb_paket_soal';

    protected $fillable = ['judul', 'deskripsi', 'durasi_menit', 'mulai_pada', 'selesai_pada', 'aktif'];

    protected $casts = [
        'mulai_pada' => 'datetime',
        'selesai_pada' => 'datetime',
        'aktif' => 'boolean',
    ];

    public function soal(): HasMany
    {
        return $this->hasMany(SpmbSoal::class, 'paket_id')->orderBy('urut')->orderBy('id');
    }

    public function ujian(): HasMany
    {
        return $this->hasMany(SpmbUjian::class, 'paket_id');
    }

    /** Paket yang boleh dikerjakan sekarang (aktif + dalam jadwal). */
    public static function terbuka(): ?self
    {
        return static::query()
            ->where('aktif', true)
            ->where(fn ($q) => $q->whereNull('mulai_pada')->orWhere('mulai_pada', '<=', now()))
            ->where(fn ($q) => $q->whereNull('selesai_pada')->orWhere('selesai_pada', '>=', now()))
            ->latest('id')
            ->first();
    }

    public function getJadwalAttribute(): string
    {
        if (! $this->mulai_pada && ! $this->selesai_pada) {
            return 'Selama tes dibuka panitia';
        }

        $fmt = fn ($d) => $d?->translatedFormat('d M Y H:i') ?? '…';

        return $fmt($this->mulai_pada).' — '.$fmt($this->selesai_pada);
    }
}
