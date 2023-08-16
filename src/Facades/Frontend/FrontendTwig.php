<?php

namespace Adminx\Common\Facades\Frontend;

use Illuminate\Support\Facades\Facade;

class FrontendTwig extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'FrontendTwigEngine';
    }
}