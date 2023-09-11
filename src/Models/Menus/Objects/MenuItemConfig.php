<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Menus\Objects;

use Adminx\Common\Models\Generics\DataSource;
use ArtisanLabs\GModel\GenericModel;

class MenuItemConfig extends GenericModel
{

    protected $fillable = [
        'submenu_source',
        'is_source_submenu',
        'use_submenu_url',
    ];

    protected $attributes = [
        'is_source_submenu' => 0,
        'use_submenu_url' => 0,
    ];

    protected $casts = [
        'is_source_submenu' => 'bool',
        'use_submenu_url' => 'bool',
        'submenu_source' => DataSource::class
    ];

}
