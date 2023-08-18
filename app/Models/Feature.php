<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;
    protected $table = "features";
    protected $fillable = [
        'name',
        'product_types_id'
    ];

    public function sizes()
    {
        return $this->hasMany(Size::class, 'feature_id');
    }
    
    public function sizis()
    {
        return $this->hasMany(Size::class, 'feature_id');
    }
}
