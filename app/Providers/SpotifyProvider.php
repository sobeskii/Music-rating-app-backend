<?php

namespace App\Providers;

use SocialiteProviders\Spotify\Provider;

class SpotifyProvider extends Provider
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [
        'user-top-read',
        'user-library-read'
    ];
}
