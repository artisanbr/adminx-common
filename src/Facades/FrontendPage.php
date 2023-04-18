<?php

namespace Adminx\Common\Facades;

use Illuminate\Support\Facades\Facade;

class FrontendPage extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'FrontendPageEngine';
    }
}