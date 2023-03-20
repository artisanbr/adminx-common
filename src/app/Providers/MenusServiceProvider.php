<?php

namespace ArtisanBR\Adminx\Common\App\Providers;

use App\View\Components\Icon;
use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use ArtisanBR\Adminx\Common\App\Models\Page;
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
            return Link::to($to, Html::itemTitle($title)->icon($icon)->render())
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

                    if ($page->using_posts || $page->using_forms) {
                        //Posts
                        $this->itemSubmenu($page->title, function (Menu $submenu) use ($page) {
                            $submenu->subItem("Configurar Página", route('app.pages.cadastro', $page->id));

                            if ($page->using_posts) {
                                //Posts
                                $submenu->subItem('Gerenciar Postagens', route('app.pages.posts.index', $page->id))
                                        ->subItem('Nova Postagem', route('app.pages.posts.cadastro', $page->id));
                            }

                            if ($page->using_forms) {
                                //todo: Form
                                /*$submenu->subItem('Respostas do Formulário', route('app.elements.forms.answers', [
                                    $page->form->id,
                                    $page->id,
                                ]));*/
                            }

                            return $submenu;
                        },                 $page->is_home ? 'home' : 'blog');

                    }
                    else {
                        //Default
                        $this->item($page->title, route('app.pages.cadastro', $page->id), $page->is_home ? 'home' : 'page');
                    }

                }

                return $this;

            });
        //endregion

        //sideMenu
        Menu::macro('sideMenu', function () {
            $menu = Menu::new()
                        ->setAttributes([
                                            'class'               => ['menu menu-column menu-rounded menu-sub-indention px-3'],
                                            'id'                  => 'kt_app_sidebar_menu',
                                            'data-kt-menu'        => 'true',
                                            'data-kt-menu-expand' => 'false',
                                        ])

                //Itens
                        ->item('Painel de Controle', route('app.dashboard.index'), 'general/gen002.svg')

                //Sites
                /*       ->itemSubmenu('Sites', function (Menu $submenu) {

                    return $submenu
                        ->subItem('Configurar Site', route('app.sites.config'), '<span class="fa fa-cog"></span>')
                        ->subItem('Meus Sites', route('app.sites.index'));

                },                   'site')*/

                //Configs
                        ->itemSubmenu('Configurações', function (Menu $submenu) {

                    return $submenu
                        //Config Global (Equipe Sua Marca)
                        ->itemSubmenu('Globais', function (Menu $submenu) {

                            return $submenu
                                ->subItem('Informativos', route('app.core.reports.index'), 'report')
                                ->itemSubmenu('Regras de Acesso', function (Menu $submenu) {

                                    return $submenu
                                        ->subItem('Grupos')
                                        ->subItem('Permissões');


                                }, icon:      'communication/com001.svg')
                                ->itemSubmenu('Páginas', function (Menu $submenu) {

                                    return $submenu
                                        ->subItem('Tipos de Páginas', route('app.core.pages.types.index'))
                                        ->subItem('Modelos de Páginas', route('app.core.pages.models.index'));


                                }, icon:      'page')
                                ->itemSubmenu('Widgets', function (Menu $submenu) {

                                    return $submenu
                                        ->subItem('Widgets da Plataforma', route('app.core.elements.widgets.index'))
                                        ->subItem('Tipos de Widgets', route('app.core.elements.widgets.types.index'));


                                }, icon:      'widget');

                        })
                        ->itemSubmenu('Conta', function (Menu $submenu) {

                            return $submenu
                                //todo: Minha Conta
                                ->itemSubmenu('Minha Conta', function (Menu $submenu) {

                                    return $submenu
                                        ->subItem('Action')
                                        ->subItem('Another action');


                                }, icon:      'communication/com006.svg')
                                ->item('Usuários', route('app.users.index'), 'communication/com014.svg');


                        });

                }, icon:              'coding/cod001.svg')

                //Este Site
                        ->item('Meus Sites', route('app.sites.index'), 'site')
                        ->header('Este Site'/*, icon: 'general/gen062.svg'*/)
                        ->item('Configurar Site', route('app.sites.config'), '<span class="fa fa-cog"></span>')
                        ->itemSubmenu('Temas', function (Menu $submenu) {

                            return $submenu
                                ->subItem('Configurar Tema', route('app.themes.config'), 'theme')
                                ->subItem('Biblioteca de Temas', route('app.themes.index'), 'list_themes');

                        },            'art/art007.svg')
                        ->itemSubmenu('Elementos', function (Menu $submenu) {
                            return $submenu
                                ->subItem('Listas', route('app.elements.lists.index'), 'list')
                                ->subItem('Formulários', route('app.elements.forms.index'), 'form')
                                //Menus
                                ->itemSubmenu('Menus', function (Menu $submenu) {

                                    return $submenu
                                        ->subItem('Config. Menu Principal', route('app.elements.menus.main'))
                                        ->subItem('Listar Menus', route('app.elements.menus.index'))
                                        ->subItem('Novo Menu', route('app.elements.menus.cadastro'));

                                },            'menu');
                        }, icon:      'elements')
                        ->item('Gerenciar Páginas', route('app.pages.index'), 'page')

                //Pages

                        ->defaults();

            if (auth()->user()->site->pages->count()) {
                $menu->header('Páginas')
                     ->pages(auth()->user()->site->pages);
            }
            else {
                $menu->header('Nenhuma Página Cadastrada');
            }

            return $menu;
        });

        //headerMenu
        Menu::macro('headerMenu', function () {
            return Menu::new()
                       ->setAttributes([
                                           'class'        => ['menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0'],
                                           'id'           => 'kt_app_header_menu',
                                           'data-kt-menu' => 'true',
                                       ])

                //Itens
                       ->item('Páginas', route('app.pages.index'), 'page')
                       ->itemSubmenu('Temas', function (Menu $submenu) {

                           return $submenu
                               ->horizontal()
                               ->subItem('Configurar Tema', route('app.themes.config'), 'edit_theme')
                               ->subItem('Meus Temas', route('app.themes.index'), 'list_themes');

                       },            'theme')

                //todo: fix
                       ->itemSubmenu('Categorias', function (Menu $submenu) {

                    return $submenu
                        ->horizontal()
                        ->subItem('Listar Categorias', 'adminx/categories')
                        ->subItem('Adicionar Categoria', 'adminx/categories/cadastro');

                },                   'category')
                       ->itemSubmenu('Menus', function (Menu $submenu) {

                           return $submenu
                               ->horizontal()
                               ->subItem('Listar Menus', route('app.elements.menus.index'))
                               ->subItem('Editar Menu Principal', route('app.elements.menus.main'))
                               ->subItem('Criar Menu', route('app.elements.menus.cadastro'));

                       },            'menu')
                       ->defaults();
        });
        //endregion

    }
}
