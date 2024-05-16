<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Assets\Abstract;

use Adminx\Common\Libs\Helpers\HtmlHelper;
use ArtisanLabs\GModel\GenericModel;

/**
 * @property string $raw
 * @property string $raw_minify
 * @property string $raw_html
 * @property string $html
 */
abstract class AbstractFrontendAssetCodeObject extends GenericModel
{


    public function __construct(array $attributes = [])
    {

        $this->addFillables([
                                'raw',
                                'raw_minify',
                            ]);

        $this->addCasts([
                            'raw'        => 'string',
                            'raw_minify' => 'string',
                            'raw_html'   => 'string',
                            'html'       => 'string',
                        ]);

        /*$this->addAttributes([
                                 //'raw'        => '',
                                 //'raw_minify' => null,
                             ]);*/

        $this->addAppends([
                              'raw_html',
                              'html',
                          ]);

        parent::__construct($attributes);
    }

    public function minify(): static
    {
        $this->raw_minify = HtmlHelper::minifyHtml($this->raw);

        return $this;
    }

    //region ATTRIBUTES
    //region GETS

    protected function getRawHtmlAttribute(): string|null
    {
        return match (true) {
            !empty($this->raw_minify) => $this->raw_minify,
            !empty($this->raw) => $this->raw,
            default => null,
        };
    }

    protected function getHtmlAttribute(): string
    {
        return $this->raw_html;
    }

    //endregion
    //region SETS
    /*protected function setRawAttribute($value): static
    {
        $this->attributes['raw'] = $value;

        $this->minify();

        return $this;
    }*/
    //endregion
    //endregion
}
