<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Isi Halaman')
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
                            ->helperText('Alamat halaman, mis. /visi'),

                        RichEditor::make('body')
                            ->label('Isi')
                            ->fileAttachmentsDisk('media')
                            ->fileAttachmentsDirectory('unggahan')
                            ->fileAttachmentsVisibility('public')
                            ->helperText('Tombol gambar di toolbar untuk menyisipkan foto dari komputer/HP — otomatis masuk galeri.')
                            ->columnSpanFull(),

                        Select::make('sisipkan_galeri')
                            ->label('Sisipkan gambar dari galeri ke isi')
                            ->options(fn () => \App\Models\GalleryPhoto::query()->latest('id')->limit(300)->get()
                                ->mapWithKeys(fn ($f) => [$f->berkas => ($f->judul ?: basename($f->berkas))]))
                            ->searchable()
                            ->native(false)
                            ->placeholder('Pilih gambar (opsional)')
                            ->helperText('Pilih gambar, lalu tekan tombol "Sisipkan ke isi" di kanan.')
                            ->columnSpanFull()
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
                                        $set('body', rtrim((string) $get('body')) . '<p><img src="' . asset('media/' . $berkas) . '" alt=""></p>');
                                    }),
                            ]),

                    ]),

                Section::make('Pengaturan Halaman')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draf' => 'Draf',
                                'terbit' => 'Terbit',
                            ])
                            ->default('terbit')
                            ->required()
                            ->native(false),

                        TextInput::make('urut')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),

                        Toggle::make('tampil_di_menu')
                            ->label('Tampilkan di menu')
                            ->default(false)
                            ->inline(false),

                        TextInput::make('meta_judul')
                            ->label('Meta judul (SEO)')
                            ->maxLength(255),

                        Textarea::make('meta_deskripsi')
                            ->label('Meta deskripsi (SEO)')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
