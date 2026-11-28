<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Support\AkunSiakad;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('tarikSiakad')
                ->label('Tarik akun SIAKAD')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Tarik akun dari SIAKAD')
                ->modalDescription('Semua akun di siakad.smaitarafah.sch.id akan disalin ke daftar ini dengan peran bawaan Panitia SPMB. Akun yang sudah ada tidak diubah perannya.')
                ->modalSubmitActionLabel('Tarik sekarang')
                ->action(function (): void {
                    $hasil = AkunSiakad::tarikSemua();

                    if ($hasil['galat']) {
                        Notification::make()
                            ->title('Gagal menghubungi SIAKAD')
                            ->body($hasil['galat'])
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Akun SIAKAD ditarik')
                        ->body("Total {$hasil['total']} akun: {$hasil['baru']} baru, {$hasil['diperbarui']} diperbarui.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
