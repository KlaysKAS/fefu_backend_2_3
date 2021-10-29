<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  string  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return
            strcasecmp($value, '7 (000) 00-00-000') >= 0 &&
            strcasecmp($value, '8 (999) 99-99-999') <= 0  ||
            strcasecmp($value, '+7 (000) 00-00-000') >= 0 &&
            strcasecmp($value, '+8 (999) 99-99-999') <= 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The phone is invalid.';
    }
}
