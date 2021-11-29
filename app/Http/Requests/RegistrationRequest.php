<?php

namespace App\Http\Requests;

use App\Rules\LoginIsUnique;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
    public static function rules()
    {
        return [
            'name' => ['required', 'regex:/^[-a-zA-Z_0-9]{5,30}$/', new LoginIsUnique],
            'password' => ['required', 'regex:/(?=.*[0-9])(?=.*[!@#$%^&*.,])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&*.,]{10,30}/']
        ];
    }
}
