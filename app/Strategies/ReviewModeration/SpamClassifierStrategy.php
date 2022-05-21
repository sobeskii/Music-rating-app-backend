<?php

namespace App\Strategies\ReviewModeration;

use App\Models\FlaggedReviewReason;
use App\Models\ModerationRule;
use App\Models\Rating;
use App\Models\User;
use App\Services\AdminService;
use App\Strategies\ReviewModerationInterface;
use TeamTNT\TNTSearch\Classifier\TNTClassifier;

class SpamClassifierStrategy implements ReviewModerationInterface
{
    /**
     * @var AdminService
     */
    private $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function moderate(Rating $review)
    {
        $classifier_rule = ModerationRule::where('strategy', '=', 'classifier')->first();

        if ($classifier_rule != null) {
            if ($classifier_rule->active) {
                $classifier = new TNTClassifier();
                $classifier->load(base_path() . '\classifiers\spam.cls');

                $guess = $classifier->predict($review->review);

                if ($guess['label'] == 1) {
                    $flaggedReview = new FlaggedReviewReason(['rating_id' => $review->id,]);
                    $flaggedReview->moderation_rule()->associate($classifier_rule);
                    $flaggedReview->save();

                    if ($classifier_rule->mute_minutes != null) {
                        $this->mute_user($classifier_rule, $review->user);
                    }
                }
            }
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
