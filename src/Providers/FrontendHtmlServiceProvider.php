<?php

namespace Adminx\Common\Providers;

use Adminx\Common\Facades\FrontendHtml;
use Adminx\Common\Libs\FrontendEngine\FrontendHtmlEngine;
use Adminx\Common\Libs\FrontendEngine\FrontendSiteEngine;
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
