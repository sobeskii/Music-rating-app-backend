<?php

namespace Database\Seeders;

use App\Models\Rating;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 300; $i++){
            $rating = Rating::factory()->makeOne();
            if(!Rating::where( [['release_id', '=', $rating->release_id],
                ['user_id', '=', $rating->user_id],
                ['review','!=',null]])->exists()){
                $rating->save();
            }
        }

    }
}
