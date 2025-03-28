<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects\Config\Libraries;

use Adminx\Common\Models\Themes\Enums\ThemeAssetCompile;
use Adminx\Common\Models\Themes\Objects\Abstract\ThemeConfigLibraryAbstract;

class JQueryLibrary extends ThemeConfigLibraryAbstract
{

    protected $attributes = [
        'enable' => true,
        'compile' => ThemeAssetCompile::All->value,
        'version' => '3.6.3',
    ];

    protected array $included_js_files = [
        'jquery.min.js',
    ];

    protected array $included_css_files = [];

    protected function getCdnBaseUriAttribute(): string
    {
        return "https://cdnjs.cloudflare.com/ajax/libs/jquery/{$this->version}/";

    }

}
