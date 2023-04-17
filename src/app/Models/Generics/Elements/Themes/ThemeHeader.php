<?php
namespace ArtisanBR\Adminx\Common\App\Models\Generics\Elements\Themes;

use ArtisanBR\Adminx\Common\App\Models\Bases\Generic\GenericHtmlBase;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\HtmlModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\WidgeteableModel;
use ArtisanBR\Adminx\Common\App\Models\Site;

class ThemeHeader extends GenericHtmlBase
{
    public function builtHtml(Site $site, WidgeteableModel|HtmlModel $model, $viewTemporaryName = 'header-html'): string
    {
        return parent::builtHtml($site, $model, $viewTemporaryName);
    }
}
