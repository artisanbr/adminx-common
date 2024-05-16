<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects\Config\Libraries;

use Adminx\Common\Models\Themes\Enums\ThemeAssetCompile;
use Adminx\Common\Models\Themes\Objects\Abstract\ThemeConfigLibraryAbstract;

class FontAwesomeLibrary extends ThemeConfigLibraryAbstract
{

    protected $attributes = [
        'enable' => true,
        'compile' => ThemeAssetCompile::Disabled->value,
        'version' => '6.4.2',
    ];

    protected array $included_js_files = [];

    protected array $included_css_files = [
        'css/all.min.css',
    ];

    protected function getCdnBaseUriAttribute(): string
    {
        return "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/{$this->version}/";
    }

}
