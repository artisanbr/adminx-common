<?php
namespace Adminx\Common\Models\Generics\Elements\Themes;

use Adminx\Common\Models\Bases\Generic\GenericHtmlBase;
use Adminx\Common\Models\Interfaces\HtmlModel;
use Adminx\Common\Models\Site;

class ThemeFooter extends GenericHtmlBase
{
    public function builtHtml(Site $site, HtmlModel $model, $viewTemporaryName = 'footer-html'): string
    {
        return parent::builtHtml($site, $model, $viewTemporaryName);
    }
}
