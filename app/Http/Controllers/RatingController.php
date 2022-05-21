<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rating\RatingPutRequest;
use App\Interfaces\RatingRepositoryInterface;
use App\Models\ModerationRule;
use App\Models\Rating;
use App\Request\Rating\RatingDeleteRequest;
use App\Services\RatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RatingController extends Controller
{
    /**
     * @var RatingService
     */
    private $ratingService;
    /**
     * @var RatingRepositoryInterface
     */
    private $ratingRepository;

    public function __construct(RatingService $ratingService, RatingRepositoryInterface $ratingRepository)
    {
        $this->middleware(['json']);
        $this->ratingService = $ratingService;
        $this->ratingRepository = $ratingRepository;
    }

    /**
     * @param RatingPutRequest $request
     * @return JsonResponse
     */
    public function put(RatingPutRequest $request): JsonResponse
    {
        if (Auth::guard('api')->user()->id == $request->user()->id) {
            try {
                $rating_data = $this->ratingService->handleUpdatedRating($request->validated());
                return response()->json([
                    'data' => [
                        'user_rating' => $rating_data['user_rating'],
                        'rating_data' => [
                            'average' => $rating_data['average'],
                            'count' => $rating_data['count'],
                            'reviews' => $rating_data['reviews'],
                            'distribution' => $rating_data['distribution']
                        ],
                    ],
                    'success' => ['Rating has been updated!']
                ], 200);
            }
            catch (NotFoundHttpException $exception){
                return response()->json(['error' => 'Release not found'], 404);
            }
        }
        else {
            return response()->json(['error' => 'Content unavailable'], 403);
        }
    }

    /**
     * @param Rating $rating
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Rating $rating,Request $request): JsonResponse
    {
        if (Auth::guard('api')->user()->id == $request->user()->id) {
            $rating_data = $this->ratingService->handleDeletedRating($rating);
            return response()->json([
                'data' => [
                    'user_rating' => $rating_data['user_rating'],
                    'rating_data' => [
                        'average' => $rating_data['average'],
                        'count' => $rating_data['count'],
                        'reviews' => $rating_data['reviews'],
                        'distribution' => $rating_data['distribution']
                    ],
                ],
                'success' => ['Rating has been deleted!']
            ], 200);
        }
        else {
            return response()->json(['error' => 'Content unavailable'], 403);
        }
    }
}
