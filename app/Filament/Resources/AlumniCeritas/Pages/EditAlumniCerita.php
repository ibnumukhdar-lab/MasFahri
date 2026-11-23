<?php

namespace App\Filament\Resources\AlumniCeritas\Pages;

use App\Filament\Resources\AlumniCeritas\AlumniCeritaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAlumniCerita extends EditRecord
{
    protected static string $resource = AlumniCeritaResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // perubahan manual admin langsung tampil
        if (! $this->record->diverifikasi_pada) {
            $data['diverifikasi_pada'] = now();
        }
        $data['tampil'] = $data['tampil'] ?? true;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->after(function ($record): void {
                    if ($record->foto) {
                        \Illuminate\Support\Facades\Storage::disk('media')->delete($record->foto);
                    }
                }),
        ];
    }
}
