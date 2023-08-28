<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Sites\Objects\Config\Import;

use ArtisanLabs\GModel\GenericModel;

class SiteImportConfig extends GenericModel
{

    protected $fillable = [
        'wordpress',
    ];

    protected $casts = [
        'wordpress' => WordpressImportConfig::class,
    ];
    
}
