<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

return [
    'versions' => [
        'jquery'      => '3.6.3',
        'bootstrap:5' => '5.2.3',
        'bootstrap:4' => '4.6.2',
        'bootstrap' => [
            '5.3.2',
            '5.2.3',
            '5.1.3',
            '5.0.2',
            '4.6.2',
            '4.5.3',
            '4.4.1',
            '4.3.1',
        ],
        'fontawesome' => [
            '6.4.2',
            '5.15.4',
        ],
    ],
    'plugins'  => [

        'jquery' => [
            'title' => 'jQuery Framework',
            'default_version' => '3.6.3',
            'js'    => [
                'jquery.js' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/{version}/jquery.min.js',
                    'bundle' => true,
                ],
            ],
        ],

        'bootstrap' => [
            'title' => 'Bootstrap Framework',
            'default_version' => '5.3.2',
            'recommended_versions' => [
                '5.3.2',
                '5.2.3',
                '5.1.3',
                '5.0.2',
                '4.6.2',
                '4.5.3',
                '4.4.1',
                '4.3.1',
            ],
            'js'    => [
                'bootstrap.bundle.js' => [
                    'url'        => 'https://cdn.jsdelivr.net/npm/bootstrap@{version}/dist/js/bootstrap.bundle.min.js',
                    'bundle' => true,
                ],
            ],
            'css'   => [
                'bootstrap.css' => [
                    'url'        => 'https://cdn.jsdelivr.net/npm/bootstrap@{version}/dist/css/bootstrap.min.css',
                    'bundle' => true,
                ],
            ],
        ],

        'jquery-ui' => [
            'title' => 'jQuery UI',
            'default_version' => '1.12.1',
            'css'   => [
                'jquery-ui.css' => [
                    'url'        => 'https://code.jquery.com/ui/{version}/themes/base/jquery-ui.min.css',
                    'defer' => true,
                    // rel="stylesheet" href="style.css" media="print" onload="this.media='all'"
                ],
            ],
            'js'    => [
                'jquery-ui.js' => [
                    'url'        => 'https://code.jquery.com/ui/{version}/jquery-ui.min.js',
                    'bundle' => true,
                ],
            ],
        ],

        'fontawesome' => [
            'title' => 'FontAwesome',
            'default_version' => '6.4.2',
            'recommended_versions' => [
                '6.4.2',
                '5.15.4',
            ],
            'css'   => [
                'all.min.css' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/{version}/css/all.min.css',
                    'bundle' => false,
                    'defer' => true,
                    // rel="stylesheet" href="style.css" media="print" onload="this.media='all'"
                ],
            ],
        ],

        'modernizr' => [
            'title' => 'Modernizr',
            'description' => 'Modernizr é uma biblioteca JavaScript que permite a detecção de recursos do navegador, auxiliando no desenvolvimento de sites compatíveis com diferentes tecnologias e navegadores.',
            'disable_versions' => true,
            'version' => '3.6.0',
            'js'    => [
                'modernizr.js' => [
                    'url' => '/assets/vendor/modernizr/modernizr-3.6.0.min.js',
                    'defer' => true,
                    'bundle' => true,
                ],
            ],
        ],

        /*todo:'lazy-load' => [
            'title' => 'Lazy Load',
            'description' => 'LazyLoad é um script leve e flexível que acelera o seu site, adiando o carregamento de imagens, planos de fundo, vídeos, iframes e scripts abaixo da dobra para quando eles entrarem na janela de visualização. Escrito em JavaScript "vanilla" simples, ele aproveita o IntersectionObserver, oferece suporte a imagens responsivas e permite carregamento lento nativo.',
            'version' => '19.1',
            'js'    => [
                'lazyload.min.js' => [
                    'url' => 'https://cdn.jsdelivr.net/npm/vanilla-lazyload@19.1.3/dist/lazyload.min.js',
                ],
            ],
        ],*/

        'lazy-load' => [
            'title' => 'jQuery Lazy Load',
            'description' => 'O jQuery Lazy Load JS é uma biblioteca JavaScript que adia o carregamento de imagens e conteúdo, acelerando o carregamento de páginas da web e economizando largura de banda.',
            'default_version' => '1.7.9',
            'dependencies' => ['jQuery'],
            'js'    => [
                'jquery.lazy.js' => [
                    'url' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/{version}/jquery.lazy.min.js',
                    'defer' => true,
                    'bundle' => true,
                ],

                'jquery.lazy.plugins.js' => [
                    'url' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/{version}/jquery.lazy.plugins.min.js',
                    'defer' => true,
                    'bundle' => true,
                ],
            ],
        ],

        'magnific-popup' => [
            'title'       => 'Magnific Popup',
            'dependencies' => ['jQuery'],
            'description' => 'O Magnific Popup é uma biblioteca jQuery que cria pop-ups responsivos e personalizáveis, amplamente usados para exibir imagens e conteúdo de forma elegante em sites.',
            'default_version' => '1.1.0',
            'css'         => [
                'magnific-popup.css' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/{version}/magnific-popup.min.css',
                    'defer' => true,
                    'bundle' => true,
                ],
            ],
            'js'          => [
                'magnific-popup.js' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/{version}/jquery.magnific-popup.min.js',
                    'defer' => true,
                    'bundle' => true,
                ],
            ],
        ],

        'animations' => [
            'title'       => 'Animações (Animate.css + WOW.js)',
            'description' => 'Combo dos plugins para construir Animações em CSS e ao rolar a página',
            'disable_versions' => true,
            'version' => '4.1.1 + 1.1.2',
            'css'         => [
                'animate.css' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
                    'defer' => true,
                    'bundle' => true,
                ],
            ],
            'js'          => [
                'wow.js' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js',
                    'defer' => true,
                    'bundle' => true,
                ],
            ],
        ],

        'animate' => [
            'title'       => 'Animate.css',
            'description' => 'O Animate.css é uma biblioteca de CSS que oferece uma variedade de animações pré-construídas para elementos HTML, facilitando a adição de animações a sites e aplicativos.',
            'default_version' => '4.1.1',
            'css'         => [
                'animate.css' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/{version}/animate.min.css',
                    'defer' => true,
                    'bundle' => true,
                ],
            ],
        ],

        'wow' => [
            'title'       => 'WOW.js',
            'description' => 'O WOW.js é uma biblioteca JavaScript que permite adicionar animações de scroll a elementos HTML, tornando a interatividade do site mais envolvente e atraente.',
            'default_version' => '1.1.2',
            'js'          => [
                'wow.js' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/wow/{version}/wow.min.js',
                    'defer' => true,
                ],
            ],
        ],

        'slick' => [
            'title'       => 'Slick Carousel',
            'dependencies' => ['jQuery'],
            'description' => 'O Slick Carousel é um plugin jQuery que simplifica a criação de carrosséis interativos, tornando a exibição de conteúdo deslizante em sites mais fácil e flexível.',
            'default_version' => '1.8.1',
            'css'         => [
                'slick.css'       => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/{version}/slick.css',
                    'defer' => true,
                    'bundle' => false,
                ],
                'slick-theme.css' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/{version}/slick-theme.min.css',
                    'defer' => true,
                    'bundle' => false,
                ],
            ],
            'js'          => [
                'slick.js' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/{version}/slick.min.js',
                    'defer' => true,
                    'bundle' => true,
                ],
            ],
        ],

        'swiper' => [
            'title'       => 'Swiper Slider',
            'description' => 'O Swiper Slider é uma biblioteca JavaScript de slide/touch/swipe altamente personalizável, usada para criar carrosséis e galerias responsivas em páginas da web e aplicativos móveis.',
            'default_version' => '8.4.5',
            'css'         => [
                'swiper-bundle.css' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/{version}/swiper-bundle.css',
                    'defer' => true,
                    'bundle' => true,
                ],
            ],
            'js'          => [
                'swiper-bundle.js' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/{version}/swiper-bundle.min.js',
                    'defer' => true,
                    'bundle' => true,
                ],
            ],
        ],

        'owl-carousel' => [
            'title'       => 'Owl Carousel',
            'dependencies' => ['jQuery'],
            'description' => 'O Owl Carousel é um plugin jQuery que facilita a criação de carrosséis responsivos e personalizáveis para exibir conteúdo deslizante em sites e aplicativos.',
            'version' => '2.3.4',
            'css'         => [
                'owl.carousel.css'      => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/{version}/assets/owl.carousel.min.css',
                    'defer' => true,
                    'bundle' => true,
                ],
                'owl.theme.default.css' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/{version}/assets/owl.theme.default.min.css',
                    'defer' => true,
                    'bundle' => true,
                ],
            ],
            'js'          => [
                'owl.carousel.js' => [
                    'url'        => 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/{version}/owl.carousel.min.js',
                    'defer' => true,
                    'bundle' => true,
                ],
            ],
        ],
    ],
];
