<?php

namespace App\Filament\Resources\SpmbGelombangs\RelationManagers;

use App\Models\SpmbJadwalTes;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Jadwal tes masuk milik satu gelombang — boleh lebih dari satu
 * (mis. TPA pagi, BTQ siang, wawancara besok).
 */
class JadwalTesRelationManager extends RelationManager
{
    protected static string $relationship = 'jadwalTes';

    protected static ?string $title = 'Jadwal tes masuk';

    protected static ?string $modelLabel = 'Jadwal tes';

    protected static ?string $pluralModelLabel = 'Jadwal tes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jenis')
                    ->label('Jenis')
                    ->options(SpmbJadwalTes::JENIS)
                    ->default('akademik')
                    ->required()
                    ->native(false),

                TextInput::make('nama')
                    ->label('Nama kegiatan')
                    ->placeholder('Tes Potensi Akademik & Minat Bakat')
                    ->required()
                    ->maxLength(120),

                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->required(),

                TimePicker::make('jam_mulai')
                    ->label('Jam mulai')
                    ->seconds(false)
                    ->displayFormat('H:i'),

                TimePicker::make('jam_selesai')
                    ->label('Jam selesai')
                    ->seconds(false)
                    ->displayFormat('H:i')
                    ->after('jam_mulai'),

                TextInput::make('tempat')
                    ->label('Tempat / media')
                    ->placeholder('Kampus SMA IT Arafah / CBT daring')
                    ->maxLength(120),

                TextInput::make('urut')
                    ->label('Urutan')
                    ->numeric()
                    ->default(1),

                Toggle::make('aktif')
                    ->label('Tampilkan di situs')
                    ->default(true)
                    ->inline(false),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(2)
                    ->maxLength(400)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->defaultSort('tanggal')
            ->columns([
                TextColumn::make('waktu_label')
                    ->label('Waktu')
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('nama')
                    ->label('Kegiatan')
                    ->wrap()
                    ->description(fn (SpmbJadwalTes $r): ?string => $r->tempat),

                TextColumn::make('jenis_label')
                    ->label('Jenis')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('paket_count')
                    ->label('Paket CBT')
                    ->counts('paket')
                    ->badge()
                    ->color(fn ($state): string => $state > 0 ? 'success' : 'gray'),

                IconColumn::make('aktif')
                    ->label('Tampil')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah jadwal tes'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada jadwal tes')
            ->emptyStateDescription('Tambahkan tanggal tes masuk untuk gelombang ini — boleh lebih dari satu.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }
}
