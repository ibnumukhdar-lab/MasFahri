<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                            ->label('Peran')
                            ->options([
                                'admin' => 'Administrator',
                                'guru' => 'Guru',
                                'musyrif' => 'Musyrif/Musyrifah',
                                'ustadz_diniyah' => 'Ustadz Diniyah',
                                'orang_tua' => 'Orang Tua',
                                'alumni' => 'Alumni',
                            ])
                            ->default('guru')
                            ->required()
                            ->native(false)
                            ->helperText('Hanya peran Administrator yang bisa masuk panel ini.'),

                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->maxLength(25)
                            ->placeholder('08xxxxxxxxxx'),

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
