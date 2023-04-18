<?php
namespace Adminx\Common\Models\Generics\Configs;

use ArtisanLabs\GModel\GenericModel;

class MenuConfig extends GenericModel
{

    protected $fillable = [
        'menu_class',
        'menu_item_class',
        'menu_item_submenu_class',
        'submenu_class',
        'submenu_item_class',
        'menu_item_append',
        'menu_item_prepend',
    ];

    protected $attributes = [
        'menu_class' => '',
        'menu_item_class' => '',
        'menu_item_submenu_class' => '',
        'submenu_class' => '',
        'submenu_item_class' => '',
        'menu_item_append' => '',
        'menu_item_prepend' => '',
    ];

    protected $casts = [
        'menu_class' => 'string',
        'menu_item_class' => 'string',
        'menu_item_submenu_class' => 'string',
        'submenu_class' => 'string',
        'submenu_item_class' => 'string',
        'menu_item_append' => 'string',
        'menu_item_prepend' => 'string',
    ];
}
