<?php

namespace ArtisanBR\Adminx\Common\App\Providers;

use ArtisanBR\Adminx\Common\App\Facades\FrontendHtml;
use ArtisanBR\Adminx\Common\App\Libs\FrontendEngine\FrontendHtmlEngine;
use ArtisanBR\Adminx\Common\App\Libs\FrontendEngine\FrontendSiteEngine;
use Illuminate\Support\ServiceProvider;

class FrontendHtmlServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {


        //Singleton: FrontendSite
        $this->app->singleton(FrontendHtmlEngine::class, function () {
            return new FrontendHtmlEngine();
        });
        //Bind: FrontendSite
        $this->app->bind('FrontendHtmlEngine', function () {
            return app()->make(FrontendHtmlEngine::class);
        });

        $this->app->singleton('FrontendHtml', function () {
            return app('FrontendHtmlEngine');
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
