<?php

namespace Adminx\Common\Models\Objects\Themes;

use Adminx\Common\Models\Bases\Generic\GenericHtmlBase;
use Adminx\Common\Models\Interfaces\HtmlModel;
use Adminx\Common\Models\Objects\Abstract\AbstractHtmlObject;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\Theme;

class ThemeHeaderObject extends AbstractHtmlObject
{

    public function builtHtml(Theme $theme, FrontendBuildObject $frontendBuild = new FrontendBuildObject(), $viewTemporaryName = 'header-html'): string
    {
        return parent::builtHtml($theme, $frontendBuild, $viewTemporaryName);
    }
}
