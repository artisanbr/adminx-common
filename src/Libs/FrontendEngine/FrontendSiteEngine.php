<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\FrontendEngine;


use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Sites\Site;
use Adminx\Common\Models\Themes\Theme;
use Illuminate\Support\Facades\Cache;

class FrontendSiteEngine extends FrontendEngineBase
{
    //public static $siteSessionName = 'frontend_site';


    public function __construct()
    {
        $this->currentDomain();

        $this->cacheName = Str::of($this->currentDomain)->replace('.', '_');
    }

    public function loadCurrent(): Site|null
    {

        //Se o site ainda não foi carregado, ou estiver com outro endereço, carregar o atual via cache.
        if (!$this->currentSite || $this->currentSite->url !== $this->currentDomain) {

            $this->currentSite = $this->getCachedSiteByDomain();
        }


        //$this->refreshCache();

        return $this->currentSite;
    }

    public function getCachedSiteByDomain()
    {
        $chaceName = $this->getChacheName('siteModel');
        $cachedSite = Cache::get($chaceName);

        if (!$cachedSite || get_class($cachedSite) !== Site::class || $cachedSite->url !== $this->currentDomain) {
            $cachedSite = Cache::remember($chaceName, $this->cacheMinutes, fn() => $this->getSiteByDomain());
            $this->getCachedSiteTheme();
        }

        return $cachedSite;
    }

    public function getCachedSiteTheme()
    {
        $chaceName = $this->getChacheName('themeModel');
        $cachedTheme = Cache::get($chaceName);

        if (!$cachedTheme || get_class($cachedTheme) !== Theme::class || $cachedTheme->id !== $this->currentSite->theme_id) {
            $cachedTheme = Cache::remember($chaceName, $this->cacheMinutes, fn() => $this->currentSite()->theme);
        }

        return $cachedTheme;
    }

    public function getSiteByDomain(): ?Site
    {
        /*if (!$modelCache) {
            $siteQuery = $siteQuery->disableCache();
        }*/

        return Site::where('url', $this->currentDomain)->first();
    }

    public function current()
    {
        return app('CurrentSite');
    }

    public function currentSite()
    {
        return app('CurrentSite');
    }
    
    public function currentTheme()
    {
        //Se o site ainda não foi carregado, ou estiver com outro endereço, carregar o atual via cache.
        if (!$this->currentTheme || $this->currentTheme->id !== $this->currentSite()->theme_id) {

            $this->currentTheme = $this->getCachedSiteTheme();
        }


        //$this->refreshCache();

        return $this->currentTheme;
    }

    public function getChacheName($name): string
    {
        return $this->cacheName . '__' . $name;
    }
}
