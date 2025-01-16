<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\Values;

use ArtisanLabs\GModel\GenericModel;

class PDFValue extends GenericModel
{

    protected $fillable = [
        'url',
        'path',
        'position',
    ];

    protected $casts = [
        'url'             => 'string',
        'path'             => 'string',
        'position' => 'int',
    ];

    protected $attributes = [];

}
