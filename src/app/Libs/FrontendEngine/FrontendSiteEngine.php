<?php

namespace ArtisanBR\Adminx\Common\App\Libs\FrontendEngine;


use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use ArtisanBR\Adminx\Common\App\Models\Site;
use Illuminate\Support\Facades\Session;

class FrontendSiteEngine
{
    public static $siteSessionName = 'frontend_site';

    public static function isForwarderd(){
        return request()->server('HTTP_X_FORWARDED_HOST') || false;
    }

    public static function currentDomain(): array|string|null
    {
        //dd(request()->server('HTTP_X_FORWARDED_HOST'), request()->getHost());
        return request()->server('HTTP_X_FORWARDED_HOST') ?? request()->getHost() ?? null;
    }

    public static function current(): Site|null
    {

        Session::start();
        $domain = self::currentDomain();
        //header("Host: {$domain}");
        request()->headers->set('Host', [$domain]);

        //dd($domain);


        $sessionSite = Session::get(self::$siteSessionName);
        if ($sessionSite && Str::contains($sessionSite->url, $domain)) {
            //Verificar se é diferente do atual
            $site = $sessionSite;
        }
        else {

            $site = Site::where('url', Str::of($domain)->replace('www.', ''))->first();

            Session::put(self::$siteSessionName, $site);

            Session::save();

            //request()->session()->put(self::$siteSessionName, $site);
        }

        return $site;
    }
}
