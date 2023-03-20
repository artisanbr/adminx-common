<?php
namespace ArtisanBR\Adminx\Common\App\Models\Generics\Configs;

use ArtisanBR\Adminx\Common\App\Enums\Themes\ThemeFramework;
use ArtisanLabs\GModel\GenericModel;

class ThemeConfig extends GenericModel
{

    protected $fillable = [
        'no_framework',
        'framework',
        'jquery',
        'breadcrumb',
        'plugins',
    ];

    protected $attributes = [
        'no_framework' => false,
        'framework' => 'boostrap:5',
        'jquery' => true,
        'plugins' => ['modernizr','magnific-popup','animations'],
        'breadcrumb' => [],
    ];

    protected $casts = [
        'no_framework' => 'bool',
        'framework' => ThemeFramework::class,
        'jquery' => 'bool',
        'plugins' => 'collection',
        'breadcrumb' => BreadcrumbConfig::class,
    ];

    /*protected function getHasCustomPermissionsAttribute(){
        return $this->custom_permissions;
    }*/
}
