<?php

namespace ArtisanBR\Adminx\Common\App\Libs\FrontendEngine;


class FrontendHelper
{

    public static function route($name, $parameters = [], $absolute = true): string{
        return ($absolute ? FrontendSiteEngine::current()->uri : '') . route($name, $parameters, false);
    }

}
