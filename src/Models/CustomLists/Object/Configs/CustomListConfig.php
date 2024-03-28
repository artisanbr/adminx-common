<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\Configs;
use ArtisanBR\GenericModel\Model as GenericModel;

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
