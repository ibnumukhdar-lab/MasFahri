<?php

namespace App\Filament\Resources\AlumniCeritas\Pages;

use App\Filament\Resources\AlumniCeritas\AlumniCeritaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAlumniCerita extends CreateRecord
{
    protected static string $resource = AlumniCeritaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tampil'] = $data['tampil'] ?? true;
        $data['diverifikasi_pada'] = now();

        return $data;
    }
}
