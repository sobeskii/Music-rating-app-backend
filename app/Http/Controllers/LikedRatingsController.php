<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikedRating\LikedRatingToggleRequest;
use App\Interfaces\RatingRepositoryInterface;
use App\Models\Rating;
use App\Services\LikedRatingService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;

class LikedRatingsController extends Controller
{
    /**
     * @var LikedRatingService
     */
    private $likedRatingService;
    /**
     * @var RatingRepositoryInterface
     */
    private $ratingRepository;

    /**
     * @param LikedRatingService $likedRatingService
     * @param RatingRepositoryInterface $ratingRepository
     */
    public function __construct(LikedRatingService $likedRatingService, RatingRepositoryInterface $ratingRepository)
    {
        $this->middleware(['json']);
        $this->likedRatingService = $likedRatingService;
        $this->ratingRepository = $ratingRepository;
    }

    /**
     * @param Rating $rating
     * @param LikedRatingToggleRequest $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function toggle(Rating $rating, LikedRatingToggleRequest $request): JsonResponse
    {
        $this->likedRatingService->handleLikeAction($rating, $request->validated());

        $rating = $this->ratingRepository->getUserReleaseRatings($rating->release_id)->where('id','=',$rating->id)->first();

        return response()->json([
            'data' => ['like' => $request->validated(), 'rating' =>$rating ],
            'success' => ['Like has been updated!']
        ], 200);
    }
}

