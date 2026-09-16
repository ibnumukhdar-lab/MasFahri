<?php

namespace App\Filament\Resources\TeacherLinks\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tautan guru')
                    ->columns(2)
                    ->schema([
                        TextInput::make('judul')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('kelompok')
                            ->label('Kelompok')
                            ->maxLength(100)
                            ->placeholder('mis. Administrasi, Pembelajaran'),

                        TextInput::make('url')
                            ->label('URL')
                            ->required()
                            ->maxLength(500)
                            ->url()
                            ->columnSpanFull(),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('urut')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),

                        Toggle::make('aktif')
                            ->label('Aktif')
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }
}
