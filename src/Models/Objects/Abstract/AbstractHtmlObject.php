<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Abstract;

use Adminx\Common\Facades\Frontend\FrontendHtml;
use Adminx\Common\Libs\Helpers\HtmlHelper;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Themes\Theme;
use ArtisanLabs\GModel\GenericModel;

/**
 * @property string $html
 * @property string $minify
 * @property string $twig_html
 */
abstract class AbstractHtmlObject extends GenericModel
{

    protected $fillable = [
        'html',
        'minify',
        'raw'
        //'elements',
        //'use_elements',
    ];

    protected $casts = [
        //'elements' => 'collection',
        //'use_elements' => 'bool',
        'html'   => 'string',
        'minify' => 'string',
        'twig_html' => 'string',
    ];

    protected $attributes = [
        'html' => '',
    ];



    public function minify(): static
    {
        $this->minify = HtmlHelper::minifyHtml($this->html);

        return $this;
    }

    public function builtHtml(Theme $theme, FrontendBuildObject $frontendBuild = new FrontendBuildObject(), $viewTemporaryName = 'element-html'): string
    {
        return FrontendHtml::theme($theme, $frontendBuild)->html($this->html);
    }



    //region Attributes
    //region Gets
    protected function getTwigHtmlAttribute(): string
    {
        //return Attribute::make(get: fn() => $this->header->raw);
        return preg_replace('/[^\@]{{/m', "@{{", $this->html);
    }
    //endregion

    //region Sets
    protected function setRawAttribute($value): static
    {
        $this->html = $value;
        return $this;
    }
    //endregion
    //endregion

}
