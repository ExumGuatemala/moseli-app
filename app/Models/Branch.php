<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "branches";

    protected $fillable = [
        'name',
        'phone',
        'address'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
