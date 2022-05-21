<?php

namespace App\Services;

use Aerni\Spotify\Exceptions\SpotifyApiException;
use Aerni\Spotify\Facades\SpotifyFacade as Spotify;
use App\Interfaces\RatingRepositoryInterface;
use App\Models\Artist;
use App\Models\Release;
use App\Models\Track;
use Illuminate\Support\Facades\Auth;

class ReleaseService
{
    /**
     * @var RatingRepositoryInterface
     */
    private $ratingRepository;
    /**
     * @var ArtistService
     */
    private $artistService;

    public function __construct(RatingRepositoryInterface $ratingRepository, ArtistService $artistService)
    {
        $this->ratingRepository = $ratingRepository;
        $this->artistService = $artistService;
    }

    public function handleShowRelease(string $releaseId): array
    {
        $release = Release::where('spotify_id','=',$releaseId)->with('artist')->first();

        if ($release == null) {
            $release = $this->createRelease($releaseId);
        }

        $release['tracks'] = $release->tracks;
        $user = Auth::guard('api')->user();

        $userRating = Auth::guard('api')->check() ? $user->getReleaseRating($release->spotify_id) : null;
        $rating_data = [
            'distribution' => $this->ratingRepository->getReleaseRatingDistribution($release->spotify_id),
            'average' => $this->ratingRepository->getRatingAverage($release->spotify_id),
            'count' => $this->ratingRepository->getRatingCount($release->spotify_id),
            'reviews' => $this->ratingRepository->getReviews($release->spotify_id)->get(),
        ];
        return [
            'release' => $release,
            'primary_artist' => $release->artist,
            'user_rating_data' => [
                'rating' => ($userRating != null) ? $userRating->rating : 0,
                'review' => ($userRating != null) ? $userRating->review : null,
                'user_id' => ($userRating != null) ? $userRating->user_id : null,
                'id' => ($userRating != null) ? $userRating->id : null,
            ],
            'rating_data' => $rating_data,
        ];
    }

    public function getSearchReleases(array $params)
    {
        $result = ['items' => []];
        try {
            $result = Spotify::searchItems($params['term'], 'album')->offset($params['offset'])
                ->limit($params['perPage'])
                ->get()['albums'];
        } catch (SpotifyApiException $exception) {
            return $result;
        }
        return $result;
    }

    public function createRelease(string $releaseId)  {
        $apiRelease = Spotify::album($releaseId)->get();

        $artistId = $apiRelease['artists'][0]['id'];

        $artist = Artist::where('spotify_id', '=',$artistId)->first();

        if($artist == null) {
            $this->artistService->createArtist($artistId);
        }

        $release =  Release::create([
            'spotify_id'    =>  $apiRelease['id'],
            'album_type'    =>  $apiRelease['album_type'],
            'release_date'  =>  $apiRelease['release_date'],
            'name'  =>  $apiRelease['name'],
            'label' =>  $apiRelease['label'],
            'artist_id' =>  $apiRelease['artists'][0]['id'],
            'release_image' =>  $apiRelease['images'][0]['url'],
        ]);

        foreach ($apiRelease['tracks']['items'] as $track){
            $this->createTrack($track,$releaseId);
        }

        return $release;
    }

    private function createTrack($track,$releaseId){
        return Track::create([
                'spotify_id'    =>  $track['id'],
                'name'  =>  $track['name'],
                'duration_ms'   =>  $track['duration_ms'],
                'release_id'    =>  $releaseId,
                'track_number'  =>  $track['track_number']
            ]
        );
    }

}
