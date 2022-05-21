<?php

namespace App\Services;

use App\Interfaces\RatingRepositoryInterface;
use App\Models\Rating;
use Illuminate\Auth\AuthenticationException;

class LikedRatingService
{
    /**
     * @throws AuthenticationException
     */
    public function handleLikeAction(Rating $rating, array $likeData): void
    {
        if (auth()->user() != null) {
            if ($rating->ratingHasUserLike(auth()->user(), $likeData['is_like'])) {
                $rating->likes()->detach(auth()->id());
            } else {
                $rating->likes()->attach(auth()->id(), ['is_like' => $likeData['is_like']]);
            }
        } else {
            throw new AuthenticationException("User is not logged in!");
        }
    }
}
