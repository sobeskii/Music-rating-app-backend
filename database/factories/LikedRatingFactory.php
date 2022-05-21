<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikedRatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'   =>  User::all()->random()->id,
            'rating_id' =>  Rating::all()->random()->id,
            'is_like' => $this->faker->boolean
        ];
    }
}
