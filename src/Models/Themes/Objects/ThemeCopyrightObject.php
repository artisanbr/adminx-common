<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects;

use Adminx\Common\Models\Objects\Abstract\AbstractHtmlObject;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Sites\Site;
use Adminx\Common\Models\Themes\Theme;

class ThemeCopyrightObject extends AbstractHtmlObject
{

    public function builtHtml(Theme $theme, FrontendBuildObject $frontendBuild = new FrontendBuildObject(), $viewTemporaryName = 'header-copyright'): string
    {
        return parent::builtHtml($theme, $frontendBuild, $viewTemporaryName);
    }
}
