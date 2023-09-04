<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Templates\Global\Manager\Facade;

use Illuminate\Support\Facades\Facade;

class GlobalTemplateManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'GlobalTemplateManagerEngine';
    }
}