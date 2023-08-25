<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

use Adminx\Common\Models\Sites\Site;

return [
    'mime_types' => [
        'images' => [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/bmp',
            'image/webp',
            'image/svg+xml',
            'image/x-icon',
        ],
        'webp_convert' => [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
        ],
        'zip' => [
            'application/zip',
        ],

        'theme' => [
            'editable' => [
                'text/css',
                'application/javascript',
                'application/json',
            ]
        ]
    ],

    'extensions' => [
        'images'            => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'],
        'webp_convert'            => ['jpg', 'jpeg', 'png', 'gif'],

        'theme' => [
            'assets_bundle'     => ['js','css'],
            'editable' => ['js','css','json'],
            'upload_allow' => [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'svg',
                'webp',
                'zip',
                'css',
                'js',
                'pdf',
                'eot',
                'woff',
                'ttf',
                'mp4',
                'webm',
            ]
        ]
    ],


];
