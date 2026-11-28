<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Gelombang SPMB per tahun ajaran (Gelombang 1, Gelombang 2, …).
 * Jadwal tes masuk menempel pada gelombang (boleh lebih dari satu).
 */
class SpmbGelombang extends Model
{
    protected $table = 'spmb_gelombang';

    protected $fillable = [
        'tahun_ajaran', 'nama', 'urut', 'mulai', 'selesai', 'keterangan', 'aktif',
    ];

    protected $casts = [
        'mulai' => 'date',
        'selesai' => 'date',
        'aktif' => 'boolean',
        'urut' => 'integer',
    ];

    public function jadwalTes(): HasMany
    {
        return $this->hasMany(SpmbJadwalTes::class, 'gelombang_id')
            ->orderBy('tanggal')->orderBy('jam_mulai')->orderBy('id');
    }

    public function pendaftar(): HasMany
    {
        return $this->hasMany(Registration::class, 'gelombang_id');
    }

    /** Gelombang aktif milik satu tahun ajaran, urut. */
    public static function untukTahun(string $tahunAjaran): \Illuminate\Support\Collection
    {
        return static::query()
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('aktif', true)
            ->orderBy('urut')->orderBy('mulai')
            ->get();
    }

    /** Gelombang yang periodenya sedang berjalan hari ini (null bila tidak ada). */
    public static function berjalan(?string $tahunAjaran = null): ?self
    {
        $kini = now()->toDateString();

        return static::query()
            ->where('aktif', true)
            ->when($tahunAjaran, fn ($q) => $q->where('tahun_ajaran', $tahunAjaran))
            ->where(fn ($q) => $q->whereNull('mulai')->orWhereDate('mulai', '<=', $kini))
            ->where(fn ($q) => $q->whereNull('selesai')->orWhereDate('selesai', '>=', $kini))
            ->orderBy('mulai')
            ->first();
    }

    /** Gelombang berikutnya yang belum dibuka. */
    public static function berikutnya(?string $tahunAjaran = null): ?self
    {
        return static::query()
            ->where('aktif', true)
            ->when($tahunAjaran, fn ($q) => $q->where('tahun_ajaran', $tahunAjaran))
            ->whereDate('mulai', '>', now()->toDateString())
            ->orderBy('mulai')
            ->first();
    }

    public function sedangBerjalan(): bool
    {
        $kini = now()->startOfDay();

        return (bool) $this->aktif
            && ($this->mulai === null || $this->mulai->lte($kini))
            && ($this->selesai === null || $this->selesai->gte($kini));
    }

    public function belumDibuka(): bool
    {
        return $this->mulai !== null && $this->mulai->gt(now()->startOfDay());
    }

    public function sudahDitutup(): bool
    {
        return $this->selesai !== null && $this->selesai->lt(now()->startOfDay());
    }

    public function getStatusLabelAttribute(): string
    {
        return match (true) {
            ! $this->aktif => 'Tidak aktif',
            $this->belumDibuka() => 'Belum dibuka',
            $this->sudahDitutup() => 'Pendaftaran ditutup',
            default => 'Sedang dibuka',
        };
    }

    /** "1 Desember 2026 – 28 Februari 2027" */
    public function getRentangLabelAttribute(): string
    {
        $f = fn ($d) => $d?->locale('id')->translatedFormat('j F Y');
        $mulai = $f($this->mulai) ?? '—';
        $selesai = $f($this->selesai) ?? 'seterusnya';

        return $mulai.' – '.$selesai;
    }

    /** Tes terakhir pada gelombang ini (dipakai hitungan H+1 pengumuman). */
    public function tesTerakhir(): ?SpmbJadwalTes
    {
        return $this->jadwalTes()->where('aktif', true)->reorder()->orderByDesc('tanggal')->first();
    }

    /**
     * Jadwal dikelompokkan per HARI TES: satu hari berisi seluruh tahapan
     * (TPA, BTQ, wawancara) yang dilaksanakan berurutan pada hari itu.
     *
     * @return \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, SpmbJadwalTes>>
     */
    public function jadwalPerHari(): \Illuminate\Support\Collection
    {
        return $this->jadwalTes()
            ->where('aktif', true)
            ->get()
            ->groupBy(fn (SpmbJadwalTes $j) => $j->tanggal?->toDateString() ?? 'menyusul')
            ->sortKeys();
    }

    /** Hari tes pertama pada gelombang (untuk chip hero & kartu tes). */
    public function hariTesPertama(): ?SpmbJadwalTes
    {
        return $this->jadwalTes()->where('aktif', true)->reorder()->orderBy('tanggal')->first();
    }
}
