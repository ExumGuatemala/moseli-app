<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'key',
        'start_date',
        'end_date',
        'state_id',
    ];

    /**
     * Get the Order state
     */
    public function state()
    {
        return $this->belongsTo(OrderState::class);
    }
}
