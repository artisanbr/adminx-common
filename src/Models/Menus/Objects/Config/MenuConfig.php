<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Menus\Objects\Config;

use Adminx\Common\Models\Menus\Objects\Config\Render\MenuRenderRepository;
use Adminx\Common\Models\Menus\Objects\MenuRenderConfigOld;
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
        'renders', //Opções de renderização
    ];

    protected $attributes = [
        //'render'     => [],
        'renders' => [],
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
        'menu_item_append'  => 'string',
        'menu_item_prepend' => 'string',

        //'render'     => 'array', //para migrar
        'render'            => MenuRenderConfigOld::class, //antigo
        //'render' => MenuRenderConfig::class,
        'renders'           => MenuRenderRepository::class,
    ];

    protected $appends = [
        //'render',
    ];

    public function clearConfig()
    {
        //unset($this->attributes['render']);

        /*unset($this->attributes['menu_class']);
        unset($this->attributes['menu_item_class']);
        unset($this->attributes['menu_item_submenu_class']);
        unset($this->attributes['submenu_class']);
        unset($this->attributes['submenu_item_class']);
        unset($this->attributes['menu_item_append']);
        unset($this->attributes['menu_item_prepend']);*/
    }

    public function mount(): static
    {
        if (!($this->attributes['render'] ?? false)) {
            $this->attributes['render'] = [];
        }

        $currentRender = $this->render->toArray();

        $replaceValues = [];

        if (!empty($this->menu_class ?? null)) {
            $replaceValues['menu'] = ['class' => $this->menu_class];
        }

        if (!empty($this->menu_item_class ?? null)) {
            $replaceValues['item'] = ['class' => $this->menu_item_class];
        }

        if (!empty($this->menu_item_submenu_class ?? null)) {
            $replaceValues['item_submenu'] = ['class' => $this->menu_item_submenu_class];
        }

        if (!empty($this->submenu_class ?? null)) {
            $replaceValues['submenu'] = ['class' => $this->submenu_class];
        }

        if (!empty($this->submenu_item_class ?? null)) {
            $replaceValues['submenu_item'] = ['class' => $this->submenu_item_class];
        }

        //dump($replaceValues, $currentRender);

        $this->render = array_replace_recursive(
            $currentRender,
            $replaceValues,
        );

        return $this;
    }

    /*public function setAttribute($key, $value)
    {

        switch ($key) {
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
    }*/


}
