<?php

namespace App\Filament\Resources\SpmbGelombangs\Pages;

use App\Filament\Resources\SpmbGelombangs\SpmbGelombangResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpmbGelombangs extends ListRecords
{
    protected static string $resource = SpmbGelombangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah gelombang'),
        ];
    }
}
