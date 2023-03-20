<?php

namespace ArtisanBR\Adminx\Common\App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DomainRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    protected $has_protocol = false;

    public function __construct()
    {
        //
    }

    public function __toString(){
        return 'Domain';
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
        return preg_match('/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Este não é um domínio válido.';
    }
}
