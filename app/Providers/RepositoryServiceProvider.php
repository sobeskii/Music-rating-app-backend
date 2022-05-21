<?php

namespace App\Providers;

use App\Interfaces\FlaggedReviewReasonRepositoryInterface;
use App\Interfaces\RatingRepositoryInterface;
use App\Interfaces\RuleRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\RatingRepository;
use App\Repositories\RuleRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RatingRepositoryInterface::class, RatingRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RuleRepositoryInterface::class,RuleRepository::class);
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
