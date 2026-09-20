<?php

namespace App\Filament\Resources\Beranda;

use App\Filament\Resources\Beranda\Pages\CreateBeranda;
use App\Filament\Resources\Beranda\Pages\EditBeranda;
use App\Filament\Resources\Beranda\Pages\ListBeranda;
use App\Filament\Resources\Beranda\Schemas\BerandaForm;
use App\Filament\Resources\Beranda\Tables\BerandaTable;
use App\Models\BerandaBagian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class BerandaResource extends Resource
{
    protected static ?string $slug = 'beranda';

    protected static ?string $model = BerandaBagian::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Halaman Beranda';

    protected static ?string $modelLabel = 'Bagian Beranda';

    protected static ?string $pluralModelLabel = 'Bagian Beranda';

    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function form(Schema $schema): Schema
    {
        return BerandaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BerandaTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBeranda::route('/'),
            'create' => CreateBeranda::route('/create'),
            'edit' => EditBeranda::route('/{record}/edit'),
        ];
    }
}
