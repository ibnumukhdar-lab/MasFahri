<?php

namespace App\Filament\Resources\Graduates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GraduateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data kelulusan')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nisn')
                            ->label('NISN')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),

                        TextInput::make('nama')
                            ->label('Nama siswa')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('kelas')
                            ->label('Kelas')
                            ->maxLength(50),

                        TextInput::make('tahun_ajaran')
                            ->label('Tahun ajaran')
                            ->maxLength(20)
                            ->placeholder('2025/2026'),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'lulus' => 'Lulus',
                                'tidak_lulus' => 'Tidak lulus',
                            ])
                            ->default('lulus')
                            ->required()
                            ->native(false),

                        Toggle::make('tampil')
                            ->label('Tampilkan di halaman kelulusan')
                            ->default(true)
                            ->inline(false),

                        Textarea::make('pesan')
                            ->label('Pesan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
