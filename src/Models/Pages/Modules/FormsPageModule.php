<?php

namespace Adminx\Common\Models\Pages\Modules;

use Adminx\Common\Models\Article;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Form;
use Adminx\Common\Models\Pages\Modules\Abstract\AbstractPageModule;
use Adminx\Common\Models\Pages\Types\Abstract\AbstractPageType;

class FormsPageModule extends AbstractPageModule
{

    public string $moduleRelatedModel = Form::class;

    protected $attributes = [
        'title'       => 'Formulários',
        'description' => 'Módulo de Formulários',
        'slug'        => 'forms',
    ];


}