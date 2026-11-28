<?php

namespace App\Filament\Resources\SpmbGelombangs\RelationManagers;

use App\Models\SpmbGelombang;
use App\Models\SpmbJadwalTes;
use Filament\Actions\Action;
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
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

/**
 * Hari tes masuk milik satu gelombang.
 *
 * SATU HARI TES = seluruh tahapan dijalankan pada hari itu
 * (Tes Potensi Akademik → Baca Tulis Al-Qur'an → Wawancara).
 * Baris di tabel ini dikelompokkan per tanggal supaya terlihat sebagai satu hari.
 */
class JadwalTesRelationManager extends RelationManager
{
    protected static string $relationship = 'jadwalTes';

    protected static ?string $title = 'Hari tes & tahapannya';

    protected static ?string $modelLabel = 'Tahapan tes';

    protected static ?string $pluralModelLabel = 'Tahapan tes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jenis')
                    ->label('Tahapan')
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
                    ->label('Tanggal (hari tes)')
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->required()
                    ->helperText('Beberapa tahapan pada tanggal yang sama = satu hari tes.'),

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
                    ->label('Urutan dalam hari')
                    ->numeric()
                    ->default(1)
                    ->helperText('1 = tahap pertama pagi, 2 = tahap berikutnya, dst.'),

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
            ->groups([
                Group::make('tanggal')
                    ->label('Hari tes')
                    ->date()
                    ->collapsible(),
            ])
            ->defaultGroup('tanggal')
            ->columns([
                TextColumn::make('jam_label')
                    ->label('Jam')
                    ->weight('bold')
                    ->placeholder('—'),

                TextColumn::make('nama')
                    ->label('Tahapan')
                    ->wrap()
                    ->description(fn (SpmbJadwalTes $r): ?string => $r->tempat),

                TextColumn::make('jenis_label')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (SpmbJadwalTes $r): string => match ($r->jenis) {
                        'akademik' => 'primary',
                        'btq' => 'success',
                        'wawancara' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('paket_count')
                    ->label('Paket CBT')
                    ->counts('paket')
                    ->badge()
                    ->color(fn ($state): string => $state > 0 ? 'success' : 'gray'),

                TextColumn::make('urut')
                    ->label('Urut')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('aktif')
                    ->label('Tampil')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                Action::make('tambahHariLengkap')
                    ->label('Tambah hari tes lengkap')
                    ->icon('heroicon-o-calendar-days')
                    ->color('primary')
                    ->modalHeading('Tambah satu hari tes')
                    ->modalDescription('Sekali isi, tiga tahapan langsung dibuat: Tes Potensi Akademik, Baca Tulis Al-Qur\'an, dan Wawancara pada tanggal yang sama. Jamnya bisa disesuaikan setelah tersimpan.')
                    ->modalSubmitActionLabel('Buat hari tes')
                    ->schema([
                        DatePicker::make('tanggal')
                            ->label('Tanggal hari tes')
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->required(),

                        TextInput::make('tempat')
                            ->label('Tempat')
                            ->placeholder('Kampus SMA IT Arafah')
                            ->maxLength(120),

                        Section::make('Jam tiap tahapan')
                            ->columns(2)
                            ->schema([
                                TimePicker::make('tpa_mulai')->label('TPA — mulai')->seconds(false)->default('08:00'),
                                TimePicker::make('tpa_selesai')->label('TPA — selesai')->seconds(false)->default('10:00'),
                                TimePicker::make('btq_mulai')->label('BTQ — mulai')->seconds(false)->default('10:30'),
                                TimePicker::make('btq_selesai')->label('BTQ — selesai')->seconds(false)->default('11:30'),
                                TimePicker::make('waw_mulai')->label('Wawancara — mulai')->seconds(false)->default('13:00'),
                                TimePicker::make('waw_selesai')->label('Wawancara — selesai')->seconds(false)->default('15:00'),
                            ]),

                        Grid::make(1)->schema([
                            Toggle::make('aktif')
                                ->label('Langsung tampilkan di situs')
                                ->default(true),
                        ]),
                    ])
                    ->action(function (array $data): void {
                        $ownerRecord = $this->getOwnerRecord();
                        $tahapan = [
                            ['jenis' => 'akademik', 'nama' => 'Tes Potensi Akademik & Minat Bakat', 'mulai' => $data['tpa_mulai'] ?? null, 'selesai' => $data['tpa_selesai'] ?? null, 'urut' => 1,
                                'keterangan' => 'Dikerjakan daring (CBT) di perangkat masing-masing.'],
                            ['jenis' => 'btq', 'nama' => 'Baca Tulis Al-Qur\'an', 'mulai' => $data['btq_mulai'] ?? null, 'selesai' => $data['btq_selesai'] ?? null, 'urut' => 2,
                                'keterangan' => 'Praktik membaca, menulis, dan hafalan bersama penguji.'],
                            ['jenis' => 'wawancara', 'nama' => 'Wawancara minat & bakat', 'mulai' => $data['waw_mulai'] ?? null, 'selesai' => $data['waw_selesai'] ?? null, 'urut' => 3,
                                'keterangan' => 'Wawancara bersama pendidik.'],
                        ];

                        foreach ($tahapan as $t) {
                            $ownerRecord->jadwalTes()->create([
                                'jenis' => $t['jenis'],
                                'nama' => $t['nama'],
                                'tanggal' => $data['tanggal'],
                                'jam_mulai' => $t['mulai'],
                                'jam_selesai' => $t['selesai'],
                                'tempat' => $data['tempat'] ?? null,
                                'keterangan' => $t['keterangan'],
                                'urut' => $t['urut'],
                                'aktif' => (bool) ($data['aktif'] ?? true),
                            ]);
                        }

                        Notification::make()
                            ->title('Hari tes dibuat')
                            ->body('3 tahapan (TPA, BTQ, wawancara) tersimpan pada tanggal tersebut. Silakan sesuaikan jamnya bila perlu.')
                            ->success()
                            ->send();
                    }),

                CreateAction::make()
                    ->label('Tambah satu tahapan'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada hari tes')
            ->emptyStateDescription('Klik "Tambah hari tes lengkap" — sekali isi, TPA + BTQ + wawancara langsung dibuat untuk hari itu.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }
}
