<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Order extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'total',
        'balance',
        'state_id',
        'client_id',
        'institution_id',
        'description',
        'key',
        'finish_date',
    ];

    /**
     * Get the Order state
     */
    public function state()
    {
        return $this->belongsTo(OrderState::class);
    }

    /**
     * Get the client of the Order
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the institution of the Order
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get the branch of the Order
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * The products that belong to the Order.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'orders_products', 'order_id', 'product_id')->withPivot('id','quantity', 'sublimate','size','embroidery','has_embroidery','has_sublimate', 'colors');
    }

    /**
     * Get the payments for order.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the logbooks for order.
     */
    public function logbooks()
    {
        return $this->belongsToMany(LogBook::class, 'logbook', 'id', 'model_id');
    }
    
    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Manipulations::FIT_CROP, 300, 300)
            ->nonQueued();
    }
}
