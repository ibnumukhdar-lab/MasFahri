<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /** Kalau admin memilih foto dari galeri, itu yang dipakai sebagai gambar sampul. */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['pilih_galeri'])) {
            $data['gambar_sampul'] = $data['pilih_galeri'];
        }
        unset($data['pilih_galeri'], $data['sisipkan_galeri']);

        return $data;
    }
}
