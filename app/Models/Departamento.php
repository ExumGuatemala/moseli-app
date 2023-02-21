<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';

    protected $fillable = [
        'name',
    ];

    /**
     * Get the municipios for the departamento.
     */
    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }
}
