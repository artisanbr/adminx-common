<?php

namespace Adminx\Common\Providers;

use Adminx\Common\Facades\Frontend\FrontendHtml;
use Adminx\Common\Libs\FileManager\FileUploadManager;
use Adminx\Common\Libs\FrontendEngine\FrontendHtmlEngine;
use Adminx\Common\Libs\FrontendEngine\FrontendSiteEngine;
use Illuminate\Support\ServiceProvider;

class FileManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        //region FileUpload

        /*$this->app->singleton(FileUploadManager::class, function () {
            return new FileUploadManager();
        });

        $this->app->bind('FileUploadManager', function () {
            return app()->make(FileUploadManager::class);
        });

        $this->app->singleton('FileUploadManager', function () {
            return app('FileUploadManager');
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
