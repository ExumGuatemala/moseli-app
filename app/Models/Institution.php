<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'address',
    ];

    /**
     * Get all the orders for the client.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
