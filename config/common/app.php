<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

return [
    'adminx_domain'   => env('ADMINX_DOMAIN'),
    'frontend_domain' => env('FRONTEND_DOMAIN'),
    'cdn_domain'      => env('CDN_HOST'),


    'info' => [
        'description' =>
            'O AdminX é uma plataforma de criação e administração de websites que visa ser simples e dinâmica, facilitando a criação de sites mesmo para quem seja leigo no assunto, e ao mesmo tempo poderosa e versátil podendo agradar até mesmo experientes desenvolvedores.',

        'keywords' =>
            'adminx, painel, criação de sites, cms, painel administrativo, gerenciador de conteúdo, content manager',

        'version' => '0.6.6-alpha',
    ],

    'provider' => [
        'name'      => env('ADMINX_PROVIDER_NAME', 'Artisan Digital'),
        'url'       => env('ADMINX_PROVIDER_URL', 'https://artisan.dev.br'),
        'logo'      => env('ADMINX_PROVIDER_LOGO', 'media/images/providers/artisan/logo-micro.webp'),
        'since'     => env('ADMINX_PROVIDER_SINCE', 2022),
        'copyright' => env('ADMINX_PROVIDER_COPYRIGHT', "Desenvolvido com ❤️ com o apoio de"),
    ],

    'domains' => [
        'adminx'   => env('ADMINX_DOMAIN'),
        'frontend' => env('FRONTEND_DOMAIN'),
    ],
];
