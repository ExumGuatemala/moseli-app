<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderState extends Model
{
    use HasFactory, SoftDeletes;

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
