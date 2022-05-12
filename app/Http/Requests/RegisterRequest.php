<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|alpha|min:2|max:50',
            // 'last_name' => 'string|alpha|min:2|max:50',
            'profile_picture' => 'string|min:2|max:255',
            'username' => 'required|string|alpha_num|unique:users,username|min:2|max:50',
            'email' => 'required|string|unique:users,email|email|min:8|max:70',
            'password' => 'required|confirmed|string|min:8|max:32|',
            'is_admin' => 'boolean',
        ];
    }
}