<?php

namespace Adminx\Common\Libs\FrontendEngine;


class FrontendUtils
{

    public static function route($name, $parameters = [], $absolute = true): string{
        return ($absolute ? FrontendSiteEngine::current()->uri : '') . route($name, $parameters, false);
    }

}
