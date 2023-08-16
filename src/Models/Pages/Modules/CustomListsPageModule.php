<?php

namespace Adminx\Common\Models\Pages\Modules;

use Adminx\Common\Models\Article;
use Adminx\Common\Models\CustomLists\Abstract\CustomListBase;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\Pages\Modules\Abstract\AbstractPageModule;
use Adminx\Common\Models\Pages\Types\Abstract\AbstractPageType;

class CustomListsPageModule extends AbstractPageModule
{

    public string $moduleRelatedModel = CustomList::class;

    protected $attributes = [
        'title'       => 'Listas Personalizadas',
        'description' => 'Módulo de Formulários',
        'slug'        => 'lists',
    ];


}