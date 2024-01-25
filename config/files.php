<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

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
                'json',
                'pdf',
                'eot',
                'otf',
                'woff',
                'woff2',
                'ttf',
                'mp4',
                'webm',
            ]
        ]
    ],


];
