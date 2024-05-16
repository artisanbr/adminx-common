<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Forms;

use ArtisanLabs\GModel\GenericModel;

class FormRecipient extends GenericModel
{

    protected $fillable = [
        'address',
        'title',
    ];

    protected $casts = [
        'address' => 'string',
        'title' => 'string',
    ];
}
