<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'amount',
        'order_id',
    ];

    /**
     * Get the order for the payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
