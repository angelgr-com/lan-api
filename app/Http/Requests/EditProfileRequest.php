<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|min:2|max:64',
            'email' => 'string|email|min:8|max:64',
            'password' => 'string|min:8|max:32|',
            'newPassword' => 'string|min:8|max:32|',
            'confirmNewPassword' => 'string|min:8|max:32|',
            'username' => 'string|min:2|max:32|',
            'steamUsername' => 'string|min:2|max:32|',
            'role' => 'string|min:4|max:5|',
        ];
    }
}
