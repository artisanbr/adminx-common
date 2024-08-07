<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects\Config\Libraries;

use Adminx\Common\Models\Themes\Enums\ThemeAssetCompile;
use Adminx\Common\Models\Themes\Objects\Abstract\ThemeConfigLibraryAbstract;

class JQueryUiLibrary extends ThemeConfigLibraryAbstract
{

    protected $attributes = [
        'enable'  => true,
        'compile' => ThemeAssetCompile::Js->value,
        'version' => '1.13.2',
        'strict'  => false,
    ];

    protected array $included_js_files = [
        'jquery-ui.min.js',
    ];

    protected array $included_css_files = [
        'themes/base/jquery-ui.min.css',
    ];

    protected function getCdnBaseUriAttribute(): string
    {
        return "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/{$this->version}/";
    }

}
