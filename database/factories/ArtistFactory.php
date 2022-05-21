<?php

namespace Database\Factories;

use App\Models\Artist;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
class ArtistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Artist::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'spotify_id'    =>  Str::random(32),
            'artist_image' => Str::random(24).'.jpg',
            'followers'=> $this->faker->numberBetween(3,50000000),
            'name' => $this->faker->catchPhrase,
        ];
    }
}
