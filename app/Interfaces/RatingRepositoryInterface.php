<?php

namespace App\Interfaces;

use App\Models\User;

interface RatingRepositoryInterface
{
    public function getReleaseRatings($releaseId);

    public function getArtistsReleaseRatings($artistId);

    public function getRatingCount($releaseId);

    public function getRatingAverage($releaseId);

    public function getBestRatedReleaseIds($query);

    public function getReviews($releaseId);

    public function getReviewsUserLiked(User $user);

    public function getUserRatings(User $user);

    public function getFlaggedReviews(string $term, int $perPage);

    public function getReleaseRatingDistribution($releaseId);

    public function getUserRatingDistribution($user);
}
