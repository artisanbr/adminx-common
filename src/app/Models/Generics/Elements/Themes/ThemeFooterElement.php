<?php

namespace ArtisanBR\Adminx\Common\App\Models\Generics\Elements\Themes;


use ArtisanBR\Adminx\Common\App\Models\Generics\Elements\GenericElement;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\WidgeteableModel;
use ArtisanBR\Adminx\Common\App\Models\Site;

class ThemeFooterElement extends GenericElement
{
    public function buildAdvancedHtml(Site $site, WidgeteableModel $model, $viewTemporaryName = 'footer-hmtl'): string
    {
        return parent::buildAdvancedHtml($site, $model, $viewTemporaryName);
    }
}
