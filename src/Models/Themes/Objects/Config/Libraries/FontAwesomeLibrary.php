<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects\Config\Libraries;

use Adminx\Common\Models\Themes\Objects\Abstract\ThemeConfigLibraryAbstract;

class FontAwesomeLibrary extends ThemeConfigLibraryAbstract
{

    protected $attributes = [
        'enable' => true,
        'version' => '6.4.2',
    ];

    protected array $js_files = [];

    protected array $css_files = [
        'css/all.min.css',
    ];

    protected function getCdnBaseUriAttribute(): string
    {
        return "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/{$this->version}/";
    }

}
