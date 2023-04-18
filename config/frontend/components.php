<?php

return [
    'props' => [
        'pages'   => [
            'Posts' => [
                'miniature' => [
                    'big' => false,
                    'small' => false,

                    'descriptionLines' => 5,
                    'titleLines' => 2,

                    'showDescription' => true,
                    'showMeta' => true,
                    'showCover' => true,
                    'showReadMore' => true,
                    'showCategories' => true,
                    'showIcons' => true,
                    'bottomMeta' => false,

                    'readMoreText' => 'Leia Mais',
                    'categoriesIcon' => '<i class="fa-solid fa-tags"></i>',

                    //Meta
                    'showAuthor' => true,
                    'showDate' => true,
                    'showComments' => false,

                    'dateHumanFormat' => true,
                    'dateFormat' => 'd/m/Y H:i:s',
                    'shortComments' => true,

                    'authorIcon' => '<i class="fa-solid fa-user me-1"></i>',
                    'dateIcon' => '<i class="fa-solid fa-calendar-days me-1"></i>',
                    'commentsIcon' => '<i class="fa-solid fa-comments me-1"></i>',
                ]
            ]
        ],
        'posts'   => 'Posts',
        'widgets' => 'Elementos Dinâmicos (Widgets)',
    ],
];
