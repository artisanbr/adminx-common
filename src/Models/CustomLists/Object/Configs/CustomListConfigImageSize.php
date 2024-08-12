<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\Configs;
use ArtisanLabs\GModel\GenericModel;

class CustomListConfigImageSize extends GenericModel
{

    protected $fillable = [
        'enable',
        'height',
        'width',
    ];

    protected $casts = [
        'height' => 'string',
        'width' => 'string',
        'enable' => 'bool',
    ];

    protected $attributes = [
    ];
}
