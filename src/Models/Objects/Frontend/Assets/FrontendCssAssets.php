<?php

namespace Adminx\Common\Models\Objects\Frontend\Assets;

use Adminx\Common\Libs\Helpers\HtmlHelper;
use Adminx\Common\Models\Objects\Frontend\Assets\Abstract\AbstractFrontendAssetObject;
use Html;
use MatthiasMullie\Minify\CSS;
use ScssPhp\ScssPhp\Exception\SassException;

class FrontendCssAssets extends AbstractFrontendAssetObject
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
        return "{$this->resources_html} \n {$this->raw_html}";
    }

    protected function getResourcesHtmlAttribute(): string
    {
        return $this->resources->transform(fn($item) => Html::style($item))->join("\n");
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
