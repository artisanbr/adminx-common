<?php

namespace ArtisanBR\Adminx\Common\App\Providers;

use ArtisanBR\Adminx\Common\App\Facades\FrontendSite;
use ArtisanBR\Adminx\Common\App\Libs\FrontendEngine\FrontendSiteEngine;
use ArtisanBR\Adminx\Common\App\Models\Site;
use Illuminate\Support\ServiceProvider;

class FrontendSiteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {


        //Singleton: FrontendSite
        $this->app->singleton(FrontendSiteEngine::class, function () {
            return new FrontendSiteEngine();
        });
        //Bind: FrontendSite
        $this->app->bind('FrontendSiteEngine', function () {
            return app()->make(FrontendSiteEngine::class);
        });


        $this->app->singleton('CurrentSite', function () {
            return FrontendSite::loadCurrent();
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
