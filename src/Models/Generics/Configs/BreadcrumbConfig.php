<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Configs;

use Adminx\Common\Enums\Themes\BreadcrumbSeparator;
use Adminx\Common\Models\Objects\BreadcrumbBackgroundObject;
use ArtisanBR\GenericModel\GenericModel;

class BreadcrumbConfig extends GenericModel
{

    protected static $nullable = true;

    protected $fillable = [
        'enable',
        'separator',
        'height',
        'background',
        'show_title',
        'show_navigation',
        'css_class',
        'default_items',
    ];

    protected $attributes = [
        'enable' => true,
        'separator' => '/',
        'height' => 250,
        'show_title' => true,
        'show_navigation' => true,
        'css_class' => '',
    ];

    protected $casts = [
        'enable' => 'bool',
        'separator' => BreadcrumbSeparator::class,
        'height' => 'int',
        'background' => BreadcrumbBackgroundObject::class,
        'show_title' => 'bool',
        'css_class' => 'string',
        'default_items' => 'collection',
    ];


    protected function setBackgroundUrlAttribute($value)
    {
        $this->background->url = $value;

        return $this;
    }

    /*protected function getBackgroundUrlAttribute()
    {
        return $this->background->url ?? null;
    }*/
}
