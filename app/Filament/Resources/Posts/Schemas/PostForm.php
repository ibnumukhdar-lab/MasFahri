<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\GalleryPhoto;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('media')
                            ->fileAttachmentsDirectory('unggahan')
                            ->fileAttachmentsVisibility('public')
                            ->helperText('Tombol gambar di toolbar untuk menyisipkan foto dari komputer/HP — otomatis masuk galeri.'),

                        Select::make('sisipkan_galeri')
                            ->label('Sisipkan gambar dari galeri ke isi')
                            ->options(fn () => \App\Models\GalleryPhoto::query()->latest('id')->limit(300)->get()
                                ->mapWithKeys(fn ($f) => [$f->berkas => ($f->judul ?: basename($f->berkas))]))
                            ->searchable()
                            ->native(false)
                            ->placeholder('Pilih gambar (opsional)')
                            ->helperText('Pilih gambar, lalu tekan tombol "Sisipkan ke isi" di kanan.')
                            ->hintActions([
                                Action::make('buka_galeri_isi')
                                    ->label('Lihat galeri')
                                    ->icon('heroicon-o-photo')
                                    ->modalHeading('Pilih gambar dari galeri')
                                    ->modalSubmitAction(false)
                                    ->modalCancelActionLabel('Tutup')
                                    ->modalWidth('4xl')
                                    ->modalContent(fn () => view('filament.pilih-galeri', [
                                        'foto' => \App\Models\GalleryPhoto::query()->latest('id')->limit(120)->get(),
                                        'target' => 'data.sisipkan_galeri',
                                    ])),
                                Action::make('sisipkan_ke_isi')
                                    ->label('Sisipkan ke isi')
                                    ->icon('heroicon-o-arrow-down-on-square')
                                    ->action(function (\Filament\Schemas\Components\Utilities\Get $get, \Filament\Schemas\Components\Utilities\Set $set) {
                                        $berkas = $get('sisipkan_galeri');
                                        if (! $berkas) return;
                                        $gambar = '<p><img src="' . asset('media/' . $berkas) . '" alt=""></p>';
                                        $set('body', rtrim((string) $get('body')) . $gambar);
                                    }),
                            ]),

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

                        FileUpload::make('gambar_sampul')
                            ->label('Gambar sampul')
                            ->image()
                            ->disk('media')
                            ->directory('unggahan')
                            ->visibility('public')
                            ->maxSize(8192)
                            ->automaticallyResizeImagesToWidth(1600)
                            ->helperText('Unggah dari komputer/HP. Gambar yang diunggah otomatis masuk galeri.'),

                        Select::make('pilih_galeri')
                            ->label('Atau pilih gambar dari galeri')
                            ->options(fn () => GalleryPhoto::query()->latest('id')->limit(300)->get()
                                ->mapWithKeys(fn ($f) => [$f->berkas => ($f->judul ?: basename($f->berkas))]))
                            ->searchable()
                            ->native(false)
                            ->placeholder('Belum memilih dari galeri')
                            ->helperText('Kalau diisi, gambar ini dipakai sebagai sampul (mengalahkan berkas yang diunggah).')
                            ->hintAction(
                                Action::make('buka_galeri')
                                    ->label('Lihat galeri')
                                    ->icon('heroicon-o-photo')
                                    ->modalHeading('Pilih gambar dari galeri')
                                    ->modalDescription('Klik salah satu foto untuk memakainya sebagai gambar sampul.')
                                    ->modalSubmitAction(false)
                                    ->modalCancelActionLabel('Tutup')
                                    ->modalWidth('4xl')
                                    ->modalContent(fn () => view('filament.pilih-galeri', [
                                        'foto' => GalleryPhoto::query()->latest('id')->limit(120)->get(),
                                    ]))
                            ),
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
