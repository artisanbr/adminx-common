<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Menus\Objects\Config\Render;

class MenuRenderObject extends GenericModel
{

    protected $fillable = [
        'class',
        'style',
        'prepend',
        'append',
    ];

    protected $casts = [
        /*'class' => 'string',
        'style' => 'string',
        'prepend' => 'string',
        'append' => 'string',*/
    ];

    protected $attributes = [
        'class' => null,
        'style' => null,
        'prepend' => null,
        'append' => null,
    ];
}
