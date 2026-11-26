<?php

namespace App\Filament\Resources\AlumniCeritas\Schemas;

use App\Models\AlumniCerita;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AlumniCeritaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data alumni')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama alumni')
                            ->required()
                            ->maxLength(120),

                        TextInput::make('wa')
                            ->label('Nomor WhatsApp')
                            ->maxLength(25),

                        TextInput::make('tahun_lulus')
                            ->label('Tahun lulus')
                            ->maxLength(8),

                        TextInput::make('kelas_akhir')
                            ->label('Kelas akhir')
                            ->maxLength(40),

                        Select::make('status_saat_ini')
                            ->label('Status sekarang')
                            ->options(AlumniCerita::STATUS)
                            ->native(false),

                        TextInput::make('status_keterangan')
                            ->label('Di mana / di apa')
                            ->maxLength(160)
                            ->placeholder('UIN Antasari — PAI'),

                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->maxLength(80)
                            ->placeholder('@nama'),

                        FileUpload::make('foto')
                            ->label('Foto alumni (pangkas jadi persegi)')
                            ->image()
                            ->directory('unggahan')
                            ->disk('media')
                            ->maxSize(4096)
                            ->imageEditor()
                            ->imageEditorMode(2)
                            ->imageEditorViewport(400, 400)
                            ->avatar()
                            ->columnSpanFull(),

                        Textarea::make('prestasi')
                            ->label('Prestasi selama di Arafah')
                            ->rows(3)
                            ->maxLength(600)
                            ->columnSpanFull(),

                        Textarea::make('pesan')
                            ->label('Pesan untuk adik kelas')
                            ->rows(5)
                            ->maxLength(1500)
                            ->columnSpanFull(),
                    ]),

                Section::make('Publikasi')
                    ->columns(2)
                    ->schema([
                        Toggle::make('tampil')
                            ->label('Tampilkan di halaman alumni')
                            ->inline(false),

                        TextInput::make('diverifikasi_pada')
                            ->label('Diverifikasi pada')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
