<?php

namespace ArtisanBR\Adminx\Common\App\Libs\FrontendEngine;


use ArtisanBR\Adminx\Common\App\Facades\FrontendSite;
use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use ArtisanBR\Adminx\Common\App\Models\Page;
use ArtisanBR\Adminx\Common\App\Models\Site;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class FrontendPageEngine extends FrontendEngineBase
{
    public Page|null $currentPage = null;

    public function __construct()
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

    public function loadFromUrl($pageUrl = null): Page|null
    {

        //Se não for informada URL e a pagina atual não existir, ou não for a home, definir e retornar a home
        if (!$pageUrl) {
            return $this->getHomePage();
        }

        //Se a página não estiver definida OU se a pageURL for informada e for diferente do esperado na pagina salva
        if (!$this->currentPage || ($pageUrl && $this->currentPage->slug !== $pageUrl && $this->currentPage->public_id !== $pageUrl)) {

            $this->currentPage = Cache::remember($this->currentSite->relatedCacheName($pageUrl), $this->cacheMinutes, function () use ($pageUrl) {
                return $this->getPageByUrl($pageUrl);
            });
        }

        return $this->currentPage;
    }

    public function getPageByUrl($pageUrl): Page|null
    {
        $page = $this->currentSite->pages->firstWhere('slug', $pageUrl);

        if (!$page) {
            $page = $this->currentSite->pages->firstWhere('public_id', $pageUrl);
        }

        if ($page) {
            $page->load($page->buildSchema);
        }

        return $page;
    }

    public function current()
    {
        return app('CurrentPage');
    }
}
