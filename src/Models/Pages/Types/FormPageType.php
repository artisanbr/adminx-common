<?php

namespace Adminx\Common\Models\Pages\Types;

use Adminx\Common\Models\Pages\Types\Abstract\AbstractPageType;

class FormPageType extends AbstractPageType
{
    protected $attributes = [
        'slug'        => 'Forms',

        'title'       => 'Formulários & Contato',
        'description' => 'Formulários personalizados, de contato, captação de Leads. Com localização e informações de contato.',

        'allowed_modules' => ['forms', 'lists', 'widgets'],
        'enabled_modules' => ['forms','widgets'],
    ];
}