<?php

namespace Adminx\Common\Facades\FileManager;

use Adminx\Common\Libs\FileManager\FileUploadManager;
use Illuminate\Support\Facades\Facade;

class FileUpload extends Facade
{

    protected static function getFacadeAccessor()
    {
        return FileUploadManager::class;
    }
}