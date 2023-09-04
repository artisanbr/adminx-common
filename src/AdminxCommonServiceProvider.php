<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common;

use Adminx\Common\Libs\Support\Str;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AdminxCommonServiceProvider extends ServiceProvider
{

    private $config_path    = __DIR__ . '/../config/';
    private $routes_path    = __DIR__ . '/../routes/';
    private $resources_path = __DIR__ . '/../resources/';
    private $views_path     = __DIR__ . '/../resources/views/';

    private $config_files = [
        'location',
        'files',

        'common/app',
        'common/morphs',

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
        foreach (File::allFiles($this->config_path) as $file) {

            if ($file->isFile() && $file->getExtension() === 'php') {
                $relativePathName = Str::replaceNative('/', '.', $file->getRelativePathname());

                $relativePathName = Str::replaceNative('.php', '', $relativePathName);

                $this->mergeConfigFrom(
                    $file->getPathname(), $relativePathName
                );
            }


        }

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $commonViewPath = $this->views_path . 'common';
        $frontendViewPath = $this->views_path . 'frontend';
        $templatesViewPath = $this->resources_path . 'templates';
        //Common Views
        $this->loadViewsFrom($commonViewPath, 'common');
        $this->loadViewsFrom($frontendViewPath, 'common-frontend');
        $this->loadViewsFrom($templatesViewPath, 'common-templates');
        $this->loadViewsFrom($frontendViewPath . '/pages/templates', 'pages-templates');

        Blade::anonymousComponentPath($commonViewPath . '/components', 'common');
        Blade::anonymousComponentPath($frontendViewPath . '/components', 'frontend');

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
                      ->group($this->routes_path . 'api.php');
             });
    }
}
