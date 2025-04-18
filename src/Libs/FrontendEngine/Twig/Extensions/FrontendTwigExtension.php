<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\FrontendEngine\Twig\Extensions;

use Adminx\Common\Facades\Frontend\FrontendPage;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Models\Category;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListItem;
use Adminx\Common\Models\Form;
use Adminx\Common\Models\Menus\Menu;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Sites\Site;
use Adminx\Common\Models\Templates\Global\Manager\Facade\GlobalTemplateManager;
use Adminx\Common\Models\Templates\Template;
use Adminx\Common\Models\Widgets\SiteWidget;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FrontendTwigExtension extends AbstractExtension
{
    /**
     * @var Collection|SiteWidget[]|Builder
     */
    protected array|Collection $widgets;

    /**
     * @var Collection|Form[]|Builder
     */
    protected array|Collection $forms;

    protected ?SiteWidget $currentSiteWidget = null;
    protected ?Form       $currentForm       = null;

    //protected Site $currentSite;


    /**
     * @property Collection<Menu>|Menu[]
     */
    protected Collection $menus;

    /**
     * @property Collection<CustomList>|CustomList[]
     */
    protected Collection $customLists;

    /**
     * @property Collection<Page>|Page[]
     */
    protected Collection $pages;

    public function __construct(protected Environment $twig, protected ?Site $currentSite = null)
    {
        if (!$this->currentSite) {
            $this->currentSite = FrontendSite::current();
        }
        $this->widgets = collect();
        $this->forms = collect();

        $this->menus = collect();
        $this->customLists = collect();
        $this->pages = collect();

    }

    public function getFunctions()
    {
        return [
            new TwigFunction('widget', $this->widget(...), ['needs_context' => true]),
            new TwigFunction('w', $this->widget(...), ['needs_context' => true]),

            //Todo: formRender e formRenderAjax
            new TwigFunction('form', $this->form(...), ['needs_context' => true]),
            new TwigFunction('f', $this->form(...), ['needs_context' => true]),

            new TwigFunction('menu', $this->menu(...), ['needs_context' => true]),
            new TwigFunction('m', $this->menu(...), ['needs_context' => true]),

            new TwigFunction('custom_list', $this->customList(...), ['needs_context' => true]),
            new TwigFunction('customList', $this->customList(...), ['needs_context' => true]),
            new TwigFunction('lista', $this->customList(...), ['needs_context' => true]),
            new TwigFunction('list', $this->customList(...), ['needs_context' => true]),
            new TwigFunction('l', $this->customList(...), ['needs_context' => true]),

            new TwigFunction('listItems', $this->customListItems(...), ['needs_context' => true]),
            new TwigFunction('list_items', $this->customListItems(...), ['needs_context' => true]),
            new TwigFunction('lis', $this->customListItems(...), ['needs_context' => true]),


            new TwigFunction('listCategories', $this->customListCategories(...), ['needs_context' => true]),
            new TwigFunction('list_categories', $this->customListCategories(...), ['needs_context' => true]),
            new TwigFunction('lcs', $this->customListCategories(...), ['needs_context' => true]),

            new TwigFunction('listItem', $this->getListItem(...), ['needs_context' => true]),
            new TwigFunction('list_item', $this->getListItem(...), ['needs_context' => true]),
            new TwigFunction('li', $this->getListItem(...), ['needs_context' => true]),


            new TwigFunction('page', $this->page(...), ['needs_context' => true]),
            new TwigFunction('parent', $this->parent(...), ['needs_context' => true]),
            new TwigFunction('parentPage', $this->parent(...), ['needs_context' => true]),
            new TwigFunction('parents', $this->parents(...), ['needs_context' => true]),
            new TwigFunction('getParents', $this->parents(...), ['needs_context' => true]),
            new TwigFunction('children', $this->children(...), ['needs_context' => true]),
            new TwigFunction('subPages', $this->children(...), ['needs_context' => true]),

            new TwigFunction('articles', $this->articles(...), ['needs_context' => true]),
            new TwigFunction('article', $this->getArticle(...), ['needs_context' => true]),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('widget', [$this, 'widget'], ['needs_context' => true]),
            new TwigFilter('form', [$this, 'form'], ['needs_context' => true]),
            new TwigFilter('menu', [$this, 'menu'], ['needs_context' => true]),
            new TwigFilter('custom_list', [$this, 'customList'], ['needs_context' => true]),
        ];
    }


    /**
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function widget($context, $widget_public_id): string
    {

        if (!$this->currentSiteWidget || $this->currentSiteWidget->public_id != $widget_public_id) {
            //Não foi o ultimo utilizado, Verificar no cache
            $this->currentSiteWidget = $this->widgets->firstWhere('public_id', $widget_public_id) ?? $this->widgets->firstWhere('slug', $widget_public_id);

            //Não encontrou, buscar no banco
            if (!$this->currentSiteWidget) {
                $this->currentSiteWidget = $this->currentSite->widgets()->where('public_id', $widget_public_id)->orWhere('slug', $widget_public_id)->first();

                if ($this->currentSiteWidget) {
                    $this->widgets->add($this->currentSiteWidget);
                }
            }

            //Se não encontrar parar aqui.
            if (!$this->currentSiteWidget) {
                return "Widget '{$widget_public_id}' não encontrado";
            }
        }


        //Se estiver sem content, compilar
        /*if (empty($this->currentSiteWidget->content->html)) {
            $this->currentSiteWidget->save();
        }*/

        if ($this->currentSiteWidget->config->ajax_render) {

            return $this->currentSiteWidget->content->portal ?? $this->currentSiteWidget->content->html ?? '';

        }


        $templateContent = $this->currentSiteWidget->template?->id ? $this->currentSiteWidget->template_content : $this->currentSiteWidget->content->html;

        $template = $this->twig->createTemplate($templateContent, "widget-{$this->currentSiteWidget->public_id}");

        return $template->render($this->currentSiteWidget->getViewRenderData());

    }

    public function form($context, string $form_slug, bool $ajax = false): string
    {

        if (!$this->currentForm || ((string)$this->currentForm->public_id !== $form_slug && (string)$this->currentForm->slug !== $form_slug)) {
            //Não foi o ultimo utilizado, Verificar no cache
            $this->currentForm = $this->forms->firstWhere('public_id', $form_slug) ?? $this->forms->firstWhere('slug', $form_slug);

            //Não encontrou, buscar no banco
            if (!$this->currentForm) {
                $this->currentForm = $this->currentSite->forms()->where('public_id', $form_slug)->orWhere('slug', $form_slug)->first();

                if ($this->currentForm) {
                    $this->forms->add($this->currentForm);
                }
            }

            //Se não encontrar parar aqui.
            if (!$this->currentForm) {
                return "Formulário '{$form_slug}' não encontrado";
            }

            /*if ($ajax) {

                $renderView = 'common::Elements.Widgets.renders.ajax-render';

                $renderData = $this->config->ajax_render ? [
                    'siteWidget' => $this,
                ] : $this->getViewRenderData();

                $widgetView = View::make($renderView, $renderData);

                $this->content->portal = $widgetView->render();

                return $this->currentSiteWidget->content->portal ?? $this->currentSiteWidget->content->html ?? '';

            }*/

            $templateBase = GlobalTemplateManager::getTemplate('custom-form');

            $template = new Template($templateBase->toArray());

            //Debugbar::debug($template->blade_file);

            return View::make($template->blade_file, [
                'form'     => $this->currentForm,
                'template' => $template,
            ])->render();

        }


        //Se estiver sem content, compilar


        return $this->currentSiteWidget->content->html;

    }

    public function menu($context, $menuSlug, $exibition = null)
    {

        //Verificar no cache
        $menu = $this->menus->firstWhere('slug', $menuSlug); //todo: public_id

        //Não encontrou, buscar no banco
        if (!$menu) {
            $menu = $this->currentSite->menus()->where('slug', $menuSlug)->first();

            if ($menu) {
                $this->menus->add($menu);
            }
        }
        //DebugBar::debug($menuSlug, $exibition, (string)$menu->buildTwig());

        //$menu = $this->menus->firstWhere('slug', $menuSlug);
        if (!$menu) {
            return 'Menu não encontrado';
        }

        $template = $this->twig->createTemplate($menu->html, "menu-{$menu->id}");

        $renderConfig = null;

        if ($exibition) {
            $renderConfig = $menu->config->renders->getBySlug($exibition) ?? $menu->config->renders->getByIndex($exibition);
        }

        if (!$renderConfig) {
            $renderConfig = $menu->config->renders->getDefault();
        }

        /*DebugBar::debug($exibition, $renderConfig->toArray(), [
            'html' => $menu->html,
        ]);*/

        return $template->render([
                                     'renderConfig' => $renderConfig->toArray(),
                                 ]);

        // return $menu->html ?? 'Menu não encontrado';

    }

    public function customList($context, $list = null): CustomList
    {

        //Verificar no cache
        if(!$list){
            return $context['list'] ?? new CustomList();
        }

        $customList = $this->customLists->firstWhere('public_id', $list) ?? $this->customLists->firstWhere('slug', $list);

        //Não encontrou, buscar no banco
        if (!$customList) {

            $customList = $this->currentSite->lists()
                                            ->where('public_id', $list)
                                            ->orWhere('slug', $list)
                                            ->with(['items' => fn($query) => $query->withRelations()])
                                            ->first();

            if ($customList) {
                /**
                 * @var CustomList $customList
                 */


                $customList->append('categories');
                //$customList->append('categories');

                /*$customList['items'] = $customListModel->items;

                $customList['categories'] = CustomList::find($customListModel->id)->categories->toArray();

                foreach ($customList['items'] as &$listItem) {

                    $listItem['categories'] = CustomListItem::find($listItem->id)->categories->toArray();

                }
                unset($listItem);*/


                $this->customLists->add($customList);
            }
        }


        return $customList ?? new CustomList();

    }

    public function customListItems($context, $list = null, $perPage = 0, $pageNumber = 1, null|string|array $category = null, ?string $search = null)
    {

        //dd($context, $list, $perPage, $pageNumber, $category, $search);

        $list = $this->customList($context, $list);

        if(!$list?->id){
            return collect();
        }

        $query = $list->items()->withRelations();

        if (!blank($category)) {
            $categories = is_array($category) ? $category : str($category)->explode('|')->toArray();
            $categories = collect($categories)->filter()->values()->toArray();
            $query = $query->hasAnyCategory($categories);
        }

        if (!blank($search)) {
            $query = $query->search($search);
        }


        $result = $perPage > 0 ? $query->paginate(
            perPage: $perPage,
            columns: ['*'],
            page:    $pageNumber
        )->collect() : $query->get();

        $result = $result->append(['url', 'uri']);

        return $result ?? collect();

    }

    public function customListCategories($context, $list = null, $perPage = 0, $pageNumber = 1, null|string|array $parent = null, ?string $search = null)
    {

        $list = $this->customList($context, $list);

        if(!$list?->id){
            return collect();
        }

        $query = $list->categories()->with(['children']);

        if (!blank($parent)) {
            $parents = is_array($parent) ? $parent : str($parent)->explode('|')->toArray();
            $parents = collect($parents)->filter()->values()->toArray();
            $query = $query->whereHas('parent', fn($parentQuery) => $parentQuery->whereUrlIn($parents));
        }

        if (!blank($search)) {
            $query = $query->search($search);
        }


        $result = $perPage > 0 ? $query->paginate(
            perPage: $perPage,
            columns: ['*'],
            page:    $pageNumber
        )->collect() : $query->get();

        //['user_id','account_id','site_id']

        //dd($result->append(['url', 'uri'])->withoutRelationships());

            $result = $result->append(['url'])->map(function (Category $category): Category {


                //return $category->withoutRelations();


                $categoryData = $category->only(['id','site_id', 'title', 'slug', 'description', 'parent_id','url']);
                $categoryModel = new Category($categoryData);

                $categoryModel->setAttribute('id', $categoryData['id'] ?? null);
                $categoryModel->setAttribute('url', $categoryData['url'] ?? null);
                //$categoryModel->setAttribute('uri', $categoryData['uri'] ?? null);

                return $categoryModel;
            });


            //dd($result);

        return $result ?? collect();

    }

    public function getListItem($context, $list = null, $item = null): CustomListItem
    {

        $list = $this->customList($context, $list);

        if(!$list?->id){
            return new CustomListItem();
        }

        if(!$item){
            return $context['currentItem'] ?? new CustomListItem();
        }

        $query = $list->items()->withRelations()->where(function ($query) use ($item) {
            $query->whereUrl($item);
        });

        return $query->first() ?? new CustomListItem();

    }

    /**
     * Recupera os artigos de uma pagina
     */
    public function articles($context, null|string|Page $page = null, $perPage = 0, $pageNumber = 1, null|string|array $category = null, ?string $search = null)
    {

        if (!$page) {
            $page = $this->currentSite->pages()->with(['site'])->whereHas('articles')->first();
        }
        else {
            $page = $this->page($context, $page);
        }

        if (!($page instanceof Page)) {
            return collect();
        }

        $query = $page->articles()->with(['page', 'page.site'])->ordered();

        if (!blank($category)) {
            $categories = is_array($category) ? $category : str($category)->explode('|')->toArray();
            $categories = collect($categories)->filter()->values()->toArray();
            $query = $query->hasAnyCategory($categories);
        }

        if (!blank($search)) {
            $query = $query->search($search);
        }


        return $perPage > 0 ? $query->paginate(
            perPage: $perPage,
            columns: ['*'],
            page:    $pageNumber
        )->collect() : ($query->get() ?? collect());

    }

    //todo: Articles categories

    public function getArticle($context, $article, null|string|Page $page = null)
    {

        if (!$page) {
            $page = $this->currentSite->pages()->with(['site'])->whereHas('articles')->first();
        }
        else {
            $page = $this->page($context, $page);
        }


        return $page?->articles()->where(function ($query) use ($article) {
            $query->where('public_id', $article)->orWhere('slug', $article);
        })->first() ?? null;

    }

    /**
     * Recupera uma página pelo slug ou public_id
     */
    public function page($context, null|string|Page $page = null, ?string $parent = null)
    {

        if (!$page) {
            //Pega home page
            $page = $context['page'] ?? FrontendPage::current() ?? $this->currentSite->pages()
                                                                                     ->with([
                                                                                                'site',
                                                                                            ])->homePage()->first();
        }
        else if (is_string($page)) {

            $searchTerm = $page;

            //Verificar no cache
            $page = $this->pages->firstWhere('public_id', $searchTerm) ?? $this->pages->firstWhere('slug', $searchTerm);

            if (!$page) {
                $page = $this->currentSite->pages()
                                          ->with(['site', 'parent'])
                                          ->whereUrl($searchTerm);

                if ($parent) {
                    $page = $page->whereHas('parent', fn($parentQuery) => $parentQuery->whereUrl($parent));
                }

                $page = $page->first();

                if (!$parent) {
                    $this->pages->add($page);
                }
            }
        }

        return $page ?? null;

    }

    /**
     * Recupera a página pai de uma página pelo slug ou public_id
     */
    public function parent($context, null|string|Page $page = null)
    {

        $page = $this->page($context, $page);

        return $page?->parent ?? null;

    }

    /**
     * Recupera as páginas pai do site
     */
    public function parents($context, $perPage = 0, $pageNumber = 1)
    {
        $query = $this->currentSite->pages()->parents();

        return $perPage > 0 ? $query->paginate($perPage, ['*'], $pageNumber)->collect() : ($query->get() ?? collect());

    }

    /**
     * Recupera as sub-páginas de uma página pelo slug ou public_id
     */
    public function children($context, null|string|Page $page = null, $perPage = 0, $pageNumber = 1)
    {
        $page = $this->page($context, $page);
        $query = $page?->children();

        return $perPage > 0 ? $query->paginate($perPage, ['*'], $pageNumber)->collect() : ($query->get() ?? collect());

    }
}