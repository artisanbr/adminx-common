<?php

namespace ArtisanBR\Adminx\Common\App\Rules;

use Illuminate\Contracts\Validation\Rule;

class HtmlEmptyRule implements Rule
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

    public function __toString(){
        return 'HtmlEmpty';
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
        $text = strip_tags($value, "<img><a>");

        return !empty($text);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "O campo <b>:attribute</b> é obrigatório.";
    }
}
