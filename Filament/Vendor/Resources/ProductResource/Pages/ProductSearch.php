<?php

namespace App\Filament\Vendor\Resources\ProductResource\Pages;

use App\Filament\Vendor\Resources\ProductSearchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ProductSearch extends ListRecords
{
    protected static string $resource = ProductSearchResource::class;
    protected static string $view = 'filament.vendor.pages.product-search';
    protected ?string $subheading = '';
    public $tenant;
    public function getBreadcrumbs(): array
    {
        return [];
    }
    public function getTitle(): string
    {
        return '';
    }
}
