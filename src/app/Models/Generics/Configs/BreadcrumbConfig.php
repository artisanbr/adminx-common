<?php
namespace ArtisanBR\Adminx\Common\App\Models\Generics\Configs;

use ArtisanBR\Adminx\Common\App\Enums\Themes\BreadcrumbSeparator;
use ArtisanBR\Adminx\Common\App\Models\Generics\Files\GenericFile;
use ArtisanLabs\GModel\GenericModel;

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
        'background' => GenericFile::class,
        'show_title' => 'bool',
        'css_class' => 'string',
        'default_items' => 'collection',
    ];
}
