<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Assets;

use Adminx\Common\Models\Objects\Frontend\FrontendHtmlObject;
use ArtisanLabs\GModel\GenericModel;

/**
 * @property string $css_bundle_html
 */
class FrontendAssetsBundle extends GenericModel
{

    protected $fillable = [
        'js',
        'css',
        'scss',
        'head_script',
    ];

    protected $casts = [
        'js'              => FrontendJsAssetsBundle::class,
        'css'             => FrontendCssAssets::class,
        'scss'            => FrontendScssAssets::class,
        'head_script'     => FrontendHtmlObject::class,
        'css_bundle_html' => 'string',
    ];

    protected $appends = ['css_bundle_html'];


    public function minify()
    {
        $this->css->minify();
        $this->scss->compile();
        $this->js->minify();
        $this->head_script->minify();

        return $this;
    }

    public function compile()
    {
        $this->scss->compile();

        return $this;
    }

    //region Attributes
    //region Gets
    protected function getCssBundleHtmlAttribute(): string
    {
        return $this->css->html . "\n" . $this->scss->html;
    }
    //endregion
    //endregion
}
