<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\User;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Select::make('peran')
                            ->label('Peran di panel')
                            ->options(function (?User $record): array {
                                $opsi = User::PERAN_PILIHAN;

                                // Peran lama (guru, musyrif, orang tua, …) tetap ditampilkan
                                // agar akun lama tidak "kehilangan" perannya saat disunting.
                                if ($record && filled($record->peran) && ! isset($opsi[$record->peran])) {
                                    $opsi = [$record->peran => $record->peran_label.' (tidak bisa masuk panel)'] + $opsi;
                                }

                                return $opsi;
                            })
                            ->default('panitia_spmb')
                            ->required()
                            ->native(false)
                            ->helperText('Panitia SPMB hanya melihat menu SPMB. Operator Website melihat seluruh menu konten. Super Admin juga bisa menetapkan peran.'),

                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->maxLength(25)
                            ->placeholder('08xxxxxxxxxx'),

                        TextInput::make('nipa')
                            ->label('NIPA')
                            ->maxLength(30)
                            ->placeholder('YMA. 0000 000')
                            ->helperText('Nomor Induk Pegawai Arafah (opsional).'),

                        TextInput::make('password')
                            ->label('Kata sandi baru')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText('Kosongkan bila tidak ingin mengubah kata sandi.')
                            ->columnSpanFull(),

                        Toggle::make('aktif')
                            ->label('Akun aktif')
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }
}
