<?php

namespace App\Resolvers;

use App\Models\Rating;
use App\Strategies\ReviewModerationInterface;

class ReviewFlagResolver
{
    /**
     * @var ReviewModerationInterface
     */
    private $reviewModeration;

    public function __construct(ReviewModerationInterface ...$reviewModeration)
    {
        $this->reviewModeration = $reviewModeration;
    }

    public function resolve(Rating $rating)
    {
        $rating->flaggedReviewReasons()->delete();

        foreach ($this->reviewModeration as $moderation){
            $moderation->moderate($rating);
        }
    }
}
