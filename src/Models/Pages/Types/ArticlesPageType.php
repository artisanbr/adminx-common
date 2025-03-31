<?php
/*
 * Copyright (c) 2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages\Types;

use Adminx\Common\Models\Pages\Types\Abstract\AbstractPageType;

class ArticlesPageType extends AbstractPageType
{
    protected $attributes = [
        'slug'        => 'Articles',

        'title'       => 'Página de Artigos',
        'description' => 'Blog, notícias e artigos diversos com gerenciamento de postagens, categorias, tags, comentários e funcionalidades de SEO.',

        'allowed_modules' => ['forms', 'articles', 'widgets'],
        'enabled_modules' => ['articles','widgets'],
    ];


}