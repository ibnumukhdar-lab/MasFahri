<?php

namespace App\Filament\Resources\AlumniCeritas;

use App\Filament\Resources\AlumniCeritas\Pages\CreateAlumniCerita;
use App\Filament\Resources\AlumniCeritas\Pages\EditAlumniCerita;
use App\Filament\Resources\AlumniCeritas\Pages\ListAlumniCeritas;
use App\Filament\Resources\AlumniCeritas\Schemas\AlumniCeritaForm;
use App\Filament\Resources\AlumniCeritas\Tables\AlumniCeritasTable;
use App\Models\AlumniCerita;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AlumniCeritaResource extends Resource
{
    protected static ?string $slug = 'cerita-alumni';

    protected static ?string $model = AlumniCerita::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'SPMB & Alumni';

    protected static ?string $navigationLabel = 'Cerita Alumni';

    protected static ?string $modelLabel = 'Cerita alumni';

    protected static ?string $pluralModelLabel = 'Cerita alumni';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function getNavigationBadge(): ?string
    {
        $menunggu = AlumniCerita::where(fn ($q) => $q->where('tampil', false)->orWhereNull('diverifikasi_pada'))->count();

        return $menunggu > 0 ? (string) $menunggu : null;
    }

    public static function form(Schema $schema): Schema
    {
        return AlumniCeritaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AlumniCeritasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAlumniCeritas::route('/'),
            'create' => CreateAlumniCerita::route('/create'),
            'edit' => EditAlumniCerita::route('/{record}/edit'),
        ];
    }
}
