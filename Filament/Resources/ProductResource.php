<?php

namespace App\Filament\Resources;

use App\Models\Product;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\ProductStatus;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\ProductResource\Pages;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $activeNavigationIcon = 'heroicon-s-shopping-bag';

    protected static ?string $navigationLabel = 'Products';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationGroup = 'Shop';

    //    protected static ?string $recordTitleAttribute = 'name';

    //    protected static int $globalSearchResultsLimit = 20;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /*     public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'description'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Brand' => $record->Brand->name,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['brand']);
    }
 */

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
            ->headerActions([
                \Filament\Tables\Actions\ExportBulkAction::make()
                    ->exporter(\App\Filament\Exports\ProductExporter::class),
                \Filament\Tables\Actions\ImportAction::make()
                    ->importer(\App\Filament\Imports\ProductImporter::class)
            ])
            ->columns([
                //ImageColumn::make('image'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('brand.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_visible')
                    ->sortable()
                    ->toggleable()
                    ->label('Visibility')
                    ->boolean(),

                TextColumn::make('price')
                    ->sortable()
                    ->currency(null, true),

                TextColumn::make('quantity')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('published_at')
                    ->date()
                    ->sortable(),

                TextColumn::make('type'),
            ])
            ->filters([
                TernaryFilter::make('is_visible')
                    ->label('Visibility')
                    ->boolean()
                    ->trueLabel('Only Visible Products')
                    ->falseLabel('Only Hidden Products')
                    ->native(false),

                SelectFilter::make('brand')
                    ->relationship('brand', 'name')
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
