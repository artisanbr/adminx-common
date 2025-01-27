<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects\Config;

use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use ArtisanLabs\GModel\GenericModel;

class ThemeConfig extends GenericModel
{
    //use NonRecursiveGenericModel;

    protected $fillable = [

        'bundles_after',
        'bundle_stuffs',


        'breadcrumb',
        'plugins',
        'libs',
    ];

    protected $attributes = [

        //'plugins' => ['modernizr','magnific-popup','animations'],
        'breadcrumb' => [],
        //'bundle_stuffs' => true,
        //'libs' => [],
        //'bundles_after' => [],
    ];

    protected $casts = [
        'bundles_after' => 'collection',
        'bundle_stuffs' => 'boolean',



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
