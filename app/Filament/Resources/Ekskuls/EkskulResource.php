<?php
namespace App\Filament\Resources\Ekskuls;

use App\Filament\Resources\Ekskuls\Pages\CreateEkskul;
use App\Filament\Resources\Ekskuls\Pages\EditEkskul;
use App\Filament\Resources\Ekskuls\Pages\ListEkskuls;
use App\Filament\Resources\Ekskuls\RelationManagers\PhotosRelationManager;
use App\Filament\Resources\Ekskuls\Schemas\EkskulForm;
use App\Filament\Resources\Ekskuls\Tables\EkskulsTable;
use App\Models\Ekskul;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EkskulResource extends Resource
{
    protected static ?string $slug = 'ekskul';

    protected static ?string $model = Ekskul::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Ekstrakurikuler';

    protected static ?string $modelLabel = 'Ekstrakurikuler';

    protected static ?string $pluralModelLabel = 'Ekstrakurikuler';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return EkskulForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EkskulsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEkskuls::route('/'),
            'create' => CreateEkskul::route('/create'),
            'edit' => EditEkskul::route('/{record}/edit'),
        ];
    }
}
