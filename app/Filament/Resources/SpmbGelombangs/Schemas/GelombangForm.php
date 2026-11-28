<?php

namespace App\Filament\Resources\SpmbGelombangs\Schemas;

use App\Http\Controllers\SpmbController;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GelombangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Gelombang')
                    ->columns(2)
                    ->schema([
                        Select::make('tahun_ajaran')
                            ->label('Tahun ajaran')
                            ->options(array_combine(SpmbController::TAHUN_AJARAN, SpmbController::TAHUN_AJARAN))
                            ->default(SpmbController::TAHUN_AJARAN[0])
                            ->required()
                            ->native(false),

                        TextInput::make('nama')
                            ->label('Nama gelombang')
                            ->placeholder('Gelombang 1')
                            ->required()
                            ->maxLength(60),

                        DatePicker::make('mulai')
                            ->label('Pendaftaran dibuka')
                            ->native(false)
                            ->displayFormat('d F Y'),

                        DatePicker::make('selesai')
                            ->label('Pendaftaran ditutup')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->afterOrEqual('mulai'),

                        TextInput::make('urut')
                            ->label('Urutan tampil')
                            ->numeric()
                            ->default(1),

                        Toggle::make('aktif')
                            ->label('Tampilkan di situs')
                            ->helperText('Gelombang yang aktif saja yang muncul di halaman /spmb.')
                            ->default(true)
                            ->inline(false),

                        Textarea::make('keterangan')
                            ->label('Catatan untuk pendaftar')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Tampil kecil di bawah jadwal gelombang ini.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
