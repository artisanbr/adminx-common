<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

use Adminx\Common\Models\Article;
use Adminx\Common\Models\Category;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItem;
use Adminx\Common\Models\Form;
use Adminx\Common\Models\FormAnswer;
use Adminx\Common\Models\Menus\Menu;
use Adminx\Common\Models\Menus\MenuItem;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Pages\PageInternal;
use Adminx\Common\Models\Report;
use Adminx\Common\Models\Sites\Site;
use Adminx\Common\Models\Themes\Theme;
use Adminx\Common\Models\Users\User;
use Adminx\Common\Models\Widget;
use Adminx\Common\Models\Widgets\SiteWidget;

return [
    'map' => [
        'report'        => Report::class,
        'site'          => Site::class,
        'article'       => Article::class,
        'page'          => Page::class,
        'page_internal' => PageInternal::class,
        'category'      => Category::class,
        'menu'          => Menu::class,
        'menu_item'     => MenuItem::class, //todo: change to .
        'form'          => Form::class,
        'form_answer'   => FormAnswer::class,
        'theme'         => Theme::class,
        'list'          => CustomList::class,
        'list_item'     => CustomListItem::class,
        'user'          => User::class,
        'widget'        => Widget::class,
        'site_widget'   => SiteWidget::class,
    ],
];
