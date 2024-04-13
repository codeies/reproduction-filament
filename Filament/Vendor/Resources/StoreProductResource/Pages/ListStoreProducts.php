<?php

namespace App\Filament\Vendor\Resources\StoreProductResource\Pages;

use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Vendor\Resources\StoreProductResource;

class ListStoreProducts extends ListRecords
{
    protected static string $resource = StoreProductResource::class;

    protected function getHeaderActions(): array
    {
        $tenant = Filament::getTenant();
        return [
            Actions\Action::make('Add Product')
                ->url(route('filament.vendor.resources.products.search', ['tenant' => $tenant])),
            //Actions\CreateAction::make(),
        ];
    }
}
