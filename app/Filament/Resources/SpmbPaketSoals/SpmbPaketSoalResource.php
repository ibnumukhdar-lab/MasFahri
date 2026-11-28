<?php

namespace App\Filament\Resources\SpmbPaketSoals;

use App\Filament\Resources\SpmbPaketSoals\Pages;
use App\Models\SpmbBankSoal;
use App\Models\SpmbPaketSoal;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class SpmbPaketSoalResource extends Resource
{
    protected static ?string $model = SpmbPaketSoal::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $slug = 'paket-soal-cbt';

    protected static ?string $navigationLabel = 'Paket Soal CBT';

    protected static ?string $modelLabel = 'Paket soal';

    protected static ?string $pluralModelLabel = 'Paket soal CBT';

    protected static string | UnitEnum | null $navigationGroup = 'Penerimaan SPMB';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('judul')
                    ->label('Judul paket')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('mis. Tes Potensi Akademik & Minat Bakat'),

                Textarea::make('deskripsi')
                    ->label('Deskripsi / petunjuk singkat')
                    ->rows(2)
                    ->placeholder('Muncul di halaman masuk tes siswa.')
                    ->columnSpanFull(),

                TextInput::make('mata_pelajaran')
                    ->label('Mata pelajaran / jenis tes')
                    ->maxLength(80)
                    ->placeholder('mis. Matematika'),

                TextInput::make('urutan')
                    ->label('Urutan tampil')
                    ->numeric()->default(0)
                    ->helperText('Paket dengan angka kecil tampil lebih dulu di daftar siswa.'),

                Select::make('jadwal_tes_id')
                    ->label('Tautkan ke jadwal gelombang (opsional)')
                    ->options(fn (): array => \App\Models\SpmbJadwalTes::query()
                        ->with('gelombang')
                        ->orderBy('tanggal')
                        ->get()
                        ->mapWithKeys(fn ($j) => [
                            $j->id => ($j->gelombang?->nama ?? '—').' · '.$j->waktu_label.' · '.$j->nama,
                        ])
                        ->all())
                    ->searchable()
                    ->native(false)
                    ->helperText('Bila ditautkan, tanggal & jam paket ini MENGIKUTI jadwal gelombang tersebut — tidak perlu diisi manual lagi.')
                    ->columnSpanFull(),

                DatePicker::make('tanggal_tes')
                    ->label('Tanggal tes')
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->helperText('Diabaikan bila paket ditautkan ke jadwal gelombang di atas.'),

                TextInput::make('kkm')
                    ->label('KKM (opsional)')
                    ->numeric()->minValue(0)->maxValue(100)
                    ->helperText('Ambang lulus paket ini. Kosongkan bila belum ditetapkan.'),

                Toggle::make('wajib')
                    ->label('Wajib diselesaikan')
                    ->default(true)
                    ->helperText('Paket wajib ikut dihitung dalam syarat "semua paket tuntas" dan nilai akhir.'),

                TextInput::make('durasi_menit')
                    ->label('Durasi (menit)')
                    ->numeric()->minValue(1)->maxValue(300)
                    ->required()
                    ->default(30),

                Toggle::make('aktif')
                    ->label('Paket aktif (dibuka untuk siswa)')
                    ->default(true)
                    ->helperText('Hanya paket aktif + dalam jadwal yang bisa dikerjakan siswa.'),

                DateTimePicker::make('mulai_pada')
                    ->label('Buka mulai')
                    ->seconds(false)
                    ->helperText('Kosongkan = bisa dikerjakan kapan saja.'),

                DateTimePicker::make('selesai_pada')
                    ->label('Tutup pada')
                    ->seconds(false)
                    ->helperText('Kosongkan = tidak ada batas tutup.'),

                Repeater::make('soal')
                    ->label('Butir soal (pilihan ganda A–E)')
                    ->relationship()
                    ->schema([
                        Textarea::make('teks')
                            ->label('Teks soal')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),

                        TextInput::make('opsi_a')->label('Pilihan A')->required()->maxLength(255),
                        TextInput::make('opsi_b')->label('Pilihan B')->required()->maxLength(255),
                        TextInput::make('opsi_c')->label('Pilihan C')->maxLength(255),
                        TextInput::make('opsi_d')->label('Pilihan D')->maxLength(255),
                        TextInput::make('opsi_e')->label('Pilihan E')->maxLength(255)
                            ->helperText('Opsional — kosongkan bila 4 pilihan.'),

                        Select::make('kunci')
                            ->label('Kunci jawaban')
                            ->options(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E'])
                            ->required()
                            ->default('A'),
                    ])
                    ->columns(4)
                    ->itemLabel(fn (array $state): ?string => str($state['teks'] ?? '')->limit(70)->toString())
                    ->collapsible()
                    ->cloneable()
                    ->addActionLabel('Tambah soal')
                    ->defaultItems(1)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('soal'))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('judul')
                    ->label('Judul paket')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('mata_pelajaran')
                    ->label('Mata pelajaran')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('urutan')
                    ->label('Urutan')
                    ->sortable(),

                TextColumn::make('soal_count')
                    ->label('Soal')
                    ->suffix(' butir'),

                TextColumn::make('tanggal_tes')
                    ->label('Tanggal tes')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('kkm')
                    ->label('KKM')
                    ->placeholder('-'),

                TextColumn::make('wajib')
                    ->label('Wajib')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Ya' : 'Tidak')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

                TextColumn::make('durasi_menit')
                    ->label('Durasi')
                    ->suffix(' menit'),

                TextColumn::make('jadwal')
                    ->label('Jadwal'),

                TextColumn::make('aktif')
                    ->label('Aktif')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Nonaktif')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()->label('Ubah'),

                // Ambil butir soal dari bank soal (disalin ke paket ini, tetap bisa diedit).
                Action::make('ambil_bank')
                    ->label('Ambil dari Bank')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->color('gray')
                    ->modalHeading('Ambil soal dari Bank Soal')
                    ->modalDescription(fn (\App\Models\SpmbPaketSoal $record) => 'Soal yang dipilih akan disalin ke paket "'.$record->judul.'".')
                    ->modalSubmitActionLabel('Tambahkan ke paket')
                    ->schema([
                        CheckboxList::make('soal_ids')
                            ->label('Pilih soal')
                            ->options(fn () => SpmbBankSoal::query()
                                ->where('aktif', true)
                                ->orderBy('mata_pelajaran')->orderBy('id')
                                ->get()
                                ->mapWithKeys(fn ($s) => [
                                    $s->id => '['.($s->mata_pelajaran ?: 'Umum').' · '.($s->tingkat ?: 'sedang').'] '
                                        .Str::limit($s->teks, 90),
                                ]))
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(1)
                            ->required(),
                    ])
                    ->action(function (array $data, \App\Models\SpmbPaketSoal $record) {
                        $urut = (int) $record->soal()->max('urut');
                        $jumlah = 0;

                        foreach (SpmbBankSoal::whereIn('id', $data['soal_ids'] ?? [])->get() as $s) {
                            $record->soal()->create([
                                'urut' => ++$urut,
                                'teks' => $s->teks,
                                'opsi_a' => $s->opsi_a,
                                'opsi_b' => $s->opsi_b,
                                'opsi_c' => $s->opsi_c,
                                'opsi_d' => $s->opsi_d,
                                'opsi_e' => $s->opsi_e,
                                'kunci' => $s->kunci,
                            ]);
                            $jumlah++;
                        }

                        Notification::make()
                            ->title($jumlah.' soal ditambahkan ke paket')
                            ->success()
                            ->send();
                    }),

                // Duplikat paket beserta seluruh butir soalnya.
                Action::make('duplikat')
                    ->label('Duplikat')
                    ->icon(Heroicon::OutlinedDocumentDuplicate)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Duplikat paket')
                    ->modalDescription('Seluruh butir soal ikut disalin. Paket baru dibuat nonaktif, judul diberi "(salinan)", dan tanggal tes dikosongkan.')
                    ->action(function (\App\Models\SpmbPaketSoal $record) {
                        // Jangan pakai replicate(): atribut hasil withCount (soal_count) ikut tersalin
                        // dan bukan kolom tabel → insert gagal.
                        $baru = new \App\Models\SpmbPaketSoal();
                        $baru->fill($record->only($baru->getFillable()));
                        $baru->judul = $record->judul.' (salinan)';
                        $baru->aktif = false;
                        $baru->tanggal_tes = null;
                        $baru->save();

                        foreach ($record->soal()->orderBy('urut')->get() as $s) {
                            $baru->soal()->create($s->only([
                                'urut', 'teks', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e', 'kunci',
                            ]));
                        }

                        Notification::make()
                            ->title('Paket diduplikat')
                            ->body($baru->judul.' — '.$baru->soal()->count().' soal')
                            ->success()
                            ->send();
                    }),
            ])
            ->headerActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpmbPaketSoals::route('/'),
            'create' => Pages\CreateSpmbPaketSoal::route('/create'),
            'edit' => Pages\EditSpmbPaketSoal::route('/{record}/edit'),
        ];
    }
}
