<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use GuzzleHttp\Client;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'spotify_id',
        'spotify_token',
        'spotify_refresh_token',
        'spotify_avatar',
        'muted',
        'muted_until',
        'spotify_token_expiresin',
        'mute_reason',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return Collection
     */
    public function getUserTopArtists(): Collection
    {
        return $this->topArtists()->with('artist')->get();
    }

    /**
     * @return HasMany
     */
    public function likes()
    {
        return $this->hasMany('App\Models\LikedRating');
    }

    /**
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo('App\Models\Role');
    }

    /**
     * @return HasMany
     */
    public function ratings()
    {
        return $this->hasMany('App\Models\Rating');
    }

    /**
     * @return mixed
     */
    public function getRatingCount()
    {
        return $this->ratings()->whereUserId($this->id)->count();
    }

    /**
     * @param $release
     * @return Model|HasMany|null
     */
    public function getReleaseRating($release)
    {
        return $this->ratings()->firstWhere('release_id', $release);
    }

    /**
     * @param $release
     * @return bool
     */
    public function isReleaseRated($release): bool
    {
        return $this->ratings()->firstWhere('release_id', $release) != null;
    }

    public function getLatestRating()
    {
        return $this->ratings()->orderBy('created_at','desc')->first();
    }

    public function getMutedReason()
    {
        return $this->getLatestRating()->flaggedReviewReasons()->orderBy('created_at','desc')->first()->moderation_rule->reason;
    }

    /**
     * @return HasMany
     */
    public function topArtists(): HasMany
    {
        return $this->hasMany('App\Models\UserTopArtist')
            ->orderBy('position', 'ASC');
    }

    public function getSpotifyTokenAttribute($value)
    {
        if(Carbon::now() > Carbon::parse($this->spotify_token_expiresin) && $this->spotify_token_expiresin != '0000-00-00 00:00:00' ) {
            $data = [
                "refresh_token" => $this->spotify_refresh_token,
                "grant_type" => 'refresh_token',
                "client_id" => config('spotify.auth.client_id'),
            ];


            $client = new Client(['base_uri' => 'https://accounts.spotify.com']);
            $headers = [
                'Authorization' => 'Basic ' . (base64_encode(config('spotify.auth.client_id') . ':' . config('spotify.auth.client_secret'))),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ];

            $response = $client->request('POST', '/api/token', ['form_params' => $data, 'headers' => $headers]);

            $tokenData = json_decode($response->getBody()->getContents());

            $this->spotify_token_expiresin = Carbon::now()->addSeconds($tokenData->expires_in);
            $this->save();

            return $tokenData->access_token;
        }
        else {
            return $value;
        }
    }
}
