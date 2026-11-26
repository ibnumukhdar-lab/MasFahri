<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbUjian extends Model
{
    protected $table = 'spmb_ujian';

    protected $fillable = [
        'registration_id', 'paket_id', 'status', 'skor', 'benar', 'total',
        'pelanggaran', 'jawaban', 'mulai_pada', 'selesai_pada',
    ];

    protected $casts = [
        'jawaban' => 'array',
        'mulai_pada' => 'datetime',
        'selesai_pada' => 'datetime',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(SpmbPaketSoal::class, 'paket_id');
    }

    public function getSisaDetikAttribute(): int
    {
        $batas = $this->mulai_pada->copy()->addMinutes((int) $this->paket->durasi_menit);

        return max(0, $batas->timestamp - now()->timestamp);
    }
}
