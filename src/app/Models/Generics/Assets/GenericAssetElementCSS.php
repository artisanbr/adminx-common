<?php

namespace ArtisanBR\Adminx\Common\App\Models\Generics\Assets;

use ArtisanBR\Adminx\Common\App\Libs\Helpers\HtmlHelper;
use Html;
use MatthiasMullie\Minify\CSS;
use ScssPhp\ScssPhp\Exception\SassException;

class GenericAssetElementCSS extends GenericAssetElementBase
{

    public function __construct(array $attributes = [])
    {
        $this->addCasts([
                            'scss_raw' => 'string',
                            'scss'     => 'string',
                        ]);

        $this->addFillables([
                                'scss_raw',
                                'scss',
                            ]);

        parent::__construct($attributes);
    }

    protected function getHtmlAttribute(): string{
        return "{$this->resources_html} \n {$this->raw_html} \n {$this->scss_html}";
    }


    public function minify(): static
    {
        $minify = new CSS();
        $this->raw_minify = $minify->add($this->raw)->minify();


        $this->compile(true);

        /*$minifyScss = new CSS();
        $this->attributes['scss'] = $minifyScss->add($this->scss)->minify();*/

        return $this;
    }

    /**
     * @throws SassException
     */
    public function compile($compress = false): static
    {
        $this->attributes['scss'] = !empty($this->scss_raw ?? null) ? HtmlHelper::compileSCSS($this->scss_raw, $compress) : '';

        return $this;
    }

    //region ATTRIBUTES
    protected function setScssRawAttribute($value): ?string
    {
        $this->attributes['scss_raw'] = $value;

        return $value;
    }

    //region GETS

    protected function getResourcesHtmlAttribute(): string
    {
        return $this->resources->transform(fn($item) => Html::style($item))->join("\n");
    }

    protected function getRawHtmlAttribute(): string
    {
        return '<style>' . parent::getRawHtmlAttribute() . '</style>';
    }

    protected function getScssHtmlAttribute(): string
    {
        return "<style>{$this->scss}</style>";
    }

    //endregion
    //region SETS

    //endregion
    //endregion
}
