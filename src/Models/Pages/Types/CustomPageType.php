<?php

namespace Adminx\Common\Models\Pages\Types;

use Adminx\Common\Models\Pages\Types\Abstract\AbstractPageType;

class CustomPageType extends AbstractPageType
{
    protected $attributes = [
        'slug'        => 'CustomPage',

        'title'       => 'Página Personalizada',
        'description' => 'Página com conteúdo HTML personalizado.',

        'allowed_modules' => ['forms', 'widgets', 'lists', 'advanced_html'],
        'enabled_modules' => ['widgets'],
    ];
}