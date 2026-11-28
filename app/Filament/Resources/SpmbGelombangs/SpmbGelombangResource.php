<?php

namespace App\Filament\Resources\SpmbGelombangs;

use App\Filament\Resources\SpmbGelombangs\Pages\CreateSpmbGelombang;
use App\Filament\Resources\SpmbGelombangs\Pages\EditSpmbGelombang;
use App\Filament\Resources\SpmbGelombangs\Pages\ListSpmbGelombangs;
use App\Filament\Resources\SpmbGelombangs\RelationManagers\JadwalTesRelationManager;
use App\Filament\Resources\SpmbGelombangs\Schemas\GelombangForm;
use App\Filament\Resources\SpmbGelombangs\Tables\GelombangsTable;
use App\Models\SpmbGelombang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Gelombang SPMB + jadwal tes masuk.
 * Satu gelombang boleh punya banyak jadwal tes; halaman /spmb membaca data ini.
 */
class SpmbGelombangResource extends Resource
{
    protected static ?string $slug = 'gelombang-spmb';

    protected static ?string $model = SpmbGelombang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Penerimaan SPMB';

    protected static ?string $navigationLabel = 'Gelombang & Jadwal';

    protected static ?string $modelLabel = 'Gelombang';

    protected static ?string $pluralModelLabel = 'Gelombang & Jadwal';

    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return GelombangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GelombangsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            JadwalTesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpmbGelombangs::route('/'),
            'create' => CreateSpmbGelombang::route('/create'),
            'edit' => EditSpmbGelombang::route('/{record}/edit'),
        ];
    }
}
