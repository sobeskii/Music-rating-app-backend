<?php

namespace App\Http\Controllers;

use Aerni\Spotify\Exceptions\SpotifyApiException;
use Illuminate\Http\JsonResponse;
use App\Services\ArtistService;

class ArtistController extends Controller
{

    /**
     * @var ArtistService
     */
    private $artistService;

    /**
     * @param ArtistService $artistService
     */
    public function __construct(ArtistService $artistService)
    {
        $this->middleware(['json']);
        $this->artistService = $artistService;
    }

    /**
     * @param string $artistId
     * @return JsonResponse
     */
    public function show(string $artistId): JsonResponse
    {
        try {
            $result = $this->artistService->handleShowArtist($artistId);
        } catch (SpotifyApiException $apiException) {
            return response()->json(['error' => 'Artist ID is not valid please try again.'], 404);
        }
        return response()->json($result, 200);
    }
}
