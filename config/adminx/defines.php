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
use Adminx\Common\Models\Users\User;

return [
    'adminx_domain' => env('ADMINX_DOMAIN'),
    'frontend_domain' => env('FRONTEND_DOMAIN'),
    'cdn_domain' => env('CDN_HOST'),

    'morphs' => [
        'map' => [
            'report'    => Report::class,
            'site'      => Site::class,
            'post'      => Post::class,
            'page'      => Page::class,
            'category'  => Category::class,
            'menu'      => Menu::class,
            'menu_item' => MenuItem::class, //todo: change to .
            'form'      => Form::class,
            'form_answer' => FormAnswer::class,
            'theme'     => Theme::class,
            'list'      => CustomList::class,
            'list.item' => CustomListItem::class,
            'user' => User::class,
        ],
    ],

    'icons' => [
        'svg_references' => [
            'save'             => 'files/fil022.svg',
            'edit'             => 'art/art005.svg',
            'page'             => 'files/fil003.svg',
            'file'             => 'files/fil004.svg',
            'refresh'          => 'arrows/arr029.svg',
            'close'            => 'arrows/arr061.svg',
            'help'             => 'general/gen011.svg',
            'search'           => 'general/gen004.svg',
            'info'             => 'general/gen045.svg',
            'menu'             => 'abstract/abs015.svg',
            'menu-dot'         => 'general/gen053.svg',
            'post'             => 'general/gen005.svg',
            'tag'              => 'general/gen056.svg',
            'category'         => 'general/gen025.svg',
            'blog'             => 'layouts/lay009.svg',
            'list'             => 'general/gen010.svg',
            'site'             => 'files/fil020.svg',
            'home'             => 'general/gen001.svg',
            'external-link'    => 'arrows/arr095.svg',
            'site-in'          => 'arrows/arr076.svg',
            'click'            => 'coding/cod006.svg',
            'time'             => 'general/gen012.svg',
            'day'              => 'general/gen014.svg',
            'signal'           => 'electronics/elc007.svg',
            'cog'              => 'coding/cod001.svg',
            'star'             => 'general/gen003.svg',
            'email'            => 'communication/com002.svg',
            'double-check'     => 'arrows/arr084.svg',
            'check'            => 'arrows/arr085.svg',
            'unlink'           => 'coding/cod008.svg',
            'copy'             => 'general/gen054.svg',
            'link'             => 'coding/cod007.svg',
            'move'             => 'arrows/arr035.svg',
            'arrow-horizontal' => 'arrows/arr038.svg',
            'arrow-down'       => 'arrows/arr065.svg',
            'arrow-up'         => 'arrows/arr066.svg',
            'double-down'      => 'arrows/arr082.svg',
            'double-up'        => 'arrows/arr081.svg',
            'down'             => 'arrows/arr072.svg',
            'up'               => 'arrows/arr073.svg',
            'trash'            => 'general/gen027.svg',
            'image'            => 'general/gen006.svg',
            'rocket'           => 'general/gen002.svg',
            'seo'              => 'general/gen002.svg',
            'verified'         => 'general/gen026.svg',
            'plus'             => 'arrows/arr075.svg',
            'plus-button'      => 'general/gen035.svg',
            'minus'            => 'arrows/arr090.svg',
            'minus-button'     => 'general/gen036.svg',
            'display-config'   => 'general/gen062.svg',
            'display-mobile'   => 'electronics/elc002.svg',
            'display-tablet'   => 'electronics/elc003.svg',
            'display-notebook' => 'electronics/elc001.svg',
            'display-pc'       => 'electronics/elc004.svg',
            'calendar'         => 'general/gen014.svg',
            'table'            => 'files/fil002.svg',
            'publish'          => 'files/fil022.svg',
            'unpublish'        => 'files/fil021.svg',
            'read_message'     => 'communication/com010.svg',
            'fingerprint'      => 'technology/teh004.svg',
            'dark_mode'        => 'general/gen061.svg',
            'light_mode'       => 'general/gen060.svg',
            'youtube'          => 'social/soc007.svg',
            'attach'           => 'communication/com008.svg',
            'contact'          => 'communication/com005.svg',
            'layout'           => 'layouts/lay009.svg',
            'edit_layout'      => 'art/art003.svg',
            'list_layouts'     => 'art/art002.svg',
            'code'             => 'coding/cod003.svg',
            'visual_code'      => 'coding/cod010.svg',
            'script_file'      => 'coding/cod002.svg',
            'form'             => 'general/gen019.svg',
            'widget'           => 'technology/teh001.svg',
            'dir_up'           => 'arrows/arr052.svg',
            'elements'         => 'abstract/abs026.svg',
            'send_mail'        => 'general/gen016.svg',
            'data_source'      => 'general/gen017.svg',
            'report'      => 'general/gen056.svg',
        ],

        'aliases' => [
            'config'      => 'cog',
            'favorite'    => 'star',
            'mail'        => 'email',
            'duplicate'   => 'copy',
            'reload'      => 'refresh',
            'display-sm'  => 'display-mobile',
            'display-md'  => 'display-tablet',
            'display-lg'  => 'display-notebook',
            'display-xl'  => 'display-pc',
            'answers'     => 'table',
            'css_asset'         => 'list_layouts',
            'js_asset'          => 'script_file',
            'theme'       => 'layout',
            'edit_theme'  => 'edit_layout',
            'list_themes' => 'list_layouts',
        ],
    ],

    'files' => [
        'default' => [
            'names' => [
                'theme' => [
                    'media'      => [
                        'logo'           => 'Logotipo do Site',
                        'logo_secondary' => 'Logotipo Secundario do Site',
                        'favicon'        => 'Ícone do Site',
                    ],
                    'breadcrumb' => [
                        'background' => 'Imagem de Fundo do Breadcrumb',
                    ],
                ],
            ],

            'files' => [
                'theme' => [
                    'media' => [
                        'logo'           => '/assets/media/images/logo-dark.png',
                        'logo_secondary' => '/assets/media/images/logo-light.png',
                        'favicon'        => '/assets/media/images/favicon.png',
                    ],
                ],
            ],
        ],
        'types'   => [
            'image'            => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'],
            'theme_bundle'     => ['js', 'css'],
            'upload'           => [
                'themes' => [
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
                                    ],
            ],
            'editable_sources' => ['js', 'css', 'json'],
        ],
    ],

    'medias' => [
        'logo'    => [
            'light' => env('APP_LOGO_LIGHT', 'assets/media/images/logo-light.png'),
            'dark'  => env('APP_LOGO_DARK', 'assets/media/images/logo-dark.png'),

            'small' => [
                'light' => env('APP_LOGO_SMALL_LIGHT', 'assets/media/images/logo-light-small.png'),
                'dark'  => env('APP_LOGO_SMALL_DARK', 'assets/media/images/logo-dark-small.png'),
            ],
        ],
        'favicon' => env('APP_FAVICON', 'assets/media/images/favicon.png'),
    ],
];
