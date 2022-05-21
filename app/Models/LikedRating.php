<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LikedRating extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['user_id', 'rating_id', 'is_like'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return BelongsTo
     */
    public function rating(): BelongsTo
    {
        return $this->belongsTo('App\Models\Rating');
    }

}
