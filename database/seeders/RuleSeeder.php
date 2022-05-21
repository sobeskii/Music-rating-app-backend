<?php

namespace Database\Seeders;

use App\Models\ModerationRule;
use Illuminate\Database\Seeder;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ModerationRule::create(['name' => 'Repeating letters',
            'active' => true,
            'strategy' => 'regex',
            'code' => 'repeating_letter',
            'reason' => 'Too many repeating letters!',
            'detect_after_count' => 5,
            'mute_minutes' => 30
        ]);
        ModerationRule::create(['name' => 'Spam classifier',
            'active' => true,
            'strategy' => 'classifier',
            'code' => 'classifier',
            'reason' => 'Your review resembles spam! Please change it!',
            'detect_after_count' => null,
            'mute_minutes' => 30
        ]);
    }
}
