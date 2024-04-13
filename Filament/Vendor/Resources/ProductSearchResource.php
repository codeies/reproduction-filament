<?php

namespace App\Filament\Vendor\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\ProductStatus;
use App\Models\StoreProduct;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use App\Filament\Vendor\Resources\ProductResource\Pages;
use App\Services\EcommerceHub\Vendor\StoreProductService;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;


class ProductSearchResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Browse Our Product Catalog';
    protected static ?string $pluralModelLabel = 'Browse Our Product Catalog';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->columnSpan('8')
                    ->schema([
                        Section::make()
                            ->schema([
                                // Product Name
                                TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }
                                        $set('slug', Str::slug($state));
                                    }),

                                // Product Slug
                                TextInput::make('slug')

                                    ->dehydrated()
                                    ->unique(Product::class, 'slug', ignoreRecord: true)
                                    ->required(),

                                // Product Description
                                MarkdownEditor::make('description')
                                    ->columnSpan('full'),
                                Textarea::make('short_description')
                                    ->columnSpan('full')
                            ])->columns(2),

                    ]),
                Section::make()
                    ->schema([
                        // Product Price
                        Select::make('product_status')
                            ->label('Status')
                            ->required()
                            ->options(ProductStatus::class) // Use enum cases directly
                            ->default(ProductStatus::ACTIVE), // Set default value

                        TextInput::make('price')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                        SelectTree::make('categories')->searchable()
                            ->relationship('categories', 'name', 'parent_id'),
                        Select::make('brand')
                            ->relationship('brand', 'name')->preload()->searchable()
                    ])->collapsible()->columnSpan('4'),

                Tabs::make('Tabs')->columnSpan('12')

                    ->tabs([
                        Tab::make('Images')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make(
                                    'product_image'
                                )
                                    ->optimize('webp')
                                    ->collection('product')
                                    //->imageCropAspectRatio('1:1')
                                    ->acceptedFileTypes(['image/jpeg', 'image/webp'])
                                    ->imageCropAspectRatio('1:1')
                                    ->imageResizeTargetWidth('800')
                                    ->imageResizeTargetHeight('800')
                                    //->maxSize(1024)
                                    //->maxFiles(5)
                                    ->imageEditor()
                                    ->reorderable()
                                    ->multiple(),

                            ]),
                        Tab::make('Product Identity')
                            ->schema([
                                // Product SKU
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->unique(ignoreRecord: true),
                                TextInput::make('upc')
                                    ->label('UPC')
                                    ->unique(ignoreRecord: true),
                                TextInput::make('ean')
                                    ->label('EAN')
                                    ->unique(ignoreRecord: true),
                                TextInput::make('isbn')
                                    ->label('ISBN')
                                    ->unique(ignoreRecord: true),


                            ])->columns(2),
                    ]),
            ])->columns(12);
    }
    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Split::make([

                    Tables\Columns\SpatieMediaLibraryImageColumn::make('product_image')->collection('product')->conversion('thumb')->grow(false)->height(130)
                        ->label('Image')->limit(1),


                    Stack::make([
                        TextColumn::make('brand.name')->searchable(),
                        TextColumn::make('name')->searchable(['name', 'sin', 'ean', 'isbn', 'upc']),
                        TextColumn::make('sin')->color('gray'),

                    ]),
                    /* IconColumn::make('has_product')
                        ->getStateUsing(fn ($record): bool => $record->ean !== null)
                        ->trueIcon('heroicon-o-check-badge')
                        ->falseIcon('heroicon-o-x-mark')
                        ->boolean(), */

                    /*     IconColumn::make('is_featured')
                        ->boolean(true)
                        ->trueIcon('heroicon-o-check-badge')
                        ->falseIcon('heroicon-o-x-mark'), */
                    Stack::make([

                        TextColumn::make('ean')
                            ->prefix('EAN ')
                            ->grow(false),
                        TextColumn::make('isbn')
                            ->prefix('ISBN ')
                            ->grow(false),
                        TextColumn::make('upc')
                            ->prefix('UPC ')
                            ->grow(false),
                    ])
                        ->visibleFrom('md'),

                ])
            ])
            ->recordUrl(null)
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('sellThisProduct')
                        ->label(function ($record) {
                            $store = Filament::getTenant();
                            $hasProduct =  StoreProductService::hasProduct($store->id, $record->id);
                            if ($hasProduct) {
                                return 'Already Selling';
                            }
                        })
                        ->disabled(function ($record) {
                            $store = Filament::getTenant();
                            return StoreProductService::hasProduct($store->id, $record->id);
                        })
                        ->icon('heroicon-m-banknotes')
                        ->form([

                            TextInput::make('price')->required(),
                            TextInput::make('seller_sku')
                        ])
                        ->action(function (array $data, Product $record): void {
                            $storeProduct = new StoreProduct();
                            $store = Filament::getTenant();

                            $sellerSku = $data['seller_sku'] ?? $storeProduct::generateSKU($record->name, $store->name);

                            // Populate the store_product table fields
                            $storeProduct->product_id = $record->id;
                            $storeProduct->price = $data['price'];
                            $storeProduct->store_id = $store->id;
                            $storeProduct->seller_sku = $sellerSku;
                            // Save the store_product record
                            $storeProduct->save();
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function scopeEloquentQueryToTenant(Builder $query, ?Model $tenant): Builder
    {
        return $query;
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            //'edit' => Pages\EditProduct::route('/{record}/edit'),
            'search' => Pages\ProductSearch::route('/search'),
        ];
    }
}
