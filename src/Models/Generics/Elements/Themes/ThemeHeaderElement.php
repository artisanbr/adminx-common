<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Elements\Themes;

use Adminx\Common\Models\Generics\Elements\GenericElement;
use Adminx\Common\Models\Interfaces\FrontendModel;
use Adminx\Common\Models\Sites\Site;

class ThemeHeaderElement extends GenericElement
{
    public function buildAdvancedHtml(Site $site, FrontendModel $model, $viewTemporaryName = 'header-html'): string
    {
        return parent::buildAdvancedHtml($site, $model, $viewTemporaryName);
    }
}
