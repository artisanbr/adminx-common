<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Assets\Code;

use Adminx\Common\Libs\Helpers\HtmlHelper;
use Adminx\Common\Models\Objects\Frontend\Assets\Abstract\AbstractFrontendAssetCodeObject;
use MatthiasMullie\Minify\CSS;
use ScssPhp\ScssPhp\Exception\SassException;

class FrontendScssAssetsCode extends AbstractFrontendAssetCodeObject
{

    protected $casts = [
        'css'        => 'string',
        'css_minify' => 'string',
        'css_html'   => 'string',
    ];

    protected $fillable = [
        'css',
        'css_minify',
    ];

    protected $appends = [
        'css_html',
    ];


    public function minify(): static
    {
        $minify = new CSS();
        $this->css_minify = $minify->add($this->css)->minify();

        return $this;
    }

    /**
     * @throws SassException
     */
    public function compile(): static
    {
        $this->attributes['css'] = !empty($this->raw ?? null) ? HtmlHelper::compileSCSS($this->raw, false) : '';

        $this->minify();

        return $this;
    }

    //region ATTRIBUTES

    //region GETS

    protected function getHtmlAttribute(): string
    {
        return $this->css_html;
    }

    protected function getCssHtmlAttribute(): string
    {
        return match (true) {
            !empty($this->css_minify) => "<style>{$this->css_minify}</style>",
            !empty($this->css) => "<style>{$this->css}</style>",
            default => '',
        };
    }

    //endregion

    /**
     * @throws SassException
     */
    /*protected function setRawAttribute($value): static
    {

        $this->attributes['raw'] = $value;

        $this->compile();

        $this->minify();

        return $this;
    }*/
    //region SETS

    //endregion

    //endregion
}
