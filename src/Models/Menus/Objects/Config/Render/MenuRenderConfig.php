<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Menus\Objects\Config\Render;

use ArtisanLabs\GModel\GenericModel;

class MenuRenderConfig extends GenericModel
{

    protected $fillable = [
        'title',
        'slug',
        'default',

        //region Menus & Sub=menus
        'ul',          //Todos os menus
        'menu',             //Menu
        'submenu',          //Submenus
        //endregion

        //region Itens do Menu
        'li',             //Todos os itens (incluindo subitens)
        'top_item',      //Item top-level
        'child_item',     //Sub-itens
        'parent_item',      //Itens que contem sub-itens
        'parent_top_item',      //Item top-level que contém sub-itens
        'parent_child_item',      //Sub-itens que contém sub-itens

        //endregion

        //region Links do Menu
        'a',             //Links de Todos os itens do menu (incluindo subitens)
        'top_link',      //Links dos Itens pai
        'child_link',      //Links dos Itens pai com submenu
        'parent_link',      //Links dos Itens pai
        'parent_top_link',      //Links dos Itens pai
        'parent_child_link',      //Links dos Itens pai
        //endregion

        //todo: active_item, active_link, active_submenu?

    ];

    protected $casts = [
        'title'   => 'string',
        'slug'    => 'string',
        'default' => 'boolean',

        'ul'      => MenuRenderObject::class,
        'menu'    => MenuRenderObject::class,
        'submenu' => MenuRenderObject::class,

        'li'              => MenuRenderObject::class,
        'top_item'        => MenuRenderObject::class,
        'child_item'      => MenuRenderObject::class,
        'parent_item'     => MenuRenderObject::class,
        'parent_top_item' => MenuRenderObject::class,
        'parent_child_item' => MenuRenderObject::class,

        'a'               => MenuRenderObject::class,
        'top_link'        => MenuRenderObject::class,
        'child_link'      => MenuRenderObject::class,
        'parent_link'     => MenuRenderObject::class,
        'parent_top_link' => MenuRenderObject::class,
        'parent_child_link' => MenuRenderObject::class,
    ];

    protected $attributes = [
        'default' => false,

        'ul'      => [],
        'menu'    => [],
        'submenu' => [],

        'li'              => [],
        'top_item'        => [],
        'child_item'      => [],
        'parent_item'     => [],
        'parent_top_item' => [],
        'parent_child_item' => [],

        'a'               => [],
        'top_link'        => [],
        'child_link'      => [],
        'parent_link'     => [],
        'parent_top_link' => [],
        'parent_child_link' => [],
    ];

    public static function makeDefault($attributes = [])
    {
        return self::make([
                              'title'   => 'Exibição Padrão',
                              'slug'    => 'padrao',
                              'default' => true,
                              ...$attributes,
                          ]);
    }
}
