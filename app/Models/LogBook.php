<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogBook extends Model
{
    use HasFactory;
    protected $table = 'logbook';

    protected $fillable = [
        'type',
        'description',
        'user_id',
        'model_id',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }    
}
