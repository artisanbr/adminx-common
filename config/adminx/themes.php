<?php

return [
    'versions' => [
        'jquery'      => '3.6.3',
        'bootstrap:5' => '5.2.3',
        'bootstrap:4' => '4.6.2',
    ],
    'plugins'  => [
        'jquery-ui' => [
            'title' => 'jQuery UI',
            'css'   => [
                'jquery-ui.css' => [
                    'src'        => 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.min.css',
                    'attributes' => [
                        'rel'    => 'stylesheet',
                        'media'  => 'print',
                        'onload' => "this.media='all'",
                    ],

                    // rel="stylesheet" href="style.css" media="print" onload="this.media='all'"
                ],
            ],
            'js'    => [
                'jquery-ui.js' => [
                    'src'        => 'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js',
                    'attributes' => [
                        'integrity'   => 'sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=',
                        'crossorigin' => 'anonymous',
                        'async',
                    ],
                ],
            ],
        ],

        'modernizr' => [
            'title' => 'Modernizr',
            'js'    => [
                'modernizr.js' => [
                    'src' => '/assets/vendor/modernizr/modernizr-3.6.0.min.js',
                ],
            ],
        ],

        'lazy-load' => [
            'title' => 'Lazy Load',
            'js'    => [
                'jquery.lazy.js' => [
                    'src' => '//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js',
                ],

                'jquery.lazy.plugins.js' => [
                    'src' => '//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.plugins.min.js',
                ],
            ],
        ],

        'magnific-popup' => [
            'title'       => 'Magnific Popup',
            'description' => 'Popup/Modal em Jquery',
            'css'         => [
                'magnific-popup.css' => [
                    'src'        => 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css',
                    'attributes' => [
                        'integrity'      => 'sha512-+EoPw+Fiwh6eSeRK7zwIKG2MA8i3rV/DGa3tdttQGgWyatG/SkncT53KHQaS5Jh9MNOT3dmFL0FjTY08And/Cw==',
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',
                        'rel'    => 'stylesheet',
                        'media'  => 'print',
                        'onload' => "this.media='all'",
                    ],
                ],
            ],
            'js'          => [
                'magnific-popup.js' => [
                    'src'        => 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js',
                    'attributes' => [
                        'integrity'      => 'sha512-IsNh5E3eYy3tr/JiX2Yx4vsCujtkhwl7SLqgnwLNgf04Hrt9BT9SXlLlZlWx+OK4ndzAoALhsMNcCmkggjZB1w==',
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',
                        'defer'
                    ],
                ],
            ],
        ],

        'animations' => [
            'title'       => 'Animações (Animate.css + WOW.js)',
            'description' => 'Animações em CSS e ao rolar a página',
            'css'         => [
                'animate.css' => [
                    'src'        => 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
                    'attributes' => [
                        'integrity'      => 'sha512-c42qTSw/wPZ3/5LBzD+Bw5f7bSF2oxou6wEb+I/lqeaKV5FDIfMvvRp772y4jcJLKuGUOpbJMdg/BTl50fJYAw==',
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',
                        'rel'    => 'stylesheet',
                        'media'  => 'print',
                        'onload' => "this.media='all'",
                    ],
                ],
            ],
            'js'          => [
                'wow.js' => [
                    'src'        => 'https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js',
                    'attributes' => [
                        'integrity'      => 'sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g==',
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',
                    ],
                ],
            ],
        ],

        'slick' => [
            'title'       => 'Slick Carousel',
            'description' => 'Slide e Carrosel em Javascript',
            'css'         => [
                'slick.css'       => [
                    'src'        => 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css',
                    'attributes' => [
                        'integrity'      => 'sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw==',
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',
                    ],
                ],
                'slick-theme.css' => [
                    'src'        => 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css',
                    'attributes' => [
                        'integrity'      => 'sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A==',
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',
                        'rel'    => 'stylesheet',
                        'media'  => 'print',
                        'onload' => "this.media='all'",
                    ],
                ],
            ],
            'js'          => [
                'slick.js' => [
                    'src'        => 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js',
                    'attributes' => [
                        'integrity'      => 'sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg==',
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',
                    ],
                ],
            ],
        ],

        'swiper' => [
            'title'       => 'Swiper Slider',
            'description' => 'Slide e Carrosel em Javascript',
            'css'         => [
                'swiper-bundle.css' => [
                    'src'        => 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.5/swiper-bundle.css',
                    'attributes' => [
                        'integrity'      => 'sha512-jxGmKjC/OykrTklkNK2NgnhNtKtUAADFY+rvSi3nA7dbaPRfjrSXYxHqX8iq5N6WTOntqEQZrEwW3L84sirfKQ==',
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',
                        'rel'    => 'stylesheet',
                        'media'  => 'print',
                        'onload' => "this.media='all'",
                    ],
                ],
            ],
            'js'          => [
                'swiper-bundle.js' => [
                    'src'        => 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.5/swiper-bundle.min.js',
                    'attributes' => [
                        'integrity'      => 'sha512-wdUM0BxMyMC/Yem1RWDiIiXA6ssXMoxypihVEwxDc+ftznGeRu4s9Fmxl8PthpxOh5CQ0eqjqw1Q8ScgNA1moQ==',
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',
                    ],
                ],
            ],
        ],

        'owl-carousel' => [
            'title'       => 'Owl Carousel',
            'description' => 'Carrosel em Javascript',
            'css'         => [
                'owl.carousel.css'      => [
                    'src'        => 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css',
                    'attributes' => [
                        'integrity'      => 'sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==',
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',
                        'rel'    => 'stylesheet',
                        'media'  => 'print',
                        'onload' => "this.media='all'",
                    ],
                ],
                'owl.theme.default.css' => [
                    'src'        => 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css',
                    'attributes' => [
                        'integrity'      => 'sha512-sMXtMNL1zRzolHYKEujM2AqCLUR9F2C4/05cdbxjjLSRvMQIciEPCQZo++nk7go3BtSuK9kfa/s+a4f4i5pLkw==',
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',
                        'rel'    => 'stylesheet',
                        'media'  => 'print',
                        'onload' => "this.media='all'",
                    ],
                ],
            ],
            'js'          => [
                'owl.carousel.js' => [
                    'src'        => 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
                    'attributes' => [
                        'integrity'      => 'sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==',
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',
                    ],
                ],
            ],
        ],
    ],
];
