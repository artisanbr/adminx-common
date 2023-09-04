<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

use Adminx\Common\Models\Pages\Modules\ArticlesPageModule;
use Adminx\Common\Models\Pages\Modules\CustomListsPageModule;
use Adminx\Common\Models\Pages\Modules\FormsPageModule;
use Adminx\Common\Models\Pages\Types\BlogPageType;
use Adminx\Common\Models\Pages\Types\CustomPageType;
use Adminx\Common\Models\Pages\Types\FormPageType;

return [

    'modules' => [
        'articles' => ArticlesPageModule::class,
        'forms'    => FormsPageModule::class,
        'lists'    => CustomListsPageModule::class,
    ],

    'types' => [
        'custom' => CustomPageType::class,
        'blog'   => BlogPageType::class,
        'form'   => FormPageType::class,
    ],

    /*'templates' => [
        'blog-simple' => BlogSimpleTemplate::class,
    ],*/

    'route-binds' => [
        'modules' => [
            'page_with_articles' => 'articles',
            //'page_with_lists'    => 'lists',
        ],
    ],
];
