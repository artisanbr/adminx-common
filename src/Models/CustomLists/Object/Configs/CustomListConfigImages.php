<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\Configs;

use Adminx\Common\Models\Casts\AsCollectionWithKeysOf;
use ArtisanLabs\GModel\GenericModel;

class CustomListConfigImages extends GenericModel
{

    protected $fillable = [
        'enable',
        'max_peer_item',
        'min_peer_item',
        'sizes',

    ];

    protected $casts = [
        'enable'        => 'boolean',
        'max_peer_item' => 'int',
        'min_peer_item' => 'int',

        'sizes' => AsCollectionWithKeysOf::class . ':' . CustomListConfigImageSize::class,
    ];

    protected $attributes = [
        'enable'        => false,
        'max_peer_item' => 0,
        'min_peer_item' => 0,
        'sizes'         => [
            'xl'   => ['width' => '2560'],
            'lg'   => ['width' => '1920', 'enable' => true],
            'md'   => ['width' => '1280', 'enable' => true],
            'sm'   => ['width' => '1024'],
            'xs'   => ['width' => '414', 'enable' => true],
            'icon' => ['width' => '24'],
        ],
    ];
}
