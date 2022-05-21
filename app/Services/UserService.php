<?php

namespace App\Services;

use Aerni\Spotify\Facades\SpotifyFacade as Spotify;
use Aerni\Spotify\Facades\SpotifySeedFacade as SpotifySeed;
use App\Models\Artist;
use App\Models\UserTopArtist;
use App\Spotify\SpotifyUser;
use App\Models\User;
class UserService
{
    /**
     * @var SpotifyUser
     */
    private $spotifyUser;
    /**
     * @var ArtistService
     */
    private $artistService;

    public function __construct(ArtistService $artistService)
    {
        $this->spotifyUser = new SpotifyUser();
        $this->artistService = $artistService;
    }

    public function handleUserTopArtists(User $user)
    {
        $result = $this->spotifyUser->topItems('artists')->get(null, $user->spotify_token);

        $user->topArtists()->delete();

        foreach ($result['items'] as $key => $item) {
            $artist = Artist::where('spotify_id','=',$item['id'])->first();

            if($artist == null){
                $artist = Artist::create([
                    'spotify_id'    =>  $item['id'],
                    'name'  =>  $item['name'],
                    'followers' =>  $item['followers']['total'],
                    'artist_image'  =>  $item['images'][0]['url']
                ]);
            }

            $topArtist = new UserTopArtist([
                'artist_id' => $artist->spotify_id,
                'position' => $key,
            ]);
            $user->topArtists()->save($topArtist);
        }
    }

    /**
     * @param User $user
     * @return array
     */
    public function handleRecommendedArtists(User $user): array
    {
        if(count($user->topArtists) <= 0) {
            return [];
        }
        $randomArtists = $user->topArtists->random(5)->pluck('artist_id');

        $seed = SpotifySeed::addArtists(implode(',', $randomArtists->toArray()));

        $results = collect(Spotify::recommendations($seed)->get()['tracks'])->random(10);
        $artists = [];

        foreach ($results as $result) {
            $artists[] = Spotify::artist($result['artists'][0]['id'])->get();
        }

        return $artists;
    }

}
