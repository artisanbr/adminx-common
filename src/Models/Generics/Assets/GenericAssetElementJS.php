<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Assets;

use Html;
use MatthiasMullie\Minify\JS;

class GenericAssetElementJS extends GenericAssetElementBase
{
    public function __construct(array $attributes = [])
    {
        $this->addCasts([
                            'head_js'       => 'string',
                            'head_js_minify'       => 'string',
                            'head_js_html'       => 'string',

                            'after_body_js' => 'string',
                            'after_body_js_minify' => 'string',
                            'after_body_js_html' => 'string',
                        ]);

        $this->addFillables([
                                'head_js',
                                'head_js_minify',
                                'after_body_js',
                                'after_body_js_minify',
                            ]);

        $this->addAppends([
                              'head_js_html',
                              'after_body_js_html',
                          ]);

        parent::__construct($attributes);
    }

    public function minify(): static
    {
        $minify = new JS();
        $this->raw_minify = (new JS())->add($this->raw)->minify();
        $this->head_js_minify = (new JS())->add($this->head_js)->minify();
        $this->after_body_js_minify = (new JS())->add($this->after_body_js)->minify();

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
        return '<script type="text/javascript">' . parent::getRawHtmlAttribute() . '</script>';
    }

    protected function getHeadJsHtmlAttribute(): string
    {
        return '<script type="text/javascript">' . ($this->head_js_minify ?? $this->head_js ?? '') . '</script>';
    }

    protected function getAfterBodyJsHtmlAttribute(): string
    {
        return '<script type="text/javascript">' . ($this->after_body_js_minify ?? $this->after_body_js ?? '') . '</script>';
    }
    //endregion
    //endregion
}
