<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Facades\FileManager;

use Illuminate\Support\Facades\Facade;

class FileManager extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \Adminx\Common\Libs\FileManager\FileManager::class;
    }
}