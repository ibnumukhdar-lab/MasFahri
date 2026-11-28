<?php

namespace App\Filament\Resources\SpmbNilaiQurans\Pages;

use App\Filament\Resources\SpmbNilaiQurans\SpmbNilaiQuranResource;
use App\Models\Registration;
use App\Models\SpmbNilaiQuran;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Actions as SkemaAksi;
use Filament\Schemas\Components\Section;

class ListSpmbNilaiQurans extends ListRecords
{
    protected static string $resource = SpmbNilaiQuranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('inputNilai')
                ->label('Input nilai')
                ->icon('heroicon-o-magnifying-glass-plus')
                ->color('primary')
                ->modalHeading('Input nilai BTQ')
                ->modalDescription('Ketik nomor registrasi pendaftaran siswa, lalu tekan Oke — langsung masuk ke halaman penilaian siswa itu.')
                ->modalSubmitActionLabel('Oke, buka penilaian')
                ->modalWidth('md')
                ->schema([
                    TextInput::make('nomor')
                        ->label('Nomor registrasi pendaftaran')
                        ->placeholder('SPMB-2027-0001')
                        ->required()
                        ->autofocus()
                        ->autocomplete(false)
                        ->live(onBlur: true)
                        ->extraInputAttributes([
                            'style' => 'text-transform:uppercase;font-size:17px;font-weight:700;min-height:50px;letter-spacing:.04em',
                            'inputmode' => 'text',
                        ])
                        ->helperText(function (?string $state): string {
                            $cari = Registration::cariNomor($state);

                            if (! $cari) {
                                return filled($state)
                                    ? 'Nomor tidak ditemukan. Periksa lagi (contoh: SPMB-2027-0001).'
                                    : 'Contoh: SPMB-2027-0001 · bisa juga ketik 2027-0001.';
                            }

                            $sudah = SpmbNilaiQuran::where('registration_id', $cari->id)->exists();

                            return 'Siswa: '.$cari->nama_siswa.' · '.$cari->tahun_ajaran.($sudah ? ' · nilai sudah ada, akan dibuka untuk diubah' : ' · belum dinilai');
                        }),
                ])
                ->action(function (array $data, Action $action) {
                    $registrasi = Registration::cariNomor($data['nomor'] ?? null);

                    if (! $registrasi) {
                        Notification::make()
                            ->title('Nomor tidak ditemukan')
                            ->body('Pastikan nomor pendaftaran benar, contoh: SPMB-2027-0001.')
                            ->danger()
                            ->send();

                        return null;
                    }

                    // Buat baris nilai kosong bila belum ada, lalu langsung buka halaman penilaiannya.
                    $nilai = SpmbNilaiQuran::firstOrCreate(
                        ['registration_id' => $registrasi->id],
                        ['penilai' => auth()->user()?->name],
                    );

                    return redirect(SpmbNilaiQuranResource::getUrl('edit', ['record' => $nilai->id]));
                }),

            Action::make('daftarSiswa')
                ->label('Daftar siswa')
                ->icon('heroicon-o-users')
                ->color('gray')
                ->url(fn (): string => \App\Filament\Resources\Registrations\RegistrationResource::getUrl('index'))
                ->tooltip('Buka daftar pendaftar SPMB'),
        ];
    }
}
