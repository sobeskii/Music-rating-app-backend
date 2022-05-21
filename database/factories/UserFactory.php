<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    private const DEFAULT_IMG = 'https://ui-avatars.com/api/?size=128?&length=1&background=random';

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;


    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $isMuted = $this->faker->boolean;

        $name = $this->faker->name();

        return [
            'name' => $name,
            'spotify_id' =>  Str::random(32),
            'spotify_token' =>  Str::random(32),
            'spotify_refresh_token' => Str::random(64),
            'spotify_avatar' => self::DEFAULT_IMG.'&'.'name='.$name,
            'muted'=> $isMuted,
            'role_id' => 1,
            'muted_until' => ($isMuted) ?   Carbon::now()->addMinutes($this->faker->numberBetween(2,3000))  :   null,
        ];
    }
}
