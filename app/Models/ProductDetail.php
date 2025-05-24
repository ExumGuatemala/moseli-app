<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fabric_type',
        'fabric_code',
        'lining_color',
        'lining_type',
        'pocket_type',
        'pocket_quantity',
        'sleeve_type',
        'hood_type',
        'neckline_type',
        'elastic_waist',
        'buttons_neckline',
        'ziper_position',
        'ziper_color',
        'resort_color',
        'elastic',
        'special_stitching',
        'rivets',
        'buttons_color',
        'strap_color',
        'thread_color',
        'reflective_color',
        'reflective_position',
        'reflective_width',
        'reflective_velcro',
        'collar_cuff',
        'general_observations',
        'sewing_observations',
        'personalization_type',
        'personalization_size',
        'logos',
        'fabric_background_color',
        'monogram',
        'personalization_observations',
        'product_id',
        'institution_id'
    ];

    protected $casts = [
        'fabric_type' => 'string',
        'fabric_code' => 'string',
        'lining_color' => 'string',
        'lining_type' => 'string',
        'pocket_type' => 'string',
        'pocket_quantity' => 'string',
        'sleeve_type' => 'string',
        'hood_type' => 'string',
        'neckline_type' => 'string',
        'elastic_waist' => 'string',
        'buttons_neckline' => 'string',
        'ziper_position' => 'string',
        'ziper_color' => 'string',
        'resort_color' => 'string',
        'elastic' => 'string',
        'special_stitching' => 'string',
        'rivets' => 'string',
        'buttons_color' => 'string',
        'strap_color' => 'string',
        'thread_color' => 'string',
        'reflective_color' => 'string',
        'reflective_position' => 'string',
        'reflective_width' => 'string',
        'reflective_velcro' => 'string',
        'collar_cuff' => 'string',
        'general_observations' => 'string',
        'sewing_observations' => 'string',
        'personalization_type' => 'string',
        'personalization_size' => 'string',
        'logos' => 'array',
        'fabric_background_color' => 'string',
        'monogram' => 'string',
        'personalization_observations' => 'string',
        'product_id' => 'integer',
        'institution_id' => 'integer'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
} 