<?php

namespace App\Filament\Resources\Beranda\Pages;

use App\Filament\Resources\Beranda\BerandaResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBeranda extends ListRecords
{
    protected static string $resource = BerandaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah bagian'),
            Action::make('lihat')
                ->label('Lihat beranda')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => url('/'))
                ->openUrlInNewTab(),
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Tahan & tarik barisnya untuk mengubah urutan tampil di beranda. Matikan "Tampil" untuk menyembunyikan bagian tanpa menghapusnya.';
    }
}
