<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    /** Kalau admin memilih foto dari galeri, itu yang dipakai sebagai gambar sampul. */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['pilih_galeri'])) {
            $data['gambar_sampul'] = $data['pilih_galeri'];
        }
        unset($data['pilih_galeri'], $data['sisipkan_galeri']);

        return $data;
    }
}
