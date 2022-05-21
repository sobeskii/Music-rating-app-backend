<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge(['provider' => $this->route('provider')]);
    }
    public function rules()
    {
        return [
            'id' => [
                'required',
            ]
        ];
    }
}
