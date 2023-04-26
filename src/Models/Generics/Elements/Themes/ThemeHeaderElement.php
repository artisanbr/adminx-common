<?php
namespace Adminx\Common\Models\Generics\Elements\Themes;

use Adminx\Common\Models\Generics\Elements\GenericElement;
use Adminx\Common\Models\Interfaces\HtmlModel;
use Adminx\Common\Models\Site;

class ThemeHeaderElement extends GenericElement
{
    public function buildAdvancedHtml(Site $site, HtmlModel $model, $viewTemporaryName = 'header-html'): string
    {
        return parent::buildAdvancedHtml($site, $model, $viewTemporaryName);
    }
}
