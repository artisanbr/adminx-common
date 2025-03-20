<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\FrontendEngine;


use Adminx\Common\Exceptions\FrontendException;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\CustomLists\Abstract\CustomListAbstract;
use Adminx\Common\Models\CustomLists\Abstract\CustomListItemAbstract\CustomListItemAbstract;
use Adminx\Common\Models\CustomLists\CustomListItem;
use Adminx\Common\Models\Interfaces\FrontendModel;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Pages\PageInternal;
use Adminx\Common\Models\Pages\Types\Manager\Facade\PageTypeManager;
use Adminx\Common\Models\Sites\SiteRoute;
use App\Http\Controllers\Frontend\Page\ArticlesController;
use App\Http\Controllers\Frontend\Page\PageInternalController;
use App\Http\Controllers\Frontend\Page\PagesController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FrontendPageEngine extends FrontendEngineBase
{
    public function __construct(
        protected ?Page                                                                                      $currentPage = null,
        protected EloquentModelBase|Page|Article|CustomListItemAbstract|CustomListAbstract|PageInternal|null $firstModel = null,
        protected EloquentModelBase|Page|Article|CustomListItemAbstract|PageInternal|null                    $secondModel = null
    )
    {
        if (!$this->currentSite) {
            $this->currentSite = FrontendSite::current();
        }
    }

    public function getHomePage(): ?Page
    {

        if (!$this->currentPage || !$this->currentPage->is_home) {

            $this->currentPage = Cache::remember($this->currentSite->relatedCacheName('home'), $this->cacheMinutes, function () {
                return $this->currentSite->home_page;
            });

        }

        return $this->currentPage;
    }

    public function getCurrentPage(): ?Page
    {
        return $this->currentPage;
    }

    public function setCurrentPage(Page $page): Page
    {
        return $this->currentPage = $page;
    }

    /**
     * Carregar e definir a página atual baseado em um URL (Slug ou Public_id)
     *
     * @param null              $pageUrl
     * @param array|string|null $expectedTypes Filter pages by types
     *
     * @return Page|null
     * @throws FrontendException
     */
    public function loadCurrentPageFromUrlByTypes($pageUrl = null, array|string|null $expectedTypes = null): ?Page
    {

        if ($this->currentSite->config->performance->enable_advanced_cache) {

            $this->currentPage = Cache::remember($this->currentSite->relatedCacheName($pageUrl ?? 'home'), $this->cacheMinutes * 60, fn() => $this->getPageByUrl($pageUrl, $expectedTypes));

        }
        else {
            $this->currentPage = $this->getPageByUrl($pageUrl, $expectedTypes);
        }

        return $this->currentPage;
    }

    public function loadCurrentPageFromUrl(?String $slug = null): ?Page
    {

        if ($this->currentSite->config->performance->enable_advanced_cache) {

            $this->currentPage = Cache::remember($this->currentSite->relatedCacheName($slug ?? 'home'), $this->cacheMinutes * 60, fn() => $this->getPageByUrl($slug));

        }
        else {
            $this->currentPage = $this->getPageByUrl($slug);
        }

        return $this->currentPage;
    }


    /***
     *
     * @param string|null $pageUrl
     *
     * @return Page|null
     */
    public function getPageByUrl(?string $pageUrl = null): ?Page
    {


        //Se não for informada URL retornar a home
        if (empty($pageUrl)) {
            return $this->currentSite->home_page;
        }

        //Se não for informada URL e a pagina atual não existir, ou não for a home, definir e retornar a home

        return $this->currentSite->pages()->whereUrl($pageUrl)->first();
    }

    public function getPageByUrlByTypes(?string $pageUrl = null, array|string|null $expectedTypes = null): ?Page
    {


        //Se não for informada URL retornar a home
        if (empty($pageUrl)) {
            return $this->currentSite->home_page;
        }

        //Se não for informada URL e a pagina atual não existir, ou não for a home, definir e retornar a home
        $pages = $this->currentSite->pages()->where(static function ($query) use ($pageUrl) {
            $query->where('slug', $pageUrl)->orWhere('public_id', $pageUrl);
        });


        if ($expectedTypes) {
            $expectedPageTypes = PageTypeManager::whereCanUseAnyModule($expectedTypes);
            $pages = $pages->whereIn('type_name', $expectedPageTypes->keys()->toArray());

            $typesCollection = Collection::wrap($expectedTypes);
            /*if ($typesCollection->contains('articles')) {
                $pages->with(['last_articles']);
            }*/
        }

        $page = $pages->first();

        /*if ($page) {
            $page->load((new Page())->buildSchema);
        }*/

        return $page;
    }

    /***
     * Encontrar Artigo da Página através da URL (slug ou public_id)
     */
    public function getArticleByUrl(string $url): ?Article
    {
        return $this->currentPage?->articles()->whereUrl($url)->first();
    }

    public function getSiteRouteByUrl(?string $url): ?SiteRoute
    {
        return FrontendSite::current()->routes()->where('url', $url)->first();
    }

    /***
     * Encontrar PageInternal da Página através da URL (slug ou public_id)
     */
    public function getPageInternalByUrl(string $url): ?PageInternal
    {
        return $this->currentPage?->page_internals()->whereUrl('slug', $url)->first() ?? $this->currentPage?->page_internals()->whereNot('slug')->first();
    }

    public function getFirstInternalUrl($url): Article|PageInternal|null
    {

        //Validar pelo tipo da página
        if ($this->currentPage) {

            return $this->getInternalModel($url, $this->currentPage);

            if ($this->currentPage->can_use_articles) {
                $article = $this->getArticleByUrl($url);

                if ($article) {
                    $this->firstModel = $article;

                    return $article;
                }
            }

            if ($this->currentPage->page_internals()->count()) {


                $pageInternal = $this->getPageInternalByUrl($url);
                if ($pageInternal) {
                    $this->firstModel = $pageInternal;

                    return $pageInternal;
                }


            }
        }

        return null;
    }

    public function getInternalModel($url, Article|PageInternal|FrontendModel|EloquentModelBase|Page $mainModel): Article|PageInternal|FrontendModel|CustomListItem|null
    {

        //Validar pelo tipo da página

        if ((@$mainModel->can_use_articles ?? false) && method_exists($mainModel, 'articles') && ($article = $mainModel->articles()->whereUrl($url)->first()) && $article?->id) {
            $this->firstModel = $article;

            return $article;
        }

        //Custom List Item
        if (get_class($mainModel) === PageInternal::class && method_exists($mainModel->model, 'items')) {

            $customList = $mainModel->model;

            return $customList->items()->where('slug', $url)->orWhere('public_id', $url)->first();
        }

        //$pageInternal = $mainModel->page_internals()->whereUrl($url)->first();
        if (get_class($mainModel) === Page::class && ($pageInternal = $mainModel->page_internals()->whereUrl($url)->first() ?? $mainModel->page_internals()->whereNot('slug')->first()) && $pageInternal?->id) {
            $this->firstModel = $pageInternal;

            return $pageInternal;
        }

        return null;
    }


    public function getModelController($model): string
    {
        return match (get_class($model)) {
            Page::class => PagesController::class,
            Article::class => ArticlesController::class,
            PageInternal::class => PageInternalController::class
        };
    }

    public function current()
    {
        return app('CurrentPage');
    }
}
