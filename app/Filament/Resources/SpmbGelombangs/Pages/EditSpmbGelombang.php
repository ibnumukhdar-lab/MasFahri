<?php

namespace App\Filament\Resources\SpmbGelombangs\Pages;

use App\Filament\Resources\SpmbGelombangs\SpmbGelombangResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSpmbGelombang extends EditRecord
{
    protected static string $resource = SpmbGelombangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalDescription('Jadwal tes pada gelombang ini ikut terhapus. Pendaftar yang sudah tercatat tetap ada.'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Gelombang diperbarui';
    }
}
