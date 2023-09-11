<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Menus\Objects;

use ArtisanLabs\GModel\GenericModel;

class MenuRenderObject extends GenericModel
{

    protected $fillable = [
        'class',
        'style',
    ];

    protected $casts = [
        'class' => 'string',
        'style' => 'string',
    ];
}
