<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Menus\Objects;

use ArtisanLabs\GModel\GenericModel;

class MenuRenderConfig extends GenericModel
{

    protected $fillable = [
        'menu',             //Menu
        'item',             //Todos os itens do menu (incluindo subitens)
        'item_link',             //Links de Todos os itens do menu (incluindo subitens)

        'parent_item',      //Itens pai
        'parent_item_link',      //Links dos Itens pai

        'item_submenu',     //Itens que contenham submenus
        'item_submenu_link',     //Links de Itens que contenham submenus

        'submenu',          //Submenus
        'submenu_item',     //Sub-itens de submenu
        'submenu_item_link',     //Links dos Sub-itens de submenu
    ];

    protected $casts = [
        'menu'         => MenuRenderObject::class,
        
        'item'         => MenuRenderObject::class,
        'item_link'         => MenuRenderObject::class,

        'parent_item'  => MenuRenderObject::class,
        'parent_item_link'  => MenuRenderObject::class,

        'item_submenu' => MenuRenderObject::class,
        'item_submenu_link' => MenuRenderObject::class,

        'submenu'      => MenuRenderObject::class,
        'submenu_item' => MenuRenderObject::class,
        'submenu_item_link' => MenuRenderObject::class,
    ];
}