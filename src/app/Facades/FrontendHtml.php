<?php

namespace ArtisanBR\Adminx\Common\App\Facades;

use Illuminate\Support\Facades\Facade;

class FrontendHtml extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'FrontendHtmlEngine';
    }
}