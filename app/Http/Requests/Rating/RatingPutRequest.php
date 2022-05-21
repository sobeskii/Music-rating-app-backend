<?php

namespace App\Http\Requests\Rating;

use ApiRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Rules\ArtistExists;
use App\Rules\ReleaseExists;
use App\Rules\UserMuted;
use App\Rules\UserPostsTooOften;
use App\Strategies\ReviewModeration\UserPostsOften;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class RatingPutRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        $this->checkIfMuted();
    }

    public function rules(): array
    {
        return [
            'review' => 'nullable|string|max:10000|min:5',
            'release_id' => ['required', 'string', new ReleaseExists],
            'artist_id' => ['required', 'string', new ArtistExists],
            'user_id' => ['required','integer','exists:users,id', new UserMuted($this->user()), new UserPostsTooOften($this->user())],
            'rating' => 'required|numeric|between:0.5,5'
        ];
    }

    /**
     * @return void
     */
    private function checkIfMuted() : void
    {
        $user = $this->user();

        if($user != null){
            if($user->muted_until != null) {
                if(Carbon::now() > Carbon::parse($user->muted_until)){
                    $user->muted = 0;
                    $user->muted_until = null;
                    $user->mute_reason = null;
                    $user->save();
                }
            }
        }
    }
}
