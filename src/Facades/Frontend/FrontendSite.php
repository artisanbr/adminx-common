<?php

namespace Adminx\Common\Facades\Frontend;

use Illuminate\Support\Facades\Facade;

class FrontendSite extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'FrontendSiteEngine';
    }
}