<?php

namespace Adminx\Common\Models\Objects\Frontend\Assets;

use Adminx\Common\Libs\Helpers\HtmlHelper;
use Adminx\Common\Models\Objects\Frontend\Assets\Abstract\AbstractFrontendAssetObject;
use Html;
use MatthiasMullie\Minify\CSS;
use ScssPhp\ScssPhp\Exception\SassException;

class FrontendScssAssets extends AbstractFrontendAssetObject
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
        return "{$this->resources_html} \n {$this->css_html}";
    }

    protected function getResourcesHtmlAttribute(): string
    {
        return $this->resources->transform(fn($item) => Html::style($item))->join("\n");
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
