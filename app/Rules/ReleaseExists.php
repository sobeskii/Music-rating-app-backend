<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Aerni\Spotify\Facades\SpotifyFacade as Spotify;

class ReleaseExists implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        try {
            Spotify::album($value)->get();
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Release ID is not valid please try again.';
    }
}
