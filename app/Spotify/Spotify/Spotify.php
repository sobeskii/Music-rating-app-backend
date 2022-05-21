<?php

namespace App\Spotify\Spotify;

use Aerni\Spotify\PendingRequest;
use Aerni\Spotify\Spotify as SpotifyRoutes;
use Aerni\Spotify\Validator;

class Spotify extends SpotifyRoutes
{
    public function searchItems(string $query, $type): PendingRequest
    {
        $endpoint = '/search/';

        $acceptedParams = [
            'q' => [$query,'upc'],
            'type' => Validator::validateArgument('type', $type),
            'market' => $this->defaultConfig['market'],
            'limit' => null,
            'offset' => null,
            'include_external' => null,
        ];

        return new PendingRequest($endpoint, $acceptedParams);
    }
}
