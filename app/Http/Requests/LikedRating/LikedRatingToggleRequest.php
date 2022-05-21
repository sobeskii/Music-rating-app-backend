<?php

namespace App\Http\Requests\LikedRating;

use Illuminate\Foundation\Http\FormRequest;

class LikedRatingToggleRequest  extends FormRequest
{
    public function rules(): array
    {
        return [
            'is_like' => 'required|boolean',
        ];
    }
}
