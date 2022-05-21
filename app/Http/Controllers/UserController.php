<?php

namespace App\Http\Controllers;

use App\Interfaces\RatingRepositoryInterface;
use App\Services\ReleaseService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @var RatingRepositoryInterface
     */
    private $ratingRepository;
    /**
     * @var ReleaseService
     */
    private $releaseService;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @param RatingRepositoryInterface $ratingRepository
     * @param ReleaseService $releaseService
     * @param UserService $userService
     */
    public function __construct(RatingRepositoryInterface $ratingRepository,
                                ReleaseService            $releaseService,
                                UserService               $userService)
    {
        $this->middleware(['json']);
        $this->ratingRepository = $ratingRepository;
        $this->releaseService = $releaseService;
        $this->userService = $userService;
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show_profile(User $user) : JsonResponse
    {
        return response()->json(
            [   'user' => $user,
                'artists' => $user->getUserTopArtists(),
                'stats' =>  [
                    'decades'=>$this->ratingRepository->getUserRatingStatsByDecade($user),
                    'distribution'=> $this->ratingRepository->getUserRatingDistribution($user)
                    ]
            ], 200);
    }
    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show_reactions(User $user): JsonResponse
    {
        $reactions = $this->ratingRepository->getReviewsUserLiked($user)->get()->toArray();

        return response()->json([
            'user' => $user,
            'reactions' => $reactions], 200);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show_ratings(User $user): JsonResponse
    {
        $ratings = $this->ratingRepository->getUserRatings($user)->get()->toArray();

        return response()->json([
            'user' => $user,
            'ratings' => $ratings], 200);
    }

    /**
     * @return JsonResponse
     */
    public function discover_artists(): JsonResponse
    {
        $user = Auth::guard('api')->user();
        $recommendations = $this->userService->handleRecommendedArtists($user);

        return response()->json([
            'artists' => $recommendations
        ]);
    }
}
