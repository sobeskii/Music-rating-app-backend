<?php

namespace App\Providers;

use App\Resolvers\ReviewFlagResolver;
use App\Strategies\AbstractReviewModeration;
use App\Strategies\ReviewModeration\RegexRuleStrategy;
use App\Strategies\ReviewModeration\RepeatingLetterStrategy;
use App\Strategies\ReviewModeration\SpamClassifierStrategy;
use Illuminate\Support\ServiceProvider;
use App\Strategies\ReviewModeration\UserPostsOften;
class ModerationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->tag([RegexRuleStrategy::class,SpamClassifierStrategy::class], 'moderators');

        $this->app->bind(ReviewFlagResolver::class, function() {
            return new ReviewFlagResolver(...$this->app->tagged('moderators'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
