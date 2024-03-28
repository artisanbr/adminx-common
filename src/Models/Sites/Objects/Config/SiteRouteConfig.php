<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Sites\Objects\Config;
use ArtisanBR\GenericModel\GenericModel;

class SiteRouteConfig extends GenericModel
{

    protected $fillable = [
        'redirect_to'
    ];

    protected $attributes = [
    ];

    protected $casts = [
        'redirect_to' => 'string'
    ];
}
