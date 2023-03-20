<?php

namespace ArtisanBR\Adminx\Common\App\Models\Interfaces;

use ArtisanBR\Adminx\Common\App\Libs\FrontendEngine\AdvancedHtmlEngine;

/**
 * @property string $html
 * @methos cacheHtml()
 */
interface HtmlModel
{
    public function htmlBuilder(): AdvancedHtmlEngine;

    public function builtHtml(): string;

    public function flushHtmlCache($save = false);
}
