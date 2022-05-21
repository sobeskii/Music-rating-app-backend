<?php

namespace App\Strategies\ReviewModeration;

use App\Models\FlaggedReviewReason;
use App\Models\ModerationRule;
use App\Models\Rating;
use App\Services\AdminService;
use App\Strategies\ReviewModerationInterface;
use App\Models\User;

class RegexRuleStrategy implements ReviewModerationInterface
{
    private const REGEX_RULES = [
        'repeating_letter'  => '([a-zA-Z0-9])\1{4,}',
    ];

    private const STRATEGY_CODE = 'regex';

    /**
     * @var AdminService
     */
    private $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * @param Rating $review
     * @return void
     */
    public function moderate(Rating $review)
    {
        $rules = ModerationRule::where([
            ['active', '=', true],
            ['strategy', '=', self::STRATEGY_CODE]
        ])->get();

        foreach ($rules as $rule) {
            $matches = [];

            $appliesToRule = preg_match_all("/" . self::REGEX_RULES[$rule->code] . "/", $review->review, $matches);

            if ($appliesToRule == false) {
                continue;
            }
            $this->create_reason($matches, $review, $rule);

            if ($rule->detect_after_count < $appliesToRule && $rule->detect_after_count != null) {
                $this->mute_user($rule, $review->user);
            }
        }
    }

    /**
     * @param array $matches
     * @param Rating $review
     * @param ModerationRule $rule
     * @return void
     */
    private function create_reason(array $matches, Rating $review, ModerationRule $rule)
    {
        $flaggedParts = $matches[0];

        foreach ($flaggedParts as $part) {
            $flaggedReview = new FlaggedReviewReason(['rating_id' => $review->id,
                'flagged_part' => $part,
            ]);
            $flaggedReview->moderation_rule()->associate($rule);
            $flaggedReview->save();
        }
    }

    /**
     * @param ModerationRule $rule
     * @param User $user
     * @return void
     */
    private function mute_user(ModerationRule $rule, User $user)
    {
        $data = ['user_id' => $user->id, 'time' => $rule->mute_minutes,'mute_reason'=> $rule->reason];

        $this->adminService->handleMuteAction($data, true);
    }
}
