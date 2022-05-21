<?php

namespace App\Http\Requests\Rule;


use Illuminate\Foundation\Http\FormRequest;

class RulePostRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3',
            'reason' => 'required|string',
            'active'    => 'boolean',
            'detect_after_count' => 'integer|nullable',
            'mute_minutes' =>  'integer|nullable',
        ];
    }
}
