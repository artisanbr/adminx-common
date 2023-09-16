<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Templates\Global\Pages;


use Adminx\Common\Models\Templates\Global\Abstract\AbstractTemplate;

class BlogRightSidebarTemplate extends AbstractTemplate
{

    protected $attributes = [
        'public_id'   => 'blog-right-sidebar',
        'title'       => 'Blog com Barra Lateral à Direita',
        'description' => 'Blog simples em duas colunas com gerenciamento de postagens, categorias, tags e comentários.',
        'morphs'      => ['page'],
        'path'        => 'pages',
        'config'      => [
            'required_modules' => ['articles'],
            'use_files'        => true,
        ],
    ];

}