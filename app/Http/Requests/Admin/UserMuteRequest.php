<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserMuteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => 'exists:App\Models\User,id',
            'time'  => 'required|integer',
            'mute_reason' => 'required|string'
        ];
    }
}
