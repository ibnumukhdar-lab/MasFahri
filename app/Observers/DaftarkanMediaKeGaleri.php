<?php

namespace App\Observers;

use App\Support\PustakaMedia;
use Illuminate\Database\Eloquent\Model;

/** Setiap postingan/halaman disimpan, gambarnya didaftarkan ke album galeri pustaka. */
class DaftarkanMediaKeGaleri
{
    public function saved(Model $model): void
    {
        PustakaMedia::daftarkan(
            [(string) ($model->gambar_sampul ?? ''), (string) ($model->body ?? '')],
            $model->judul ?? null
        );
    }
}
