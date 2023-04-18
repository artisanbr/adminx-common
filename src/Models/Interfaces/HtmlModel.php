<?php

namespace Adminx\Common\Models\Interfaces;

use Adminx\Common\Libs\FrontendEngine\AdvancedHtmlEngine;

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
