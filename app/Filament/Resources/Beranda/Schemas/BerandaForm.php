<?php

namespace App\Filament\Resources\Beranda\Schemas;

use App\Models\BerandaBagian;
use App\Models\GalleryPhoto;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BerandaForm
{
    /** Jenis bagian yang memakai kolom tertentu (sisanya disembunyikan agar panel tidak membingungkan). */
    private const PAKAI_GAMBAR = ['hero', 'tentang'];

    private const PAKAI_TOMBOL = ['hero', 'tentang', 'spmb', 'cta'];

    private const PAKAI_TEKS = ['hero', 'tentang', 'visi_misi', 'spmb', 'cta'];

    private const PAKAI_SUBJUDUL = ['hero', 'keunggulan', 'program', 'kurikulum', 'galeri', 'spmb', 'statistik', 'cta'];

    private const PAKAI_DATA = ['keunggulan', 'visi_misi', 'program', 'kurikulum', 'statistik', 'faq', 'spmb'];

    private const BANTUAN_DATA = [
        'keunggulan' => 'Satu butir = satu centang. Isi kolom "Teks" saja.',
        'visi_misi' => 'Isi kolom "Teks" dengan VISI di atas, lalu satu butir untuk tiap MISI.',
        'program' => 'Satu butir = satu kartu. Isi Judul (nama program) dan Teks (penjelasan).',
        'kurikulum' => 'Satu butir = satu kartu kurikulum.',
        'statistik' => 'Judul = angka (mis. 300+), Teks = keterangannya (mis. Siswa aktif).',
        'faq' => 'Judul = pertanyaan, Teks = jawabannya.',
        'spmb' => 'Satu butir = satu poin penjelasan di samping formulir.',
    ];

    public static function configure(Schema $schema): Schema
    {
        $jenis = fn (Get $get): string => (string) $get('jenis');
        $pernah = fn (Get $get, array $daftar): bool => in_array($jenis($get), $daftar, true);

        return $schema
            ->components([
                Section::make('Bagian ini')
                    ->columns(2)
                    ->schema([
                        Select::make('jenis')
                            ->label('Jenis bagian')
                            ->options(BerandaBagian::jenisDaftar())
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set): void {
                                // Saat membuat bagian baru: isi contoh agar tidak kosong melompong.
                                if ($operation === 'create' && $state) {
                                    foreach (BerandaBagian::bawaan((string) $state) as $kunci => $nilai) {
                                        $set($kunci, $nilai);
                                    }
                                }
                            })
                            ->helperText('Jenis menentukan bentuk tampilannya. Isinya tetap bisa diubah kapan saja.'),

                        Toggle::make('aktif')
                            ->label('Tampilkan di beranda')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Matikan untuk menyembunyikan tanpa menghapus.'),
                    ]),

                Section::make('Teks')
                    ->schema([
                        TextInput::make('judul')
                            ->label('Judul bagian')
                            ->maxLength(180)
                            ->helperText('Untuk statistik: isi dengan angkanya, mis. 300+'),

                        TextInput::make('subjudul')
                            ->label('Sub-judul / kalimat pembuka')
                            ->maxLength(255)
                            ->visible(fn (Get $get) => $pernah($get, self::PAKAI_SUBJUDUL)),

                        Textarea::make('teks')
                            ->label('Teks')
                            ->rows(4)
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $pernah($get, self::PAKAI_TEKS)),
                    ]),

                Section::make('Gambar')
                    ->schema([
                        FileUpload::make('gambar')
                            ->label('Unggah gambar (dari komputer/HP)')
                            ->image()
                            ->disk('media')
                            ->directory('unggahan')
                            ->visibility('public')
                            ->maxSize(8192)
                            ->automaticallyResizeImagesToWidth(1920)
                            ->helperText('Gambar yang diunggah otomatis masuk galeri.'),

                        Select::make('pilih_galeri')
                            ->label('Atau pilih gambar dari galeri')
                            ->options(fn () => GalleryPhoto::query()->latest('id')->limit(300)->get()
                                ->mapWithKeys(fn ($f) => [$f->berkas => ($f->judul ?: basename($f->berkas))]))
                            ->searchable()
                            ->native(false)
                            ->placeholder('Belum memilih dari galeri')
                            ->helperText('Kalau diisi, gambar ini yang dipakai (mengalahkan berkas yang diunggah).')
                            ->hintAction(
                                Action::make('buka_galeri')
                                    ->label('Lihat galeri')
                                    ->icon('heroicon-o-photo')
                                    ->modalHeading('Pilih gambar dari galeri')
                                    ->modalDescription('Klik salah satu foto untuk memakainya di bagian beranda ini.')
                                    ->modalSubmitAction(false)
                                    ->modalCancelActionLabel('Tutup')
                                    ->modalWidth('4xl')
                                    ->modalContent(fn () => view('filament.pilih-galeri', [
                                        'foto' => GalleryPhoto::query()->latest('id')->limit(120)->get(),
                                        'target' => 'data.gambar',
                                    ]))
                            ),
                    ])
                    ->visible(fn (Get $get) => $pernah($get, self::PAKAI_GAMBAR)),

                Section::make('Tombol')
                    ->columns(2)
                    ->schema([
                        TextInput::make('tombol_teks')->label('Tulisan tombol')->maxLength(60),
                        TextInput::make('tombol_tautan')
                            ->label('Tautan tombol')
                            ->maxLength(255)
                            ->helperText('Boleh alamat lengkap (https://…) atau jalur di situs ini (/spmb).'),
                    ])
                    ->visible(fn (Get $get) => $pernah($get, self::PAKAI_TOMBOL)),

                Section::make('Daftar isi bagian')
                    ->schema([
                        Repeater::make('data')
                            ->label('Butir')
                            ->addActionLabel('Tambah butir')
                            ->schema([
                                TextInput::make('judul')
                                    ->label('Judul kecil (nama / angka)')
                                    ->maxLength(140),
                                Textarea::make('teks')
                                    ->label('Teks')
                                    ->rows(2),
                            ])
                            ->itemLabel(fn (array $state): ?string => filled($state['judul'] ?? null)
                                ? Str::limit((string) $state['judul'], 48)
                                : Str::limit(trim(strip_tags((string) ($state['teks'] ?? ''))), 48))
                            ->collapsible()
                            ->collapsed()
                            ->reorderable()
                            ->cloneable()
                            ->columnSpanFull()
                            ->helperText(fn (Get $get) => self::BANTUAN_DATA[$jenis($get)] ?? 'Butir-butir isi bagian ini.'),
                    ])
                    ->visible(fn (Get $get) => $pernah($get, self::PAKAI_DATA)),
            ]);
    }
}
