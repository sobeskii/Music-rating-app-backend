<?php

namespace Database\Factories;

use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Str;

class TrackFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Track::class;

    private static $order = 1;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'track_number' => self::$order++,
            'name' => $this->faker->realText(rand(10,30), 1),
            'duration_ms' => $this->faker->numberBetween(80,8009000),
            'spotify_id'=>Str::random(32),
            'release_id'=>Str::random(32)
        ];
    }

}
