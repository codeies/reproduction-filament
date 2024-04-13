<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Enums\ProductStatus;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\Conversions\Manipulations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    protected $fillable = [
        'name', 'slug', 'description', 'short_description', 'product_status', 'isbn', 'upc', 'ean', 'store_id'
    ];

    public static function generateUniqueCode(): string
    {
        $code = strtoupper(Str::random(10));
        while (Product::where('sin', $code)->exists()) {
            $code = strtoupper(Str::random(10));
        }
        return $code;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->sin = Product::generateUniqueCode();
        });
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(
                \Spatie\Image\Enums\Fit::Crop,
                200,
                200,
            );
    }


    protected $casts = [
        'status' => ProductStatus::class,
    ];

    public function categories()
    {
        return $this->BelongsToMany(ProductCategory::class);
    }
    public function brand()
    {
        return $this->BelongsTo(Brand::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
