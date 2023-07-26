<?php

namespace Adminx\Common\Models\Templates\Global;


use Adminx\Common\Models\Templates\Global\Abstract\AbstractPageTemplate;

class BlogSimplePageTemplate extends AbstractPageTemplate
{

    protected $attributes = [
        'public_id'   => 'blog-simple',
        'title'       => 'Blog Simples',
        'description' => 'Blog simples em duas colunas com gerenciamento de postagens, categorias, tags e comentários.',
        'morphs'      => ['page'],
        'path'      => 'pages',
    ];

}