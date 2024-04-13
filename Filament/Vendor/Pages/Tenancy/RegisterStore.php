<?php

namespace App\Filament\Vendor\Pages\Tenancy;


use App\Models\User;
use App\Models\Store;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Cheesegrits\FilamentGoogleMaps\Fields\Geocomplete;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;


class RegisterStore extends RegisterTenant
{
    protected static string $view = 'filament.vendor.pages.tenancy.register-store';

    protected ?string $maxContentWidth = 'full';


    public static function getLabel(): string
    {
        return 'Register Store';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Store Info')
                            ->icon('heroicon-m-building-storefront')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('store_logo')->imageEditor()->imageEditorAspectRatios([
                                    '1:1',
                                    '2:2',
                                ])
                                    ->imageEditorMode(2)
                                    ->imagePreviewHeight('10')
                                    ->loadingIndicatorPosition('left')
                                    ->panelAspectRatio('1:1')
                                    ->panelLayout('integrated')
                                    ->removeUploadedFileButtonPosition('right')
                                    ->uploadButtonPosition('left')
                                    ->uploadProgressIndicatorPosition('left')->columnSpan(2),

                                SpatieMediaLibraryFileUpload::make('store_banner')
                                    ->collection('store_banners')->columnSpan(10),

                                TextInput::make('name')->columnSpan(6),
                                TextInput::make('email')->columnSpan(6),
                                TextInput::make('website')->columnSpan(6),
                                PhoneInput::make('phone')->formatAsYouType(false)->columnSpan(6),
                            ]),
                        Tabs\Tab::make('Location')
                            ->icon('heroicon-m-map-pin')
                            ->schema([
                                Geocomplete::make('full_address')->columnSpan(12),
                                TextInput::make('latitude')->columnSpan(6),
                                TextInput::make('longitude')->columnSpan(6),
                                Map::make('location')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $set('latitude', $state['lat']);
                                        $set('longitude', $state['lng']);
                                    })->columnSpan(12)
                                    ->autocomplete('full_address') // field on form to use as Places geocompletion field
                                    ->autocompleteReverse(true) // reverse geocode marker location to autocomplete field
                            ]),
                        /*   Tabs\Tab::make('Tab 3')
                            ->schema([]
                        ), */
                    ])->columns(12)->columnSpan('full')->persistTabInQueryString()
            ]);
    }

    protected function handleRegistration(array $data): Store
    {
        $store = Store::create($data);
        $store->users()->attach(auth()->user());

        return $store;
    }
}
