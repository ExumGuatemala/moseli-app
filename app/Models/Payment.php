<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
    ];

    /**
     * Get the order for the payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
