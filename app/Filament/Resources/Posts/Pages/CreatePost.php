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
        $data = $this->sisipkanFotoBaru($data);

        if (! empty($data['pilih_galeri'])) {
            $data['gambar_sampul'] = $data['pilih_galeri'];
        }
        unset($data['pilih_galeri'], $data['sisipkan_galeri']);

        return $data;
    }

    /** Foto yang diunggah lewat kolom "Unggah foto dari HP/komputer" disisipkan ke dalam isi. */
    protected function sisipkanFotoBaru(array $data): array
    {
        $foto = $data['foto_baru'] ?? [];
        unset($data['foto_baru']);

        if (is_string($foto)) { $foto = [$foto]; }
        if (! is_array($foto)) { return $data; }

        $tambahan = '';
        foreach ($foto as $path) {
            if (! is_string($path) || trim($path) === '') { continue; }
            $tambahan .= '<p><img src="' . asset('media/' . ltrim($path, '/')) . '" alt=""></p>';
        }
        if ($tambahan !== '') {
            $data['body'] = rtrim((string) ($data['body'] ?? '')) . $tambahan;
        }

        return $data;
    }
}
