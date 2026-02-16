<?php

namespace App\Filament\Resources\Shop\Categories;

use App\Filament\Clusters\Products\ProductsCluster;
use App\Filament\Resources\Shop\Categories\Pages\CreateCategory;
use App\Filament\Resources\Shop\Categories\Pages\EditCategory;
use App\Filament\Resources\Shop\Categories\Pages\ListCategories;
use App\Filament\Resources\Shop\Categories\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\Shop\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Shop\Categories\Tables\CategoriesTable;
use App\Models\Shop\ProductCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static ?string $cluster = ProductsCluster::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationParentItem = 'Products';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'categories';

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
