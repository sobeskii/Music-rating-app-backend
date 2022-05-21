<?php

namespace App\Repositories;

use App\Interfaces\RuleRepositoryInterface;
use App\Models\ModerationRule;

class RuleRepository implements RuleRepositoryInterface
{
    public function getRules()
    {
        return ModerationRule::paginate();
    }
}
