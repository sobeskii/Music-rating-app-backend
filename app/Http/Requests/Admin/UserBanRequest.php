<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserBanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'exists:App\Models\User,id'
        ];
    }
}
