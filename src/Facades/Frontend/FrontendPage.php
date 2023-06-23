<?php

namespace Adminx\Common\Facades\Frontend;

use Illuminate\Support\Facades\Facade;

class FrontendPage extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'FrontendPageEngine';
    }
}