<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('passport:install');
        $this->call([
            RoleSeeder::class,
            RuleSeeder::class,
            ArtistSeeder::class,
            UserSeeder::class,
            RatingSeeder::class,
            LikedRatingSeeder::class,
        ]);
    }
}
