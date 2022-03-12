<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class GreaterThanIfPresent implements Rule
{

    public function __construct(protected $compareField)
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $compareValue = request($this->compareField);

        if (!$compareValue) {
            return true;
        }

        return $value > $compareValue;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
