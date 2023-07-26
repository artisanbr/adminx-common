<?php

namespace Adminx\Common\Models\Templates\Global\Manager\Facade;

use Illuminate\Support\Facades\Facade;

class PageTemplateManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'PageTemplateManagerEngine';
    }
}