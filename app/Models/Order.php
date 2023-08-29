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
}
