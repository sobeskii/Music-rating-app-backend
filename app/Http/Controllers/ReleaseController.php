<?php

namespace App\Http\Controllers;

use Aerni\Spotify\Exceptions\SpotifyApiException;
use App\Repositories\RatingRepository;
use App\Services\ReleaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReleaseController extends Controller
{
    /**
     * @var ReleaseService
     */
    private $releaseService;

    /**
     * @param ReleaseService $releaseService
     */
    public function __construct(ReleaseService $releaseService)
    {
        $this->middleware(['json']);
        $this->releaseService = $releaseService;
    }

    /**
     * @param string $releaseId
     * @return JsonResponse
     */
    public function show(string $releaseId): JsonResponse
    {
        try {
            $result = $this->releaseService->handleShowRelease($releaseId);
        } catch (SpotifyApiException $apiException) {
            return response()->json(['error' => 'Release ID is not valid please try again.'], 404);
        }
        return response()->json($result, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show_chart(Request $request): JsonResponse
    {
        $perPage = (($request->perPage) != null && $request->perPage <= 20 && $request->perPage >= 5) ? (int)$request->perPage : 15;

        $query = [];

        if ($request->sort_by != null) {
            $query['sort_by'] = $request->sort_by;
        }

        if ($request->type != null) {
            $query['type'] = $request->type;
        }

        if ($request->release_date != null) {
            $query['release_date'] = $request->release_date;
        }

        $releases = RatingRepository::getBestRatedReleaseIds($query)->paginate($perPage)->toArray();

        return response()->json([
            'releases' => $releases,
            'request_items' => ['perPage' => $perPage],
        ], 200);
    }
}
