<?php

namespace Adminx\Common\Providers\Frontend;

use Adminx\Common\Libs\FrontendEngine\FrontendHtmlEngine;
use Adminx\Common\Libs\FrontendEngine\Twig\FrontendTwigEngine;
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


        //Singleton: FrontendSite
        $this->app->singleton(FrontendTwigEngine::class, function () {
            return new FrontendTwigEngine();
        });
        //Bind: FrontendSite
        $this->app->bind('FrontendTwigEngine', function () {
            return app()->make(FrontendTwigEngine::class);
        });

        $this->app->singleton('FrontendTwig', function () {
            return app('FrontendTwigEngine');
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
