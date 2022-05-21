<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTopArtist extends Model
{
    use HasFactory;

    protected $table = 'user_top_artists';

    protected $fillable = ['artist_id','user_id','image','name','position'];

    public $timestamps = false;

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
    public function artist(): BelongsTo
    {
        return $this->belongsTo('App\Models\Artist','artist_id');
    }
}
