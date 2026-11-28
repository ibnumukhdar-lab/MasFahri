<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu jadwal tes masuk pada sebuah gelombang SPMB.
 * Boleh banyak per gelombang (mis. TPA pagi, BTQ siang, wawancara esok hari).
 */
class SpmbJadwalTes extends Model
{
    protected $table = 'spmb_jadwal_tes';

    /** Jenis jadwal → label & ikon tampilan. */
    public const JENIS = [
        'akademik' => 'Tes akademik (CBT)',
        'btq' => 'Baca Tulis Al-Qur\'an',
        'wawancara' => 'Wawancara',
        'lain' => 'Lainnya',
    ];

    protected $fillable = [
        'gelombang_id', 'jenis', 'nama', 'tanggal', 'jam_mulai', 'jam_selesai',
        'tempat', 'keterangan', 'urut', 'aktif',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'aktif' => 'boolean',
        'urut' => 'integer',
    ];

    public function gelombang(): BelongsTo
    {
        return $this->belongsTo(SpmbGelombang::class, 'gelombang_id');
    }

    public function paket(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SpmbPaketSoal::class, 'jadwal_tes_id');
    }

    /** "Senin, 14 Desember 2026 · 08.00–10.00" */
    public function getWaktuLabelAttribute(): string
    {
        $teks = $this->tanggal
            ? $this->tanggal->locale('id')->translatedFormat('l, j F Y')
            : 'Tanggal menyusul';

        $jam = $this->jamLabel();

        return $jam ? $teks.' · '.$jam : $teks;
    }

    /** "08.00–10.00" (null bila jam belum diisi). */
    public function jamLabel(): ?string
    {
        $mulai = $this->jam_mulai ? substr((string) $this->jam_mulai, 0, 5) : null;
        $selesai = $this->jam_selesai ? substr((string) $this->jam_selesai, 0, 5) : null;

        return match (true) {
            $mulai && $selesai => str_replace(':', '.', $mulai).'–'.str_replace(':', '.', $selesai),
            $mulai => str_replace(':', '.', $mulai).' WIB',
            default => null,
        };
    }

    public function getJenisLabelAttribute(): string
    {
        return self::JENIS[$this->jenis] ?? ucfirst((string) $this->jenis);
    }

    /** Awal & akhir jendela pengerjaan (dipakai paket CBT yang ditautkan). */
    public function jendela(): array
    {
        $tanggal = $this->tanggal?->toDateString();

        if (! $tanggal) {
            return [null, null];
        }

        $mulai = $this->jam_mulai ? $tanggal.' '.$this->jam_mulai : $tanggal.' 00:00:00';
        $selesai = $this->jam_selesai ? $tanggal.' '.$this->jam_selesai : $tanggal.' 23:59:59';

        return [$mulai, $selesai];
    }
}
