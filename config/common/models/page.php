<?php

use Adminx\Common\Models\Pages\Modules\ArticlesPageModule;
use Adminx\Common\Models\Pages\Modules\CustomListsPageModule;
use Adminx\Common\Models\Pages\Modules\FormsPageModule;
use Adminx\Common\Models\Templates\Global\BlogSimplePageTemplate;
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

    'templates' => [
        'blog-simple' => BlogSimplePageTemplate::class,
    ],

    'route-binds' => [
        'modules' => [
            'page_with_articles' => 'articles',
            //'page_with_lists'    => 'lists',
        ],
    ],
];