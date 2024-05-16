<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Assets\Code;

use Adminx\Common\Models\Objects\Frontend\Assets\Abstract\AbstractFrontendAssetCodeObject;
use MatthiasMullie\Minify\JS;

class FrontendJsAssetsCode extends AbstractFrontendAssetCodeObject
{

    public function minify(): static
    {
        $this->raw_minify = (new JS())->add($this->raw)->minify();

        return $this;
    }

    //region ATTRIBUTES

    //region GETS

    protected function getRawHtmlAttribute(): string
    {
        $raw = parent::getRawHtmlAttribute();
        if (!empty($raw)) {
            return "<script type='text/javascript'>{$raw}</script>";
        }

        return '';
    }

    //endregion
    //endregion
}
