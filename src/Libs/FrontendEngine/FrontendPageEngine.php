<?php

namespace Adminx\Common\Libs\FrontendEngine;


use Adminx\Common\Exceptions\FrontendException;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Pages\PageModel;
use Adminx\Common\Models\Pages\Types\Manager\Facade\PageTypeManager;
use Adminx\Common\Models\Site;
use App\Http\Controllers\Frontend\Page\ArticlesController;
use App\Http\Controllers\Frontend\Page\PageModelController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class FrontendPageEngine extends FrontendEngineBase
{
    public function __construct(
        protected ?Page $currentPage = null
    )
    {
        if (!$this->currentSite) {
            $this->currentSite = FrontendSite::current();
        }
    }

    public function getHomePage(): Page
    {

        if (!$this->currentPage || !$this->currentPage->is_home) {

            $this->currentPage = Cache::remember($this->currentSite->relatedCacheName('home'), $this->cacheMinutes, function () {
                return $this->currentSite->home_page;
            });

        }

        return $this->currentPage;
    }

    public function getCurrentPage(): Page
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
    public function loadCurrentPageFromUrl($pageUrl = null, array|string|null $expectedTypes = null): ?Page
    {

        if ($this->currentSite->config->performance->enable_advanced_cache) {

            $this->currentPage = Cache::remember($this->currentSite->relatedCacheName($pageUrl ?? 'home'), $this->cacheMinutes * 60, fn() => $this->getPageByUrl($pageUrl, $expectedTypes));

        }
        else {
            $this->currentPage = $this->getPageByUrl($pageUrl, $expectedTypes);
        }

        return $this->currentPage;
    }


    /***
     *
     * @param array|string|null $expectedTypes Filter pages by types
     *
     * Encontrar página através da URL (slug ou public_id)
     */
    public function getPageByUrl(?string $pageUrl = null, array|string|null $expectedTypes = null): ?Page
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
        return $this->currentPage?->articles()->where('slug', $url)->orWhere('public_id', $url)->first();
    }

    /***
     * Encontrar PageModel da Página através da URL (slug ou public_id)
     */
    public function getPageModelByUrl(string $url): ?PageModel
    {
        return $this->currentPage?->page_models()->where('slug', $url)->orWhere('public_id', $url)->first() ?? $this->currentPage?->page_models()->whereNot('slug')->first();
    }

    public function getFirstInternalUrl($url): Article|PageModel|null
    {

        //Validar pelo tipo da página
        if ($this->currentPage) {

            if ($this->currentPage->can_use_articles) {
                $article = $this->getArticleByUrl($url);

                if ($article) {
                    return $article;
                }
            }

            if ($this->currentPage->page_models()->count()) {


                $pageModel = $this->getPageModelByUrl($url);
                if ($pageModel) {
                    return $pageModel;
                }


            }
        }

        return null;
    }

    public function getSecondInternalUrl($url)
    {

        dd($this->currentPage);

        //Validar pelo tipo da página
        if ($this->currentPage) {

            if ($this->currentPage->can_use_articles) {
                $article = $this->getArticleByUrl($url);

                if ($article) {
                    return $article;
                }
            }

            if ($this->currentPage->page_models()->count()) {


                $pageModel = $this->getPageModelByUrl($url);
                if ($pageModel) {
                    return $pageModel;
                }


            }
        }

        return null;
    }


    public function getModelController($model): string
    {
        return match (get_class($model)){
            Article::class => ArticlesController::class,
            PageModel::class => PageModelController::class,
        };
    }

    public function current()
    {
        return app('CurrentPage');
    }
}
