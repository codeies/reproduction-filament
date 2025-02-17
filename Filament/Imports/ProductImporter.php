<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;


    public static function getColumns(): array
    {
        return [
            /*     ImportColumn::make('sin')
                ->requiredMapping()
                ->rules(['required', 'max:255']), */

            ImportColumn::make('name')
                ->requiredMapping()
                ->ignoreBlankState()
                ->rules(['required', 'max:255']),

            /*             ImportColumn::make('slug')
                //->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('description')
                ->rules(['max:65535']),
            ImportColumn::make('short_description')
                ->rules(['max:255']),
            ImportColumn::make('upc')
                ->rules(['max:255']),
            ImportColumn::make('ean')
                ->rules(['max:255']),
            ImportColumn::make('isbn')
                ->rules(['max:255']),
            ImportColumn::make('weight')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('length')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('width')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('height')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('product_status')
                //->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('user_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('store_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('brand_id')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('is_resellable')
                //->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']), */
        ];
    }

    public function resolveRecord(): ?Product
    {
        // return Product::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);
        //dd($this);
        return new Product();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
