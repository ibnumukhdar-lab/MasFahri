<?php

namespace App\Filament\Resources\TeacherLinks;

use App\Filament\Resources\TeacherLinks\Pages\CreateTeacherLink;
use App\Filament\Resources\TeacherLinks\Pages\EditTeacherLink;
use App\Filament\Resources\TeacherLinks\Pages\ListTeacherLinks;
use App\Filament\Resources\TeacherLinks\Schemas\TeacherLinkForm;
use App\Filament\Resources\TeacherLinks\Tables\TeacherLinksTable;
use App\Models\TeacherLink;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TeacherLinkResource extends Resource
{
    protected static ?string $slug = 'tautan-guru';

    protected static ?string $model = TeacherLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Tautan Guru';

    protected static ?string $modelLabel = 'Tautan guru';

    protected static ?string $pluralModelLabel = 'Tautan Guru';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function form(Schema $schema): Schema
    {
        return TeacherLinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeacherLinksTable::configure($table);
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
            'index' => ListTeacherLinks::route('/'),
            'create' => CreateTeacherLink::route('/create'),
            'edit' => EditTeacherLink::route('/{record}/edit'),
        ];
    }
}
