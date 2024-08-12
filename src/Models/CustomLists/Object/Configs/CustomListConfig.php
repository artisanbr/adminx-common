<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\Configs;
use Adminx\Common\Enums\CustomLists\CustomListConfigListMode;
use ArtisanLabs\GModel\GenericModel;

class CustomListConfig extends GenericModel
{

    protected $fillable = [
        'images',
        'list_mode',
    ];

    protected $attributes = [
        'list_mode' => CustomListConfigListMode::Draggable->value,
    ];

    protected $casts = [
        'images' => CustomListConfigImages::class,
        'list_mode' => CustomListConfigListMode::class,
    ];
}
