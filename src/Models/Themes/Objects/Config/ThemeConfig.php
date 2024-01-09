<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects\Config;

use Adminx\Common\Enums\Themes\ThemeFramework;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use ArtisanLabs\GModel\GenericModel;

class ThemeConfig extends GenericModel
{

    protected $fillable = [
        //'no_framework',
        //'framework',

        'bootstrap_enable',
        'bootstrap_version',
        'bootstrap_strict',


        'jquery',

        'jquery_enable',
        'jquery_version',
        'jquery_ui_enable',
        'jquery_ui_version',
        'jquery_ui_strict',


        'breadcrumb',
        'plugins',
        'libs',
    ];

    protected $attributes = [
        //'no_framework' => false,
        //'framework' => 'bootstrap:5',

        'bootstrap_enable' => false,
        'bootstrap_strict' => false,

        'jquery_enable' => true,
        'jquery_version' => '3.6.3',
        'jquery_ui_enable' => true,
        'jquery_ui_version' => '1.13.2',

        'plugins' => ['modernizr','magnific-popup','animations'],
        'breadcrumb' => [],
        'libs' => [],
    ];

    protected $casts = [
        'no_framework' => 'bool',
        'framework' => ThemeFramework::class,

        'jquery_enable' => 'bool',
        'jquery_version' => 'string',
        'jquery_ui_enable' => 'bool',
        'jquery_ui_version' => 'string',

        'plugins' => 'collection',
        'breadcrumb' => BreadcrumbConfig::class,
        'libs' => ThemeConfigLibrariesObject::class,
    ];

    /*protected function setNoFrameworkAttribute($value): void
    {
        $this->attributes['bootstrap_enable'] = !$value;
    }

    protected function setFrameworkAttribute($value = null): void
    {
        if($value){
            $configVersion = ($value instanceof ThemeFramework) ? $value->value : $value;
            $this->attributes['bootstrap_version'] = config("adminx.themes.versions.{$configVersion}", config('adminx.themes.versions.bootstrap:5'));
        }

    }*/

    protected function setBootsrapVersionAttribute($value = null): void
    {
        $this->attributes['bootstrap_version'] = $value ?? config('adminx.themes.versions.bootstrap:5');
    }

    protected function setJqueryAttribute($value): void
    {
        $this->attributes['jquery_enable'] = $value;
    }
}
