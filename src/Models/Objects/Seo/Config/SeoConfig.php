<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Seo\Config;

use ArtisanBR\GenericModel\GenericModel;

class SeoConfig extends GenericModel
{

    protected $fillable = [
        'show_parent_title',
        'use_defaults',
    ];

    protected $attributes = [
        'show_parent_title'       => true,
        'use_defaults' => true,
    ];

    protected $casts = [
        'show_parent_title'          => 'bool',
        'use_defaults'          => 'bool'
        ];

    protected $appends = [
    ];

}
