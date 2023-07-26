<?php

namespace Adminx\Common\Models\Pages\Types\Manager\Facade;

use Illuminate\Support\Facades\Facade;

class PageTypeManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'PageTypeManagerEngine';
    }
}