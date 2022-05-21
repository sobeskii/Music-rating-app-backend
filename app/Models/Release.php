<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Release extends Model
{
    use HasFactory;

    public $fillable = ['spotify_id', 'album_type', 'release_date', 'name', 'label', 'artist_id', 'release_image'];

    public $incrementing = false;

    protected $primaryKey = 'spotify_id';

    protected $keyType = 'string';

    public $timestamps = false;

    public function artist()
    {
        return $this->belongsTo('App\Models\Artist','artist_id','spotify_id');
    }

    public function tracks()
    {
        return $this->hasMany('App\Models\Track','release_id','spotify_id')
            ->orderBy('track_number','ASC');
    }

    public function ratings(){
        return $this->hasMany('App\Models\Rating','release_id');
    }
}
