<?php

namespace App\Rules;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class UserPostsTooOften implements Rule
{
    /**
     * @var User
     */
    private $user;

    /**
     * Create a new rule instance.
     *
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($this->user != null) {
            $review = $this->user->getLatestRating();
            if ($review != null) {
                if($review->review != null) {
                    if (Carbon::parse($review->created_at)->addSeconds(30) > Carbon::now()) {
                        //return false;
                    } else return true;
                } else return true;
            }
        } return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You are posting too often!';
    }

}
