<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Isi Postingan')
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
                            ->helperText('Terisi otomatis dari judul. Boleh diubah manual.'),

                        Select::make('categories')
                            ->label('Kategori')
                            ->relationship('categories', 'nama')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),

                        Textarea::make('ringkasan')
                            ->label('Ringkasan')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Dipakai di kartu berita & deskripsi SEO. Boleh dikosongkan.')
                            ->columnSpanFull(),

                        RichEditor::make('body')
                            ->label('Isi')
                            ->columnSpanFull(),
                    ]),

                Section::make('Penerbitan')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draf' => 'Draf',
                                'terbit' => 'Terbit',
                            ])
                            ->default('draf')
                            ->required()
                            ->native(false),

                        DateTimePicker::make('terbit_pada')
                            ->label('Terbit pada')
                            ->seconds(false)
                            ->default(now()),

                        TextInput::make('gambar_sampul')
                            ->label('Gambar sampul (URL / path)')
                            ->maxLength(500)
                            ->helperText('Tempel URL gambar, mis. /media/unggahan/foto.jpg'),
                    ]),

                Grid::make(1)->schema([
                    TextInput::make('penulis_id')
                        ->label('ID penulis')
                        ->numeric()
                        ->default(fn () => auth()->id()),
                ])->visibleOn('create'),
            ]);
    }
}
