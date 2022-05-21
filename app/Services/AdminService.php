<?php

namespace App\Services;

use App\Models\ModerationRule;
use App\Models\Rating;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
class AdminService
{
    public function handleBanAction(array $data,bool $isBan)
    {
        $user = User::where('id', '=', $data['user_id'])->first();

        $banned = Role::where('role','=','banned')->first();
        $userRole = Role::where('role','=','user')->first();

        $user->update(['role_id'=> ($isBan)    ?   $banned->id    :   $userRole->id]);
        $user['role'] = $user->role;

        return $user;
    }

    public function handleMuteAction(array $data,bool $isMute)
    {
        $user = User::where('id', '=', $data['user_id'])->first();

        $user->update(['muted' => $isMute,
            'muted_until' =>  ($isMute) ? ($data['time'] ==  -1) ? null : Carbon::now()->addMinutes($data['time']) : null,
            'mute_reason' => $data['mute_reason']
        ]);
        $user['role'] = $user->role;

        return $user;
    }

    public function handleConfirmAction(array $data)
    {
        $rating = Rating::where('id','=',$data['rating_id'])->first();

        if($rating->user->muted){
            $rating->user->update(['muted_until'=> null,'muted' => false, 'mute_reason'=> null]);
        }

        $rating->flaggedReviewReasons()->delete();

        return $rating;
    }

    public function handleRuleCreate( array $data)
    {
        return ModerationRule::create([
            'name' => $data['name'],
            'rule_regex' => $data['rule_regex'],
            'mute_severity' => $data['mute_severity'],
            'mute_minutes' => $data['mute_minutes'],
        ]);
    }

    public function handleRuleEdit(ModerationRule $rule, array $data): ModerationRule
    {
        $rule->update($data);

        return $rule;
    }

    public function handleRuleDelete(ModerationRule $rule): ModerationRule
    {
        $rule->delete();

        return $rule;
    }
}
