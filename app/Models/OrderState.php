<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderState extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'process_order'
    ];

    /**
     * Get the Orders for any given state.
     */
    public function quotes()
    {
        return $this->hasMany(Order::class);
    }
}
