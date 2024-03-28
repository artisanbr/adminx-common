<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\Configs\CustomListItems;

use ArtisanBR\GenericModel\Model as GenericModel;

class CustomListItemConfig extends GenericModel
{

    protected $fillable = [
        'wp_id',
    ];

    protected $attributes = [
    ];

    protected $casts = [
        'wp_id' => 'int',
    ];
}
