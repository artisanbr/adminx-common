<?php

namespace Adminx\Common\Models\Objects\Frontend\Assets;

use Adminx\Common\Models\Objects\Frontend\Assets\Abstract\AbstractFrontendAssetObject;
use Html;
use MatthiasMullie\Minify\JS;

class FrontendJsAssets extends AbstractFrontendAssetObject
{
    /*protected $casts = [
    ];

    protected $fillable = [
    ];

    protected $appends = [
    ];*/

    public function minify(): static
    {
        $this->raw_minify = (new JS())->add($this->raw)->minify();

        return $this;
    }

    //region ATTRIBUTES
    //region GETS

    protected function getResourcesHtmlAttribute(): string
    {
        return $this->resources->transform(fn($item) => Html::script($item))->join("\n");
    }

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
