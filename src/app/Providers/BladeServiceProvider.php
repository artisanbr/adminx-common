<?php

namespace ArtisanBR\Adminx\Common\App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //Namespaces
        //Frontend Components
        Blade::anonymousComponentNamespace('adminx-common::components', 'common');
        Blade::anonymousComponentNamespace('adminx-frontend::components', 'frontend');


    }
}
