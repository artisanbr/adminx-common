<?php
namespace Adminx\Common\Models\CustomLists\Generic\Configs;

use ArtisanLabs\GModel\GenericModel;

class CustomListConfig extends GenericModel
{

    protected $fillable = [
        'menu_class',
        'menu_item_class',
        'menu_item_append',
        'menu_item_prepend',
    ];

    protected $attributes = [
    ];

    protected $casts = [
        'menu_class' => 'string',
        'menu_item_class' => 'string',
        'menu_item_append' => 'string',
        'menu_item_prepend' => 'string',
    ];
}
