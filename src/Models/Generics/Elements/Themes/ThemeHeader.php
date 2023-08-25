<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Elements\Themes;

use Adminx\Common\Models\Bases\Generic\GenericHtmlBase;
use Adminx\Common\Models\Interfaces\HtmlModel;
use Adminx\Common\Models\Sites\Site;

class ThemeHeader extends GenericHtmlBase
{
    public function builtHtml(Site $site, HtmlModel $model, $viewTemporaryName = 'header-html'): string
    {
        return parent::builtHtml($site, $model, $viewTemporaryName);
    }
}
