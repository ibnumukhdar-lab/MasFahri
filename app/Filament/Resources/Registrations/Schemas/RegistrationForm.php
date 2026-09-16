<?php

namespace App\Filament\Resources\Registrations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Form pendaftaran SPMB tidak dibuka di panel (pendaftar mengisi lewat situs publik).
 * Form ini hanya dipakai oleh aksi "Ubah status / pesan" pada tabel & halaman detail.
 */
class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tindak lanjut')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(\App\Models\Registration::STATUS)
                            ->required()
                            ->native(false),

                        Textarea::make('pesan_admin')
                            ->label('Pesan untuk pendaftar')
                            ->rows(4)
                            ->maxLength(1000)
                            ->helperText('Tampil di halaman cek status SPMB.'),

                        TextInput::make('nomor')
                            ->label('Nomor pendaftaran')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }

    /** Skema khusus aksi ubah status. */
    public static function statusSchema(): array
    {
        return [
            Select::make('status')
                ->label('Status')
                ->options(\App\Models\Registration::STATUS)
                ->required()
                ->native(false),

            Textarea::make('pesan_admin')
                ->label('Pesan untuk pendaftar')
                ->rows(4)
                ->maxLength(1000),
        ];
    }
}
