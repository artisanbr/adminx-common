<?php

namespace Adminx\Common\Models\Pages\Types;

use Adminx\Common\Models\Pages\Types\Abstract\AbstractPageType;

class BlogPageType extends AbstractPageType
{
    protected $attributes = [
        'slug'        => 'Blog',

        'title'       => 'Blog',
        'description' => 'Blog diversos com gerenciamento de postagens, categorias, tags, comentários e funcionalidades de SEO.',

        'allowed_modules' => ['forms', 'articles', 'widgets'],
        'enabled_modules' => ['articles','widgets'],
    ];


}