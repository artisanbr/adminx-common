<?php

namespace ArtisanBR\Adminx\Common\App\Providers;

use ArtisanBR\Adminx\Common\App\Facades\FrontendSiteEngine;
use ArtisanBR\Adminx\Common\App\Libs\FrontendEngine\FrontendSiteEngineHelper;
use ArtisanBR\Adminx\Common\App\Models\Site;
use Illuminate\Support\ServiceProvider;

class FrontendServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {


        $this->app->singleton(FrontendSiteEngineHelper::class, function () {
            return new FrontendSiteEngineHelper();
        });

        $this->app->bind('FrontendSiteEngine', function () {
            return app()->make(FrontendSiteEngineHelper::class);
        });


        $this->app->singleton(Site::class, function () {
            return FrontendSiteEngine::current();
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
