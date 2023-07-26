<?php

namespace Adminx\Common\Models\Pages\Modules;

use Adminx\Common\Models\Article;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Pages\Modules\Abstract\AbstractPageModule;
use Adminx\Common\Models\Pages\Types\Abstract\AbstractPageType;

class ArticlesPageModule extends AbstractPageModule
{

    public string $moduleRelatedModel = Article::class;

    protected $attributes = [
        'title'       => 'Artigos',
        'description' => 'Módulo de Artigos',
        'slug'        => 'articles',
    ];


}