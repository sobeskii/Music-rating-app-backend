<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\FlaggedReviewReason;

class Rating extends Model
{
    use HasFactory, HasEvents;

    protected $table = 'user_ratings';

    protected $fillable = ['rating', 'release_id', 'user_id', 'review', 'artist_id'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return BelongsToMany
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\User', 'liked_ratings');
    }

    /**
     * @return int
     */
    public function likeCount()
    {
        return $this->likes()->count();
    }

    /**
     * @return BelongsTo
     */
    public function release(): BelongsTo
    {
        return $this->belongsTo('App\Models\Release','release_id');
    }

    /**
     * @return HasMany
     */
    public function flaggedReviewReasons() : HasMany
    {
        return $this->hasMany('App\Models\FlaggedReviewReason');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopecomputeRatingInfo($query,$user_id = null)
    {
        if($user_id == null) {
            $user_id = (Auth::guard('api')->check()) ? Auth::guard('api')->user()->id : null;
        }
        return $query->withCount(['likes as liked' => function ($q) use($user_id) {
            $q->where('user_id', $user_id)->
            where('is_like', true);
        }])->withCount(['likes as disliked' => function ($q) use($user_id) {
            $q->where('user_id', $user_id)->
            where('is_like', false);
        }])->withCasts(['liked' => 'boolean'])->
        withCasts(['disliked' => 'boolean'])->
        withCount(['likes as like_count' => function ($q) {
            $q->where('is_like', true);
        }])->
        withCount(['likes as dislike_count' => function ($q) {
            $q->where('is_like', false);
        }]);
    }

    public function ratingHasUserLike(?User $user, bool $isLike): bool
    {
        return $this->likes()->where('user_id', $user->id)->where('is_like', $isLike)->exists();
    }
}
