<?php

namespace Adminx\Common\Facades\Frontend;

use Illuminate\Support\Facades\Facade;

class FrontendHtml extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'FrontendHtmlEngine';
    }
}