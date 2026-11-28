<?php

namespace App\Filament\Resources\SpmbGelombangs\Pages;

use App\Filament\Resources\SpmbGelombangs\SpmbGelombangResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpmbGelombang extends CreateRecord
{
    protected static string $resource = SpmbGelombangResource::class;

    protected function getRedirectUrl(): string
    {
        // Langsung ke halaman ubah supaya bisa menambah jadwal tes.
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Gelombang dibuat — sekarang tambahkan jadwal tesnya';
    }
}
