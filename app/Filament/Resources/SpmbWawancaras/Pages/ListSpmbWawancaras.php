<?php

namespace App\Filament\Resources\SpmbWawancaras\Pages;

use App\Filament\Resources\SpmbWawancaras\SpmbWawancaraResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpmbWawancaras extends ListRecords
{
    protected static string $resource = SpmbWawancaraResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Input nilai wawancara')];
    }
}
