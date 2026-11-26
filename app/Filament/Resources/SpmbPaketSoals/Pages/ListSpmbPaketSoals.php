<?php

namespace App\Filament\Resources\SpmbPaketSoals\Pages;

use App\Filament\Resources\SpmbPaketSoals\SpmbPaketSoalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpmbPaketSoals extends ListRecords
{
    protected static string $resource = SpmbPaketSoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Buat paket soal'),
        ];
    }
}
