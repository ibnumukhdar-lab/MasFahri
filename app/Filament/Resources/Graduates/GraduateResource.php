<?php

namespace App\Filament\Resources\Graduates;

use App\Filament\Resources\Graduates\Pages\CreateGraduate;
use App\Filament\Resources\Graduates\Pages\EditGraduate;
use App\Filament\Resources\Graduates\Pages\ListGraduates;
use App\Filament\Resources\Graduates\Schemas\GraduateForm;
use App\Filament\Resources\Graduates\Tables\GraduatesTable;
use App\Models\Graduate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GraduateResource extends Resource
{
    protected static ?string $slug = 'kelulusan';

    protected static ?string $model = Graduate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'SPMB & Alumni';

    protected static ?string $navigationLabel = 'Kelulusan';

    protected static ?string $modelLabel = 'Data kelulusan';

    protected static ?string $pluralModelLabel = 'Kelulusan';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return GraduateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GraduatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGraduates::route('/'),
            'create' => CreateGraduate::route('/create'),
            'edit' => EditGraduate::route('/{record}/edit'),
        ];
    }
}
