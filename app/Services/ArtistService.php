<?php

namespace App\Services;

use Aerni\Spotify\Facades\SpotifyFacade as Spotify;
use App\Interfaces\RatingRepositoryInterface;
use App\Models\Artist;

class ArtistService
{
    /**
     * @var RatingRepositoryInterface
     */
    private $ratingRepository;

    public function __construct(RatingRepositoryInterface $ratingRepository)
    {
        $this->ratingRepository = $ratingRepository;
    }

    public function handleShowArtist(string $artistId): array
    {
        $artist = Artist::where('spotify_id','=',$artistId)->first();

        if ($artist == null) {
            $artist = $this->createArtist($artistId);
        }

        $artistAlbums = $this->getArtistReleases($artistId);

        return [
            'artist' => $artist,
            'artist_releases' => $artistAlbums,
        ];
    }

    private function getArtistReleases($artistId)
    {
        // Get initial data of releases (total)
        $releases = Spotify::artistAlbums($artistId)->includeGroups('album,single')
            ->limit(50)
            ->get();

        $all_releases = [];
        $all_releases = array_merge($all_releases, $releases['items']);

        // Get the rest of release data
        // Put all release data into one array

        for ($i = 50; $i < $releases['total']; $i += 50) {
            $items = Spotify::artistAlbums($artistId)->includeGroups('album,single')->limit(50)->offset($i)->get();

            $items = $items['items'];
            $all_releases = array_merge($all_releases, $items);
        }

        return collect($all_releases)->unique('name')->map(function ($value) {
            $value['rating_average'] = $this->ratingRepository->getRatingAverage($value['id']);
            $value['rating_count'] = $this->ratingRepository->getRatingCount($value['id']);
            return $value;
        })->values();
    }

    public function createArtist(string $artistId){
        $apiArtist = Spotify::artist($artistId)->get();

        return Artist::create([
            'spotify_id'    =>  $apiArtist['id'],
            'name'  =>  $apiArtist['name'],
            'followers' =>  $apiArtist['followers']['total'],
            'artist_image'  =>  $apiArtist['images'][0]['url']
        ]);
        return $artist;
    }
}
