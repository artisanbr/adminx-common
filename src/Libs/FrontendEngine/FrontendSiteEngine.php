<?php

namespace Adminx\Common\Libs\FrontendEngine;


use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Site;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class FrontendSiteEngine extends FrontendEngineBase
{
    //public static $siteSessionName = 'frontend_site';


    public function __construct()
    {
        $this->currentDomain();

        $this->cacheName = Str::of($this->currentDomain)->replace('.', '_');
    }

    public function loadCurrent($modelCache = true): Site|null
    {

        if (Auth::check() && Auth::user()->site_id) {
            return Auth::user()->site;
        }

        //Pegar dominio atual e remover o WWW
        //header("Host: {$this->currentDomain}");
        //request()->headers->set('Host', [$this->currentDomain]);
        //dd($this->currentDomain);

        //Se o site ainda não foi carregado, ou estiver com outro endereço, carregar o atual via cache.
        if (!$this->currentSite || $this->currentSite->url !== $this->currentDomain) {

            $this->currentSite = $this->getCachedSiteByDomain($modelCache);
        }


        $this->refreshCache();

        return $this->currentSite;
    }

    public function getCachedSiteByDomain($modelCache)
    {

        return Cache::remember($this->cacheName, $this->cacheMinutes, function () use ($modelCache) {
            return $this->getSiteByDomain($modelCache);
        });
    }

    public function getSiteByDomain($modelCache = true)
    {
        $siteQuery = Site::where('url', $this->currentDomain);

        if (!$modelCache) {
            $siteQuery = $siteQuery->disableCache();
        }

        return $siteQuery->first();
    }

    public function current()
    {
        return app('CurrentSite');
    }

    public function refreshCache()
    {
        if ($this->currentSite) {
            $liveSiteCheck = DB::table('sites')->where('id', $this->currentSite->id)->select(['updated_at'])->first();

            $liveUpdateAt = ($liveSiteCheck->updated_at ?? false) ? Carbon::parse($liveSiteCheck->updated_at ?? null) : $this->currentSite->updated_at;

            if (!$liveUpdateAt->equalTo($this->currentSite->updated_at)) {
                //O site foi reconfigurado recentemente
                Cache::forget($this->cacheName);
                $this->currentSite = $this->getCachedSiteByDomain(false);

                //Limpar cache se necessário:
                $cacheBus = [];
                if ($this->currentSite->config->cache->clear_model) {

                    $cacheBus[] = function () {
                        Artisan::call('modelCache:clear');
                    };

                    $this->currentSite->config->cache->clear_model = false;

                }

                if ($this->currentSite->config->cache->clear_view) {

                    $cacheBus[] = function () {
                        Artisan::call('cache:clear');
                        Artisan::call('view:clear');
                    };

                    $this->currentSite->config->cache->clear_view = false;

                }

                if (count($cacheBus)) {
                    Bus::chain($cacheBus)->dispatch();
                    $this->currentSite->save();
                }
            }
        }

        return $this->currentSite;
    }
}
