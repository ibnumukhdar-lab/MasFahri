<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    /** Kolom bantu "sisipkan dari galeri" tidak disimpan ke database. */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->sisipkanFotoBaru($data);

        unset($data['sisipkan_galeri']);

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
