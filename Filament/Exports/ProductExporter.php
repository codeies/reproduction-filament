<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('sin'),
            ExportColumn::make('name'),
            ExportColumn::make('slug'),
            ExportColumn::make('description'),
            ExportColumn::make('short_description'),
            ExportColumn::make('upc'),
            ExportColumn::make('ean'),
            ExportColumn::make('isbn'),
            ExportColumn::make('weight'),
            ExportColumn::make('length'),
            ExportColumn::make('width'),
            ExportColumn::make('height'),
            ExportColumn::make('product_status'),
            ExportColumn::make('user_id'),
            ExportColumn::make('store_id'),
            ExportColumn::make('brand_id'),
            ExportColumn::make('is_resellable'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your product export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
