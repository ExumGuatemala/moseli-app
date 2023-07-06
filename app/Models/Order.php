<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'balance',
        'state_id',
        'client_id',
        'description',
        'key'
    ];

    protected $casts = [
        'colors' => 'array',
        'has_embroidery' => 'boolean',
        'has_sublimate' => 'boolean',
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
        
        // return $this->belongsToMany(Product::class, 'orders_products', 'order_id', 'product_id')->withPivot('quantity', 'sublimate','size','embroidery','has_embroidery','has_sublimate', 'colors');
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Get the payments for order.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
