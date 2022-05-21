<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    public $fillable = ['spotify_id', 'name', 'duration_ms', 'release_id','track_number'];

    public $timestamps = false;

    public function tracks()
    {
        return $this->belongsTo('App\Models\Release','release_id','spotify_id');
    }
}
