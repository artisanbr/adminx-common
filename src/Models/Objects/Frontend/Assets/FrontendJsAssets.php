<?php

namespace Adminx\Common\Models\Objects\Frontend\Assets;

use Adminx\Common\Models\Objects\Frontend\Assets\Abstract\AbstractFrontendAssetObject;
use Html;
use MatthiasMullie\Minify\JS;

class FrontendJsAssets extends AbstractFrontendAssetObject
{
    protected $casts = [
        'head'       => 'string',
        'head_minify'       => 'string',
        'head_html'       => 'string',

        'before_body' => 'string',
        'before_body_minify' => 'string',
        'before_body_html' => 'string',

        'after_body' => 'string',
        'after_body_minify' => 'string',
        'after_body_html' => 'string',
    ];

    protected $fillable = [
        'head',
        'head_minify',

        'before_body',
        'before_body_minify',

        'after_body',
        'after_body_minify',
    ];

    protected $appends = [
        'head_html',
        'before_body_html',
        'after_body_html',
    ];

    public function minify(): static
    {
        $this->head_minify = (new JS())->add($this->head)->minify();
        $this->before_body_minify = (new JS())->add($this->before_body)->minify();
        $this->after_body_minify = (new JS())->add($this->after_body)->minify();

        return $this;
    }

    //region ATTRIBUTES
    //region GETS
    protected function getBeforeBodyAttribute(): string
    {
        return $this->attributes['before_body'] ?? $this->raw ?? '';
    }

    protected function getResourcesHtmlAttribute(): string
    {
        return $this->resources->transform(fn($item) => Html::script($item))->join("\n");
    }

    protected function getRawHtmlAttribute(): string
    {
        return '<script type="text/javascript">' . parent::getRawHtmlAttribute() . '</script>';
    }

    protected function getHeadHtmlAttribute(): string
    {
        return '<script type="text/javascript">' . ($this->head_minify ?? $this->head ?? '') . '</script>';
    }

    protected function getBeforeBodyHtmlAttribute(): string
    {
        return '<script type="text/javascript">' . ($this->before_body_minify ?? $this->before_body ?? '') . '</script>';
    }

    protected function getAfterBodyHtmlAttribute(): string
    {
        return '<script type="text/javascript">' . ($this->after_body_minify ?? $this->after_body ?? '') . '</script>';
    }
    //endregion
    //endregion
}
