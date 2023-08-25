<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Providers;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Pages\Page;
use App\View\Components\Icon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Spatie\Menu\Laravel\Html;
use Spatie\Menu\Laravel\Link;
use Spatie\Menu\Laravel\Menu;

class MenusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        Menu::macro('defaults', function () {
            return $this
                ->setActiveClassOnLink()
                ->setActiveClass('')
                ->setExactActiveClass('active')
                ->setActive(Request::url());
        });

        //region General

        Html::macro('kicon', function ($icon = null, $class = '', $paths = 2) {

            $size = 2;
            $kicon = true;

            if(is_array($icon)){

                $iconArray = $icon;

                $class = $iconArray['class'] ?? $class ?? '';
                $paths = $iconArray['paths'] ?? $paths ?? 2;
                $size = $iconArray['size'] ?? $size;
                $icon = $iconArray['icon'] ?? $icon;
                $kicon = $iconArray['type'] ?? $iconArray['kicon'] ?? true;
            }

            if (!is_null($icon)) {
                $html = "<span class='menu-icon'>";

                if($kicon){
                    $html .= Blade::render(<<<blade
<x-kicon i="{$icon}" class="{$class}" paths="{$paths}" size="{$size}" />
blade, compact('icon', 'class', 'paths', 'size'));
                }else{
                    $html .= <<<html
<i class="{$icon} {$class}"></i>
html;

                }

                $html .= "</span>";


            }
            else {
                $html = '<span class="menu-bullet"><span class="bullet bullet-dot"></span></span>';
            }

            return Html::raw($html . $this->render());
        });

        Html::macro('icon', function ($icon, $class = '') {
            if (is_string($icon)) {
                $html = "<span class='menu-icon {$class}'>" . (Str::contains($icon, [
                        '<',
                        '>',
                    ]) ? $icon : Icon::getSvgHtml($icon)) . "</span>";
            }
            else {
                $html = '<span class="menu-bullet"><span class="bullet bullet-dot"></span></span>';
            }

            return Html::raw($html . $this->render());
        });

        Menu::macro('header', function ($title, $class = '', $icon = null) {

            $iconHtml = Str::contains($icon, ['<', '>',]) ? $icon : Icon::getSvgHtml($icon);

            $html = "<span class='menu-item pt-4 {$class}'><div class='menu-content'><span class='menu-heading d-inline-flex align-items-center fw-bold text-uppercase fs-7'>{$iconHtml} <span class='ms-2'>{$title}</span></span></div></span>";

            return $this->html($html);
        });

        Menu::macro('separator', static fn($title, $class = '') => Html::raw("<div class='separator mx-1 my-3 {$class}'></div>"));

        //endregion

        //region Item
        Html::macro('itemTitle', static function ($title, $arrow = false) {

            $html = !Str::contains($title, 'menu-title') ? "<span class='menu-title'>{$title}</span>" : $title;

            if ($arrow) {
                $html .= "<span class='menu-arrow'></span></span>";
            }

            return Html::raw($html);
        });

        Link::macro('item', static function ($title, $to, $icon = null) {
            return Link::to($to, Html::itemTitle($title)->kicon($icon)->render())
                       ->addClass('menu-link')
                       ->addParentClass('menu-item');
        });

        Menu::macro('item', function ($title, $to = '#', $icon = null) {
            return $this->add(Link::item($title, $to, $icon));
        });

        //endregion

        //region Submenu

        Link::macro('submenuTitle', function ($title, $to = '#', $icon = true) {
            return Link::to($to, Html::submenuTitle($title)->icon($icon)->render())
                       ->addClass('menu-link')
                       ->addParentClass('menu-item');
        });

        Menu::macro('subItem', function ($title, $to = '#', $icon = true) {
            $link = Link::item($title, $to, $icon);

            if ($this->add($link)->isActive() && !isset($this->parentAttributes()['data-menu-hover'])) {
                $this->setAttributes([
                                         'class' => ['here', 'show'],
                                     ]);
                $this->setParentAttributes([
                                               'class' => ['here', 'show'],
                                           ]);


            }

            return $this;
        });

        //region General
        Menu::macro('hover', function () {
            $this->setAttribute('data-menu-hover', true);

            return $this->trigger("{default: 'click', lg: 'hover'}");
        });

        Menu::macro('trigger', function ($event = 'click') {
            return $this->setParentAttribute('data-kt-menu-trigger', $event);
        });

        Menu::macro('horizontal', function () {
            return $this
                ->hover()
                ->setParentAttributes([
                                          'class'                  => [
                                              'menu-item',
                                              'menu-lg-down-accordion',
                                              'menu-sub-lg-down-indention',
                                              'me-0',
                                              'me-lg-2',
                                          ],
                                          'data-kt-menu-placement' => 'bottom-start',
                                          'data-menu-hover'        => 'true',
                                      ])
                ->setAttributes([
                                    'class' => [
                                        'menu-sub',
                                        'menu-sub-lg-down-accordion',
                                        'menu-sub-lg-dropdown',
                                        'px-lg-2',
                                        'py-lg-4',
                                        'w-lg-250px',
                                    ],
                                ]);
        });
        //endregion

        Html::macro('submenuTitle', static function ($title) {
            return Html::itemTitle($title, true);
        });

        Menu::macro('itemSubmenuBuild', function ($html, callable $build) {
            return $this->submenu($html, function (Menu $submenu) use ($build) {
                /**
                 * @var Menu $builded
                 */

                $submenu
                    ->setParentAttributes([
                                              'class'                => ['menu-item', 'menu-accordion'],
                                              'data-kt-menu-trigger' => 'click',
                                          ])
                    ->addClass('menu-sub menu-sub-accordion')
                    ->defaults();
                //Itens Defs
                //->addItemParentClass('menu-item')
                //->addItemClass('menu-link')
                //Header

                $builded = $build($submenu);

                if ($builded->isActive() && !isset($submenu->parentAttributes()['data-menu-hover'])) {
                    $submenu->setParentAttributes([
                                                      'class' => ['here', 'show'],
                                                  ]);
                }

                return $builded;
            });
        });

        Menu::macro('itemSubmenu', function ($title, callable $build, $icon = null) {

            $submenuLink = Link::item(Html::submenuTitle($title)->render(), '#', $icon);

            return $this->itemSubmenuBuild($submenuLink->render(), $build);
        });

        //endregion

        //region Menus

        //region Pages
        Menu::macro('pages',
            function ($pages) {
                /**
                 * @var Page $page
                 */

                foreach ($pages as $page) {

                    if ($page->using_articles) {
                        //Posts
                        $this->itemSubmenu($page->title, function (Menu $submenu) use ($page) {
                            $submenu->subItem("Configurar Página", route('app.pages.cadastro', $page->public_id));

                            $submenu->subItem('Gerenciar Artigos', route('app.pages.articles.index', $page->id))
                                    ->subItem('Novo Artigo', route('app.pages.articles.cadastro', $page->id));


                            return $submenu;
                        },                 $page->is_home ? 'home' : 'blog');

                    }
                    else {
                        //Default
                        $this->item($page->title, route('app.pages.cadastro', $page->public_id), $page->is_home ? 'home' : 'page');
                    }

                }

                return $this;

            });
        //endregion

        //endregion

    }
}
