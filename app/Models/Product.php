<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name',
        'existence',
        'order',
        'sale_price',
        'description',
        'institution_id',
    ];

    protected $casts = [
        'colors' => 'array',
        'has_embroidery' => 'boolean',
        'has_sublimate' => 'boolean',
    ];



    /**
     * Get the color for the product.
     */
    public function type()
    {
        return $this->belongsTo(ProductType::class);
    }

    /**
     * The orders that belong to the Product.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'orders_products', 'product_id', 'order_id')->withPivot('id','quantity', 'sublimate','size','embroidery','has_embroidery','has_sublimate','special_size','has_special_size','colors');
    }

    /**
     * Get the institution that owns the Product
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Manipulations::FIT_CROP, 300, 300)
            ->nonQueued();
    }
}
