<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbPaketSoal extends Model
{
    protected $table = 'spmb_paket_soal';

    protected $fillable = ['judul', 'mata_pelajaran', 'deskripsi', 'durasi_menit', 'urutan', 'wajib', 'kkm', 'mulai_pada', 'selesai_pada', 'aktif'];

    protected $casts = [
        'mulai_pada' => 'datetime',
        'selesai_pada' => 'datetime',
        'aktif' => 'boolean',
        'wajib' => 'boolean',
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
        return static::tersedia()->first();
    }

    /** Apakah paket ini sedang dalam jendela jadwal & aktif — dipakai untuk gerbang CBT. */
    public function dalamJadwal(): bool
    {
        $kini = now();

        return (bool) $this->aktif
            && ($this->mulai_pada === null || $this->mulai_pada <= $kini)
            && ($this->selesai_pada === null || $this->selesai_pada >= $kini);
    }

    /** Semua paket yang bisa dikerjakan sekarang (satu hari bisa beberapa mata pelajaran). */
    public static function tersedia(): \Illuminate\Support\Collection
    {
        $kini = now();

        return static::query()
            ->where('aktif', true)
            ->where(fn ($q) => $q->whereNull('mulai_pada')->orWhere('mulai_pada', '<=', $kini))
            ->where(fn ($q) => $q->whereNull('selesai_pada')->orWhere('selesai_pada', '>=', $kini))
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();
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
