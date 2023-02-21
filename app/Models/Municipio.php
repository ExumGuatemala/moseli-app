<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'departamento_id',
    ];

    /**
     * Get the departamento for the municipio.
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}
