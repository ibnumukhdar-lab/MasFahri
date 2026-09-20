<?php

namespace App\Filament\Resources\Beranda\Pages;

use App\Filament\Resources\Beranda\BerandaResource;
use App\Models\BerandaBagian;
use Filament\Resources\Pages\CreateRecord;

class CreateBeranda extends CreateRecord
{
    protected static string $resource = BerandaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pilihan dari galeri mengalahkan berkas yang diunggah.
        if (! empty($data['pilih_galeri'])) {
            $data['gambar'] = $data['pilih_galeri'];
        }
        unset($data['pilih_galeri']);

        // Bagian baru ditaruh paling bawah supaya urutan yang ada tidak terganggu.
        if (empty($data['urutan'])) {
            $data['urutan'] = (int) BerandaBagian::query()->max('urutan') + 1;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
