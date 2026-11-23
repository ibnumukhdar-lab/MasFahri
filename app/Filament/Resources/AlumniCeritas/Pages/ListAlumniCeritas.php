<?php

namespace App\Filament\Resources\AlumniCeritas\Pages;

use App\Filament\Resources\AlumniCeritas\AlumniCeritaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAlumniCeritas extends ListRecords
{
    protected static string $resource = AlumniCeritaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah manual'),
        ];
    }
}
