<?php

namespace App\Filament\Resources\SpmbNilaiQurans\Pages;

use App\Filament\Resources\SpmbNilaiQurans\SpmbNilaiQuranResource;
use App\Models\SpmbNilaiQuran;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSpmbNilaiQuran extends EditRecord
{
    protected static string $resource = SpmbNilaiQuranResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        // Tombol simpan menempel di bawah layar — tidak perlu menggulir jauh.
        static::stickyFormActions();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Simpan nilai')
                ->icon('heroicon-o-check')
                ->extraAttributes(['style' => 'min-height:46px;padding-inline:22px']),

            Action::make('simpanLanjut')
                ->label('Simpan & siswa lain')
                ->icon('heroicon-o-forward')
                ->color('gray')
                ->extraAttributes(['style' => 'min-height:46px'])
                ->action(function (): void {
                    $this->save(shouldRedirect: false);

                    $skor = $this->getRecord()->fresh();
                    $angka = $skor?->skor_btq;

                    Notification::make()
                        ->title('Nilai tersimpan')
                        ->body($skor?->registration?->nomor.' — nilai BTQ: '.($angka ?? '—'))
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('index'));
                }),

            $this->getCancelFormAction()->label('Batal'),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        $record = $this->getRecord();
        $nama = $record?->registration?->nama_siswa;

        return 'Nilai BTQ '.($record?->skor_btq ?? '—').' tersimpan'.($nama ? ' · '.$nama : '');
    }
}
