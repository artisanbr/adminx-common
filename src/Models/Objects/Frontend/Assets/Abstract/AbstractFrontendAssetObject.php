<?php

namespace Adminx\Common\Models\Objects\Frontend\Assets\Abstract;

use Adminx\Common\Libs\Helpers\HtmlHelper;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;

/**
 * @property Collection $resources
 * @property string $resources_html
 * @property string $raw
 * @property string $raw_minify
 * @property string $raw_html
 * @property string $html
 */
abstract class AbstractFrontendAssetObject extends GenericModel
{


    public function __construct(array $attributes = [])
    {

        $this->addFillables([
                                'resources',
                                'raw',
                                'raw_minify',
                            ]);

        $this->addCasts([
                            'resources'      => 'collection',
                            'resources_html' => 'string',
                            'raw'            => 'string',
                            'raw_minify'     => 'string',
                            'raw_html'       => 'string',
                            'html'           => 'string',
                        ]);

        $this->addAttributes([
                                 'resources'  => [],
                                 //'raw'        => '',
                                 //'raw_minify' => null,
                             ]);

        $this->addAppends([
                              'resources_html',
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

    protected function getResourcesHtmlAttribute(): string
    {
        return $this->resources->toJson();
    }

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
        return $this->resources_html ."\n". $this->raw_html;
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
