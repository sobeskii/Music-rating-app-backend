<?php

namespace Database\Factories;

use App\Models\Release;
use App\Models\ReleaseType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class ReleaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Release::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'spotify_id'    =>  Str::random(32),
            'name' => $this->faker->catchPhrase,
            'release_date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'release_image' => Str::random(24).'.jpg',
            'label' => $this->faker->title,
            'album_type'    => 'single',
            'artist_id'=> Str::random(32),
          ];
    }
}
