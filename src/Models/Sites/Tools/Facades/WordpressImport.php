<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Sites\Tools\Facades;

use Illuminate\Support\Facades\Facade;

class WordpressImport extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'WordpressImportTools';
    }
}