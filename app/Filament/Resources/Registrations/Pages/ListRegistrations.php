<?php

namespace App\Filament\Resources\Registrations\Pages;

use App\Filament\Resources\Registrations\RegistrationResource;
use App\Models\Registration;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListRegistrations extends ListRecords
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Pendaftaran dibuat dari formulir publik, bukan dari panel.
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(fn (): int => Registration::count()),

            'baru' => Tab::make('Baru')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'baru'))
                ->badge(fn (): int => Registration::where('status', 'baru')->count())
                ->badgeColor('warning'),

            'dihubungi' => Tab::make('Dihubungi')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'dihubungi')),

            'tes' => Tab::make('Tes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'tes')),

            'diterima' => Tab::make('Diterima')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'diterima')),

            'ditolak' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'ditolak')),
        ];
    }
}
