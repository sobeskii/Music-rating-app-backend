<?php

namespace Database\Seeders;

use App\Models\LikedRating;
use App\Models\Rating;
use Illuminate\Database\Seeder;

class LikedRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 50; $i++){
            $like = LikedRating::factory()->makeOne();
            if(!LikedRating::where( [['user_id', '=', $like->user_id],
                ['rating_id', '=', $like->rating_id]])->exists()){
                $like->save();
            }
        }
    }
}
