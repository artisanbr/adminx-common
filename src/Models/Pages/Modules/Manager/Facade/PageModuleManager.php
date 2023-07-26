<?php

namespace Adminx\Common\Models\Pages\Modules\Manager\Facade;

use Illuminate\Support\Facades\Facade;

class PageModuleManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'PageModuleManagerEngine';
    }
}