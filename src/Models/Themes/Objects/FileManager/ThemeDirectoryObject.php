<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects\FileManager;

use Adminx\Common\Models\Themes\Objects\FileManager\Abstract\ThemeFileManagerObject;

class ThemeDirectoryObject extends ThemeFileManagerObject
{
    protected $fillable = [
        'id',
        'name',
        'path',
        'theme_path',
        'url',
        'uri',
        'cdn_url',
        'cdn_uri',
    ];

    protected $casts = [
        'id'         => 'string',
        'name'       => 'string',
        'path'       => 'string',
        'theme_path' => 'string',
        'url'        => 'string',
        'uri'        => 'string',
        'cdn_url'    => 'string',
        'cdn_uri'    => 'string',
    ];

}
