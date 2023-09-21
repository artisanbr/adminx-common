<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Providers\Frontend;

use Adminx\Common\Libs\FrontendEngine\FrontendRouteTools;
use Illuminate\Support\ServiceProvider;

class FrontendRouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {


        //Singleton: FrontendSite
        $this->app->singleton(FrontendRouteTools::class, function () {
            return new FrontendRouteTools();
        });
        //Bind: FrontendSite
        $this->app->bind('FrontendRouteTools', function () {
            return app()->make(FrontendRouteTools::class);
        });


        /*$this->app->singleton('CurrentSite', function () {
            return FrontendSite::loadCurrent();
        });*/
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
