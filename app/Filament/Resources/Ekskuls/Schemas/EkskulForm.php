<?php
namespace App\Filament\Resources\Ekskuls\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EkskulForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ekstrakurikuler')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama ekstrakurikuler')
                            ->required()
                            ->maxLength(120)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Set $set): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),

                        TextInput::make('slug')
                            ->label('Slug (alamat halaman)')
                            ->required()
                            ->maxLength(120)
                            ->unique(ignoreRecord: true)
                            ->helperText('Dipakai pada alamat /ekskul/slug.'),

                        Textarea::make('keterangan')
                            ->label('Keterangan singkat')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Muncul di kartu halaman ekstrakurikuler.'),

                        TextInput::make('ikon')
                            ->label('Ikon (emoji, opsional)')
                            ->maxLength(8)
                            ->helperText('Contoh: 🏸 — muncul bila belum ada foto sampul.'),

                        TextInput::make('urut')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),

                        FileUpload::make('sampul')
                            ->label('Foto sampul (opsional)')
                            ->image()
                            ->disk('media')->visibility('public')
                            ->directory('unggahan')
                            ->maxSize(8192)
                            ->automaticallyResizeImagesToWidth(1920)
                            ->helperText('Bila dikosongkan, dipakai foto kegiatan yang pertama.')
                            ->columnSpanFull(),

                        Toggle::make('terbit')
                            ->label('Tampilkan di situs')
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }
}
