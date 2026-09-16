<?php

namespace App\Filament\Resources\GalleryAlbums\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class GalleryAlbumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Album')
                    ->columns(2)
                    ->schema([
                        TextInput::make('judul')
                            ->label('Judul album')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Set $set): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),

                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->default(now()),

                        TextInput::make('urut')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),

                        TextInput::make('sampul')
                            ->label('Sampul (URL / path)')
                            ->maxLength(500)
                            ->helperText('Contoh: /media/unggahan/sampul.jpg')
                            ->columnSpanFull(),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('terbit')
                            ->label('Tampilkan di situs')
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }
}
