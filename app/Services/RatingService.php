<?php

namespace App\Services;

use App\Interfaces\RatingRepositoryInterface;
use App\Models\Rating;
use App\Models\Release;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RatingService
{
    /**
     * @var RatingRepositoryInterface
     */
    private $ratingRepository;

    public function __construct(RatingRepositoryInterface $ratingRepository)
    {
        $this->ratingRepository = $ratingRepository;
    }

    public function handleUpdatedRating(array $ratingData)
    {
        $release = Release::where('spotify_id','=',$ratingData['release_id'])->first();

        if($release != null) {
            $rating = Rating::where([['release_id', '=', $ratingData['release_id']],
                ['user_id', '=', $ratingData['user_id']],
                ['artist_id', '=', $ratingData['artist_id']]])->first();

            if ($rating !== null) {
                $rating->update($ratingData);
            } else {
                $rating = Rating::create([
                    'review' => $ratingData['review'],
                    'rating' => $ratingData['rating'],
                    'release_id' => $ratingData['release_id'],
                    'user_id' => $ratingData['user_id'],
                    'artist_id' => $ratingData['artist_id'],
                ]);
            }

            return [
                'distribution' => $this->ratingRepository->getReleaseRatingDistribution($ratingData['release_id']),
                'average' => $this->ratingRepository->getRatingAverage($ratingData['release_id']),
                'count' => $this->ratingRepository->getRatingCount($ratingData['release_id']),
                'reviews' => $this->ratingRepository->getReviews($ratingData['release_id'])->get(),
                'user_rating' => $rating,
            ];
        }
        throw new NotFoundHttpException();
    }

    public function handleDeletedRating(Rating $rating): array
    {
        $rating->likes()->detach();
        $rating->delete();

        return [
            'distribution' => $this->ratingRepository->getReleaseRatingDistribution($rating->release_id),
            'average' => $this->ratingRepository->getRatingAverage($rating->release_id),
            'count' => $this->ratingRepository->getRatingCount($rating->release_id),
            'reviews' => $this->ratingRepository->getReviews($rating->release_id)->get(),
            'user_rating' => $rating,
        ];
    }
}
