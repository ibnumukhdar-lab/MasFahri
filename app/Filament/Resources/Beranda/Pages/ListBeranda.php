<?php

namespace App\Filament\Resources\Beranda\Pages;

use App\Filament\Resources\Beranda\BerandaResource;
use App\Support\SelaraskanBeranda;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListBeranda extends ListRecords
{
    protected static string $resource = BerandaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah bagian'),

            // Halaman = sumber kebenaran; tombol ini menyalin ulang isinya ke beranda.
            Action::make('selaraskan')
                ->label('Selaraskan dari halaman')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading('Selaraskan isi beranda dengan halaman?')
                ->modalDescription('Bagian Visi & Misi, Tentang, dan Kurikulum akan disalin ulang dari halaman yang sejenis (mis. /visi, /tentang-sma-it-arafah, /kurikulum). Suntingan manual pada KETIGA bagian itu akan tertimpa.')
                ->modalSubmitActionLabel('Ya, selaraskan')
                ->action(function (): void {
                    $lapor = SelaraskanBeranda::jalan();

                    $rincian = [];
                    foreach ($lapor as $jenis => $catatan) {
                        $rincian[] = $jenis.': '.$catatan;
                    }

                    Notification::make()
                        ->title($lapor ? 'Beranda diselaraskan' : 'Tidak ada halaman yang cocok')
                        ->body($rincian ? implode(' · ', $rincian) : 'Pastikan halaman visi, tentang, dan kurikulum sudah terbit.')
                        ->success()
                        ->send();
                }),

            Action::make('lihat')
                ->label('Lihat beranda')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => url('/'))
                ->openUrlInNewTab(),
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Tahan & tarik barisnya untuk mengubah urutan tampil di beranda. Matikan "Tampil" untuk menyembunyikan bagian tanpa menghapusnya. Isi bagian Visi & Misi, Tentang, dan Kurikulum bisa disamakan dengan halamannya lewat tombol "Selaraskan dari halaman".';
    }
}
