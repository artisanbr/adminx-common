<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Menus\Objects;

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


        'render', //Opções de renderização
    ];

    protected $attributes = [
        /*'menu_class' => '',
        'menu_item_class' => '',
        'menu_item_submenu_class' => '',
        'submenu_class' => '',
        'submenu_item_class' => '',
        'menu_item_append' => '',
        'menu_item_prepend' => '',*/
    ];

    protected $casts = [
       /* 'menu_class'              => 'string',
        'menu_item_class'         => 'string',
        'menu_item_submenu_class' => 'string',
        'submenu_class'           => 'string',
        'submenu_item_class'      => 'string',*/
        'menu_item_append'        => 'string',
        'menu_item_prepend'       => 'string',

        'render'                  => MenuRenderConfig::class,
    ];

    public function setAttribute($key, $value)
    {

        switch ($key){
            case 'menu_class':
                $this->render->menu->class = $value;
                break;
            case 'menu_item_class':
                $this->render->item->class = $value;
                break;
            case 'menu_item_submenu_class':
                $this->render->item_submenu->class = $value;
                break;
            case 'submenu_class':
                $this->render->submenu->class = $value;
                break;
            case 'submenu_item_class':
                $this->render->submenu_item->class = $value;
                break;
            default:
                parent::setAttribute($key, $value);
                break;

        }

        //dd($this->render);

        return $this;
    }
}
