<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'lastname',
        'email',
        'key',
        'nit',
        'address',
        'phone1',
        'phone2',
    ];

    /**
     * Get the municipio for the client.
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    /**
     * Get all the orders for the client.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
