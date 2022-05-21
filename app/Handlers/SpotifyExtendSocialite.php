<?php

namespace App\Handlers;

use App\Providers\SpotifyProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class SpotifyExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('spotify', SpotifyProvider::class);
    }
}
