<?php

namespace App\Strategies;

use App\Models\Rating;

interface ReviewModerationInterface
{
    public function moderate(Rating $review);
}
