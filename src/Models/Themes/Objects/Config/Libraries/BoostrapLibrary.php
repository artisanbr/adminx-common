<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects\Config\Libraries;

use Adminx\Common\Models\Themes\Enums\ThemeAssetCompile;
use Adminx\Common\Models\Themes\Objects\Abstract\ThemeConfigLibraryAbstract;

class BoostrapLibrary extends ThemeConfigLibraryAbstract
{

    protected $attributes = [
        'enable' => false,
        'compile' => ThemeAssetCompile::Js->value,
        'version' => '5.3.2',
        'strict' => false,
    ];

    protected array $included_js_files = [
        'js/bootstrap.bundle.min.js',
    ];


    protected array $included_css_files = [
        'css/bootstrap.min.css',
    ];

    protected function getCdnBaseUriAttribute(): string
    {
        return "https://cdn.jsdelivr.net/npm/bootstrap@{$this->version}/dist/";

    }

}
