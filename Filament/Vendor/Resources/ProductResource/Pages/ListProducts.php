<?php

namespace App\Filament\Vendor\Resources\ProductResource\Pages;

use App\Filament\Vendor\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;
    //protected static string $view = 'filament.resources.product-resource.pages.list-products';

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
