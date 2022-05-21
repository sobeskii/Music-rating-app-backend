<?php

namespace App\Http\Controllers;

use App\Interfaces\RatingRepositoryInterface;
use App\Services\ReleaseService;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * @var ReleaseService
     */
    private $releaseService;
    /**
     * @var RatingRepository
     */
    private $ratingRepository;

    /**
     * Create a new controller instance.
     *
     * @param ReleaseService $releaseService
     * @param RatingRepositoryInterface $ratingRepository
     */
    public function __construct(ReleaseService $releaseService, RatingRepositoryInterface $ratingRepository)
    {
        $this->middleware(['json']);
        $this->releaseService = $releaseService;
        $this->ratingRepository = $ratingRepository;
    }
}
