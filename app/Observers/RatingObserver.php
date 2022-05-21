<?php

namespace App\Observers;

use App\Models\Rating;
use App\Resolvers\ReviewFlagResolver;

class RatingObserver
{
    /**
     * @var ReviewFlagResolver
     */
    private $flagResolver;

    public function __construct(ReviewFlagResolver $flagResolver)
    {
        $this->flagResolver = $flagResolver;
    }
    /**
     * Handle the Rating "created" event.
     *
     * @param Rating $rating
     * @return void
     */
    public function created(Rating $rating)
    {
        $this->flagResolver->resolve($rating);
    }
    /**
     * Handle the Rating "updated" event.
     *
     * @param Rating $rating
     * @return void
     */
    public function updated(Rating $rating)
    {
        $this->flagResolver->resolve($rating);
    }
}
