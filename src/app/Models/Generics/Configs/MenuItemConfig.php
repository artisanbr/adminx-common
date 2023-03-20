<?php
namespace ArtisanBR\Adminx\Common\App\Models\Generics\Configs;

use ArtisanBR\Adminx\Common\App\Models\Generics\DataSource;
use ArtisanLabs\GModel\GenericModel;

class MenuItemConfig extends GenericModel
{

    protected $fillable = [
        'submenu_source',
        'is_source_submenu',
    ];

    protected $attributes = [
        'is_source_submenu' => 0,
    ];

    protected $casts = [
        'is_source_submenu' => 'bool',
        'submenu_source' => DataSource::class
    ];

}
