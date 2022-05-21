<?php

namespace App\Spotify;

class SpotifyUser
{
    protected $defaultConfig;

    public function __construct()
    {
        $this->defaultConfig = [
            'country' => config('spotify.default_config.country'),
            'locale' => config('spotify.default_config.locale'),
            'market' => config('spotify.default_config.market'),
        ];
    }
    /**
     * Get Spotify catalog information for a single album.
     *
     * @param string $type
     * @return PendingRequest
     */
    public function topItems(string $type): PendingRequest
    {
        $endpoint = '/top/' . $type;

        $acceptedParams = [];

        return new PendingRequest($endpoint, $acceptedParams);
    }

}
