<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteUserProfileRequest extends FormRequest
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
            'country' => 'string|min:2|max:50|regex:/[A-zÀ-úä-ü ,()-]/',
            'native_language' => 'string|email|min:2|max:20|regex:/[A-zÀ-úä-ü ,()-]/',
            'studying_language' => 'string|min:7|max:7|regex:/[A-z]/',
        ];
    }
}
