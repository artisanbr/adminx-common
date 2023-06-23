<?php

use Adminx\Common\Models\Category;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItem;
use Adminx\Common\Models\Form;
use Adminx\Common\Models\Menu;
use Adminx\Common\Models\MenuItem;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Post;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\Theme;
use Adminx\Common\Models\Report;
use Adminx\Common\Models\FormAnswer;

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
