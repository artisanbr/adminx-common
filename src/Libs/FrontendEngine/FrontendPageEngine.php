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
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListItem;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Pages\Types\Manager\Facade\PageTypeManager;
use Adminx\Common\Models\Sites\SiteRoute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FrontendPageEngine extends FrontendEngineBase
{
    public function __construct(
        protected ?Page                                                                                      $currentPage = null,
        protected EloquentModelBase|Page|Article|CustomListItem|CustomList|null $firstModel = null,
        protected EloquentModelBase|Page|Article|CustomListItem|null                    $secondModel = null
    )
    {
        if (!$this->currentSite) {
            $this->currentSite = FrontendSite::current() ?? null;
        }
    }

    public function getHomePage(): ?Page
    {

        if ($this->currentSite && !$this->currentPage || !$this->currentPage->is_home) {

            $this->currentPage = Cache::remember($this->currentSite->relatedCacheName('home'), $this->cacheMinutes, function () {
                return $this->currentSite->home_page;
            });

        }

        return $this->currentPage;
    }

    public function getCurrentPage(): ?Page
    {
        return $this->currentPage ?? null;
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

        if ($this->currentSite?->config->performance->enable_advanced_cache ?? false) {

            $this->currentPage = Cache::remember($this->currentSite->relatedCacheName($pageUrl ?? 'home'), $this->cacheMinutes * 60, fn() => $this->getPageByUrl($pageUrl, $expectedTypes));

        }
        else {
            $this->currentPage = $this->getPageByUrl($pageUrl, $expectedTypes);
        }

        return $this->currentPage;
    }

    public function loadCurrentPageFromUrl(?String $slug = null): ?Page
    {

        if ($this->currentSite?->config->performance->enable_advanced_cache ?? false) {

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
        if ($this->currentSite && empty($pageUrl)) {
            return $this->currentSite->home_page;
        }

        //Se não for informada URL e a pagina atual não existir, ou não for a home, definir e retornar a home

        return $this->currentSite?->pages()->whereUrl($pageUrl)->first() ?? null;
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
            $pages = $pages->whereIn('type', $expectedPageTypes->keys()->toArray());

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


    public function getModelController($model): string
    {
        return match (get_class($model)) {
            Page::class => 'App\Http\Controllers\Frontend\Page\PagesController',
            Article::class => 'App\Http\Controllers\Frontend\Page\ArticlesController'
        };
    }

    public function current()
    {
        return app('CurrentPage');
    }
}
