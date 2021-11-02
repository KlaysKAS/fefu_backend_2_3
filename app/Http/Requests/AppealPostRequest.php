<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Rules\AgeRule;
use App\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppealPostRequest extends FormRequest
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
            'surname' => ['required', 'string', 'max:40'],
            'name' => ['required', 'string', 'max:20'],
            'patronymic' => ['nullable', 'string', 'max:20'],
            'age' => ['required', 'integer', 'between:14,123'],
            'gender' => ['required', Rule::in([Gender::MALE, Gender::FEMALE])],
            'phone' => ['nullable', 'string', 'regex:/^[+]{0,1}[7-8][ ]?[(]([0-9]{3})[)][ ]?([0-9]{2})[-]([0-9]{2})[-]([0-9]{3})$/'],
            'email' => ['nullable', 'required_without:phone', 'string', 'max:100',
                'regex:/^[a-zA-Z0-9а-яА-Я]+([-+.\']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/i'],
            'message' => ['required', 'string', 'max:100'],
        ];
    }
}
