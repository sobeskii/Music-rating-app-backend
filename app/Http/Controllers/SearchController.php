<?php

namespace App\Http\Controllers;

use App\Services\ReleaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
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
        $this->middleware('json');
        $this->releaseService = $releaseService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $term = ($request->term) != null ? $request->term : '';
        $offset = (($request->offset) != null) ? (int)$request->offset : 0;
        $perPage = (($request->perPage) != null && $request->perPage <= 20 && $request->perPage >= 5) ? (int)$request->perPage : 15;

        $releases = $this->releaseService->getSearchReleases([
                'term' => $term,
                'offset' => $offset,
                'perPage' => $perPage
            ]
        );

        return response()->json([
            'albums' => $releases,
            'request_items' => ['term' => $term,
                'offset' => $offset,
                'perPage' => $perPage,
            ]
        ], 200);
    }
}
