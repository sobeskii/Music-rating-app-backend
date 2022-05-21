<?php

namespace Database\Factories;

use App\Models\Release;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ratings = [ 0.5,1,1.5,2,2.5,3,3.5,4,4.5,5];

        $hasReview = $this->faker->boolean;

        $randomRelease = Release::all()->random();
        return [
            'user_id'   =>  User::all()->random()->id,
            'release_id'=> $randomRelease->spotify_id,
            'artist_id' => $randomRelease->artist->spotify_id,
            'rating'    => Arr::random($ratings),
            'review'    =>  ($hasReview)    ?    $this->faker->realText(900)    :    null,
        ];
    }
}
