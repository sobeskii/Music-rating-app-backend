<?php

namespace Database\Seeders;

use Aerni\Spotify\Facades\SpotifyFacade as Spotify;
use Illuminate\Database\Seeder;
use App\Models\Artist;
use App\Models\Release;
use App\Models\Track;
use Illuminate\Support\Str;

class ArtistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 4; $i++) {
            $random = Str::random(1);

            $apiArtist = Spotify::searchItems($random, 'artist')->get()['artists']['items'][0];
            $artistsAlbums = Spotify::artistAlbums($apiArtist['id'])->includeGroups('album,single')
                ->get()['items'];

            $artist = Artist::where('spotify_id', '=', $apiArtist['id'])->first();

            if ($artist == null) {
                $artist = Artist::create([
                    'spotify_id' => $apiArtist['id'],
                    'artist_image' => $apiArtist['images'][0]['url'],
                    'followers' => $apiArtist['followers']['total'],
                    'name' => $apiArtist['name'],
                ]);
            }
            foreach ($artistsAlbums as $album) {
                $release = Release::where('spotify_id', '=', $album['id'])->first();
                if ($release == null) {
                    $newAlbum = Spotify::album($album['id'])->get();
                    Release::create(
                        ['spotify_id' => $album['id'],
                        'name' => $newAlbum['name'],
                        'release_date' => $newAlbum['release_date'],
                        'release_image' => $newAlbum['images'][0]['url'],
                        'label' => $newAlbum['label'],
                        'album_type' => $newAlbum['album_type'],
                        'artist_id' => $apiArtist['id']
                        ]);

                    foreach ($newAlbum['tracks']['items'] as $track) {
                        Track::create([
                                'spotify_id' => $track['id'],
                                'name' => $track['name'],
                                'duration_ms' => $track['duration_ms'],
                                'release_id' => $album['id'],
                                'track_number' => $track['track_number']
                            ]);

                    }
                }
            }
        }
    }
}
