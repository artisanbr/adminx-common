<?php

namespace Adminx\Common\Models\Generics\Elements\Themes;


use Adminx\Common\Models\Generics\Elements\GenericElement;
use Adminx\Common\Models\Interfaces\WidgeteableModel;
use Adminx\Common\Models\Site;

class ThemeFooterElement extends GenericElement
{
    public function buildAdvancedHtml(Site $site, WidgeteableModel $model, $viewTemporaryName = 'footer-hmtl'): string
    {
        return parent::buildAdvancedHtml($site, $model, $viewTemporaryName);
    }
}
