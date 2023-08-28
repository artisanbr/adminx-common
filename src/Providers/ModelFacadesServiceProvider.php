<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Providers;

use Adminx\Common\Models\Sites\Tools\Import\FtpMediaImportTools;
use Adminx\Common\Models\Sites\Tools\Import\WordpressImportTools;
use Illuminate\Support\ServiceProvider;

class ModelFacadesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        //region Site Import Tools

        //FTP Media
        $this->app->singleton(FtpMediaImportTools::class, function () {
            return new FtpMediaImportTools();
        });
        $this->app->bind('FtpMediaImportTools', function () {
            return app()->make(FtpMediaImportTools::class);
        });

        //Wordpress
        $this->app->singleton(WordpressImportTools::class, function () {
            return new WordpressImportTools();
        });

        $this->app->bind('WordpressImportTools', function () {
            return app()->make(WordpressImportTools::class);
        });
/*
        $this->app->singleton('FileUploadManager', function () {
            return app('FtpMediaImportTools');
        });*/

        //endregion

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
