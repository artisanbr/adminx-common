<?php

namespace Adminx\Common;

use Adminx\Common\Libs\Support\Str;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AdminxCommonServiceProvider extends ServiceProvider
{

    private $config_path = __DIR__ . '/../config/';
    private $routes_path = __DIR__ . '/../routes/';
    private $views_path  = __DIR__ . '/../resources/views/';

    private $config_files = [
        'tracker',
        'visitor',
        'location',
        'adminx/app',
        'adminx/data-sources',
        'adminx/defines',
        'adminx/pages',
        'adminx/themes',
        'adminx/elements/forms',
        'adminx/elements/widgets',
        'frontend/components',
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //Configs
        foreach ($this->config_files as $config_file) {
            $this->mergeConfigFrom(
                $this->config_path . $config_file . '.php', Str::replaceNative('/', '.', $config_file)
            );
        }


    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $commonViewPath = $this->views_path.'common';
        $frontendViewPath = $this->views_path.'frontend';
        //Common Views
        $this->loadViewsFrom($commonViewPath, 'adminx-common');
        $this->loadViewsFrom($frontendViewPath, 'adminx-frontend');

        Blade::anonymousComponentPath($commonViewPath.'/components', 'common');
        Blade::anonymousComponentPath($frontendViewPath.'/components', 'frontend');

        Blade::componentNamespace('Adminx\Common\View\Common\Components', 'common');
        Blade::componentNamespace('Adminx\Common\View\Frontend\Components', 'frontend');

        //Common Routes
        Route::as('common.')
             ->namespace('Adminx\Common\Http\Controllers')
             ->group(function () {

                 Route::prefix('api/v1/common')
                      ->as('api.')
                      ->middleware('api.internal')
                      ->namespace('API')
                      ->group($this->routes_path.'api.php');
             });
    }
}
