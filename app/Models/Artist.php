<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artist extends Model
{
    use HasFactory;

    public $fillable = ['spotify_id', 'name', 'followers', 'artist_image'];

    public $incrementing = false;
    protected $primaryKey = 'spotify_id';

    protected $keyType = 'string';

    public $timestamps = false;

    /**
     * @return HasMany
     */
    public function releases(): HasMany
    {
        return $this->hasMany('App\Models\Release','artist_id','spotify_id');
    }

    /**
     * @return HasMany
     */
    public function topArtists(): HasMany
    {
        return $this->hasMany('App\Models\TopArtist','artist_id');
    }
}
