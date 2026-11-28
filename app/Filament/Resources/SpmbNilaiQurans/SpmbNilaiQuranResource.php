<?php

namespace App\Filament\Resources\SpmbNilaiQurans;

use App\Filament\Resources\SpmbNilaiQurans\Pages;
use App\Models\SpmbNilaiQuran;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use UnitEnum;

/**
 * Nilai BTQ (baca tulis Al-Qur'an + praktek sholat) untuk panitia/guru penguji.
 *
 * Dirancang untuk HP: tabel ringkas 4 kolom, tombol "Input nilai" memakai nomor
 * pendaftaran (SPMB-2027-0001) lalu langsung membuka halaman penilaian siswa itu.
 */
class SpmbNilaiQuranResource extends Resource
{
    protected static ?string $model = SpmbNilaiQuran::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $slug = 'nilai-quran';

    protected static ?string $navigationLabel = 'Nilai BTQ';

    protected static ?string $modelLabel = 'Nilai baca tulis';

    protected static ?string $pluralModelLabel = "Nilai baca tulis Al-Qur'an";

    protected static string | UnitEnum | null $navigationGroup = 'Penerimaan SPMB';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(array_merge(
            [self::gaya()],
            self::komponenForm($schema),
        ));
    }

    /** Blok CSS halaman ini (input besar, kartu identitas). */
    private static function gaya(): Text
    {
        return Text::make('gaya_nilai_btq')
            ->content(new HtmlString(<<<'HTML'
                <style>
                .nbq-ident{border-radius:16px;padding:16px 18px;margin-bottom:2px;color:#fff;
                  background:linear-gradient(135deg,#1b3455 0%,#1f3a5f 55%,#2b5288 100%);
                  box-shadow:0 16px 30px -24px rgba(31,58,95,.75)}
                .nbq-ident .nbq-k{font-size:11px;letter-spacing:.08em;text-transform:uppercase;opacity:.72;font-weight:700}
                .nbq-ident .nbq-t{font-size:19px;font-weight:800;line-height:1.25;margin-top:2px}
                .nbq-ident .nbq-n{font-size:13px;opacity:.86;margin-top:2px}
                .nbq-chip{display:inline-block;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.18);
                  border-radius:999px;padding:3px 10px;font-size:11.5px;font-weight:700;margin:6px 6px 0 0}
                .nbq-skor{display:inline-flex;align-items:center;gap:6px;background:#fdd100;color:#1f3a5f;
                  border-radius:999px;padding:5px 12px;font-size:13px;font-weight:800;margin:6px 6px 0 0}
                .nbq-angka input{min-height:50px;font-size:18px;font-weight:800;text-align:center;
                  border-radius:12px;padding:6px 8px;letter-spacing:.02em}
                @media (max-width:640px){
                  .nbq-ident{padding:14px;border-radius:14px}
                  .nbq-ident .nbq-t{font-size:17px}
                  .nbq-angka input{min-height:48px;font-size:17px}
                }
                </style>
                HTML));
    }

    private static function komponenForm(Schema $schema): array
    {
        return [
            Text::make('kartu_siswa')
                ->content(function (?SpmbNilaiQuran $record): HtmlString {
                    if (! $record?->registration) {
                        return new HtmlString('');
                    }

                    $r = $record->registration;
                    $skor = $record->skor_btq;

                    $chip = fn (string $teks) => '<span class="nbq-chip">'.$teks.'</span>';

                    return new HtmlString(
                        '<div class="nbq-ident">'
                        .'<div class="nbq-k">Nomor pendaftaran</div>'
                        .'<div class="nbq-t">'.e($r->nomor).'</div>'
                        .'<div class="nbq-n">'.e($r->nama_siswa).'</div>'
                        .'<div>'
                        .$chip('Baca: '.($record->skor_baca ?? '—'))
                        .$chip('Tulis: '.($record->skor_tulis ?? '—'))
                        .$chip('Sholat: '.($record->skor_sholat ?? '—'))
                        .'</div>'
                        .'<div><span class="nbq-skor">Nilai BTQ: '.($skor ?? '—').'</span></div>'
                        .'</div>'
                    );
                })
                ->visibleOn('edit'),

            Select::make('registration_id')
                ->label('Siswa (nomor pendaftaran / nama)')
                ->relationship('registration', 'nama_siswa')
                ->searchable(['nomor', 'nama_siswa'])
                ->required()
                ->getOptionLabelFromRecordUsing(fn ($record): string => $record->nomor.' — '.$record->nama_siswa)
                ->unique(ignoreRecord: true)
                ->helperText('Ketik nomor pendaftaran (mis. SPMB-2027-0001) atau nama siswa.')
                ->visibleOn('create')
                ->columnSpanFull(),

            Section::make("Baca Al-Qur'an")
                ->description('Isi 0–100 untuk tiap kriteria.')
                ->schema([
                    TextInput::make('baca_tajwid')->label('Tajwid')
                        ->numeric()->minValue(0)->maxValue(100)->inputMode('numeric')
                        ->extraAttributes(['class' => 'nbq-angka']),
                    TextInput::make('baca_makhraj')->label('Makhraj')
                        ->numeric()->minValue(0)->maxValue(100)->inputMode('numeric')
                        ->extraAttributes(['class' => 'nbq-angka']),
                    TextInput::make('baca_kelancaran')->label('Kelancaran')
                        ->numeric()->minValue(0)->maxValue(100)->inputMode('numeric')
                        ->extraAttributes(['class' => 'nbq-angka']),
                ])
                ->columns(['default' => 3])
                ->columnSpanFull(),

            Section::make('Tulis Arab')
                ->schema([
                    TextInput::make('tulis_ketepatan')->label('Ketepatan huruf')
                        ->numeric()->minValue(0)->maxValue(100)->inputMode('numeric')
                        ->extraAttributes(['class' => 'nbq-angka']),
                    TextInput::make('tulis_kerapian')->label('Kerapian tulisan')
                        ->numeric()->minValue(0)->maxValue(100)->inputMode('numeric')
                        ->extraAttributes(['class' => 'nbq-angka']),
                ])
                ->columns(['default' => 2])
                ->columnSpanFull(),

            Section::make('Praktek Sholat')
                ->schema([
                    TextInput::make('sholat_bacaan')->label('Bacaan')
                        ->numeric()->minValue(0)->maxValue(100)->inputMode('numeric')
                        ->extraAttributes(['class' => 'nbq-angka']),
                    TextInput::make('sholat_fiqih')->label('Fiqih')
                        ->numeric()->minValue(0)->maxValue(100)->inputMode('numeric')
                        ->extraAttributes(['class' => 'nbq-angka']),
                    TextInput::make('sholat_gerakan')->label('Gerakan')
                        ->numeric()->minValue(0)->maxValue(100)->inputMode('numeric')
                        ->extraAttributes(['class' => 'nbq-angka']),
                ])
                ->columns(['default' => 3])
                ->columnSpanFull(),

            Section::make('Penilai & catatan')
                ->schema([
                    TextInput::make('penilai')
                        ->label('Nama penguji')
                        ->maxLength(120)
                        ->default(fn () => auth()->user()?->name)
                        ->placeholder('Nama guru penguji'),

                    Textarea::make('catatan')
                        ->label('Catatan (opsional)')
                        ->rows(2)
                        ->maxLength(400)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('registration.nomor')
                    ->label('Pendaftar')
                    ->weight('bold')
                    ->description(fn (SpmbNilaiQuran $r): string => $r->registration?->nama_siswa ?? '—')
                    ->searchable(['registration.nomor', 'registration.nama_siswa'])
                    ->sortable(),

                TextColumn::make('skor_btq')
                    ->label('BTQ')
                    ->badge()
                    ->placeholder('—')
                    ->formatStateUsing(fn ($state): string => $state === null ? '—' : $state)
                    ->color(fn ($state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 85 => 'success',
                        $state >= 70 => 'warning',
                        default => 'danger',
                    })
                    ->description(fn (SpmbNilaiQuran $r): string => 'B '.($r->skor_baca ?? '–').' · T '.($r->skor_tulis ?? '—'))
                    ->sortable(),

                TextColumn::make('penilai')
                    ->label('Penguji')
                    ->placeholder('—')
                    ->grow(false),

                IconColumn::make('lengkap')
                    ->label('Lengkap')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn (SpmbNilaiQuran $r): string => $r->lengkap ? 'Semua kriteria terisi' : 'Masih ada kriteria kosong'),
            ])
            ->filters([
                Filter::make('belum_lengkap')
                    ->label('Belum lengkap')
                    ->query(fn ($query) => $query->where(fn ($q) => $q
                        ->whereNull('baca_tajwid')->orWhereNull('baca_makhraj')->orWhereNull('baca_kelancaran')
                        ->orWhereNull('tulis_ketepatan')->orWhereNull('tulis_kerapian')
                        ->orWhereNull('sholat_bacaan')->orWhereNull('sholat_fiqih')->orWhereNull('sholat_gerakan')))
                    ->toggle(),

                Filter::make('sudah_lengkap')
                    ->label('Sudah lengkap')
                    ->query(fn ($query) => $query
                        ->whereNotNull('baca_tajwid')->whereNotNull('baca_makhraj')->whereNotNull('baca_kelancaran')
                        ->whereNotNull('tulis_ketepatan')->whereNotNull('tulis_kerapian')
                        ->whereNotNull('sholat_bacaan')->whereNotNull('sholat_fiqih')->whereNotNull('sholat_gerakan'))
                    ->toggle(),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()
                    ->label('Nilai')
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->emptyStateHeading('Belum ada nilai BTQ')
            ->emptyStateDescription('Tekan "Input nilai" lalu masukkan nomor pendaftaran siswa (SPMB-2027-0001).')
            ->emptyStateIcon('heroicon-o-book-open');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpmbNilaiQurans::route('/'),
            'create' => Pages\CreateSpmbNilaiQuran::route('/create'),
            'edit' => Pages\EditSpmbNilaiQuran::route('/{record}/edit'),
        ];
    }
}
