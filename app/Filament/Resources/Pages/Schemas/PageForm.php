<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Isi Halaman')
                    ->schema([
                        TextInput::make('judul')
                            ->label('Judul')
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
                            ->unique(ignoreRecord: true)
                            ->helperText('Alamat halaman, mis. /visi'),

                        RichEditor::make('body')
                            ->label('Isi')
                            ->columnSpanFull(),
                    ]),

                Section::make('Pengaturan Halaman')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draf' => 'Draf',
                                'terbit' => 'Terbit',
                            ])
                            ->default('terbit')
                            ->required()
                            ->native(false),

                        TextInput::make('urut')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),

                        Toggle::make('tampil_di_menu')
                            ->label('Tampilkan di menu')
                            ->default(false)
                            ->inline(false),

                        TextInput::make('meta_judul')
                            ->label('Meta judul (SEO)')
                            ->maxLength(255),

                        Textarea::make('meta_deskripsi')
                            ->label('Meta deskripsi (SEO)')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
