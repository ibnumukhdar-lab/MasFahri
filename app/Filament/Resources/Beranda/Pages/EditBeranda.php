<?php

namespace App\Filament\Resources\Beranda\Pages;

use App\Filament\Resources\Beranda\BerandaResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBeranda extends EditRecord
{
    protected static string $resource = BerandaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lihat')
                ->label('Lihat beranda')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => url('/'))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['pilih_galeri'])) {
            $data['gambar'] = $data['pilih_galeri'];
        }
        unset($data['pilih_galeri']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
