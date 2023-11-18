<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Assets\Code;

use Adminx\Common\Models\Objects\Frontend\Assets\Abstract\AbstractFrontendAssetCodeObject;
use MatthiasMullie\Minify\CSS;

class FrontendCssAssetsCode extends AbstractFrontendAssetCodeObject
{

    protected $casts = [
        'scss' => 'string',
        'scss_raw' => 'string',
    ];

    protected $fillable = [
        'scss',
        'scss_raw'
    ];


    public function minify(): static
    {
        $minify = new CSS();
        $this->attributes['raw_minify'] = $minify->add($this->raw)->minify();

        return $this;
    }

    //region ATTRIBUTES

    //region GETS

    protected function getHtmlAttribute(): string
    {
        return $this->raw_html;
    }

    protected function getRawHtmlAttribute(): string
    {
        $raw = parent::getRawHtmlAttribute();

        if (!empty($raw)) {
            return "<style>{$raw}</style>";
        }

        return '';
    }


    //endregion

    //region SETS

    //endregion

    //endregion
}
