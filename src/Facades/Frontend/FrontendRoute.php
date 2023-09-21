<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Facades\Frontend;

use Illuminate\Support\Facades\Facade;

class FrontendRoute extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'FrontendRouteTools';
    }
}