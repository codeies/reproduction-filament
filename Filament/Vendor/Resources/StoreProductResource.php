<?php

namespace App\Filament\Vendor\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\StoreProduct;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Vendor\Resources\StoreProductResource\Pages;
use App\Filament\Vendor\Resources\StoreProductResource\RelationManagers;
use App\Filament\Vendor\Resources\StoreProductResource\Pages\EditStoreProduct;
use App\Filament\Vendor\Resources\StoreProductResource\Pages\ListStoreProducts;
use App\Filament\Vendor\Resources\StoreProductResource\Pages\CreateStoreProduct;

class StoreProductResource extends Resource
{
    protected static ?string $model = StoreProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Store Inventory';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Find Product')
                        ->schema([
                            Select::make('user_id')->columnSpan(6)
                                ->label('Store Manager')
                                ->allowHtml() // Apply the new modifier to enable HTML in the options - it's disabled by default
                                ->searchable() // Don't forget to make it searchable otherwise there is no choices.js magic!
                                ->getSearchResultsUsing(function (string $search) {
                                    $users = Product::where('name', 'like', "%{$search}%")->limit(50)->get();

                                    return $users->mapWithKeys(function ($user) {
                                        return [$user->getKey() => static::getCleanOptionString($user)];
                                    })->toArray();
                                })
                                ->getOptionLabelUsing(function ($value): string {
                                    $user = Product::find($value);

                                    return static::getCleanOptionString($user);
                                }),
                        ]),
                    Wizard\Step::make('Delivery')
                        ->schema([
                            // ...
                        ]),
                    Wizard\Step::make('Billing')
                        ->schema([
                            // ...
                        ]),
                ])->columns('12')->columnSpanFull()
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('product.product_image')->collection('product')->conversion('thumb')->height(50)
                    ->label('Image')->limit(1),

                TextColumn::make('product.name')->searchable(['name', 'sin', 'ean', 'isbn', 'upc']),

                TextColumn::make('product.brand.name')->searchable(),
                TextColumn::make('seller_sku')->color('gray'),
                TextColumn::make('product.categories.name')->color('gray')->badge(),

                TextInputColumn::make('price')->grow(true),
                ToggleColumn::make('status')->grow(false)

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListStoreProducts::route('/'),
            //'create' => Pages\CreateStoreProduct::route('/create'),
            //'edit' => Pages\EditStoreProduct::route('/{record}/edit'),
        ];
    }
}
