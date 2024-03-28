<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Assets;

use Adminx\Common\Libs\Helpers\HtmlHelper;

abstract class GenericAssetElementBase extends GenericModel
{

    protected $fillable = [
        'resources',
        'raw',
        'raw_minify',
    ];

    protected $attributes = [
        'resources' => [],
        'raw' => '',
        'raw_minify' => null,
    ];

    protected $casts = [
        'resources' => 'collection',
        'raw' => 'string',
        'raw_minify' => 'string',
        'resources_html' => 'string',
        'raw_html' => 'string',
        'html' => 'string',
    ];

    protected $appends = [
        'resources_html',
        'raw_html',
        'html',
    ];

    public function minify(): static
    {
        $this->raw_minify = HtmlHelper::minifyHtml($this->raw);
        return $this;
    }

    //region ATTRIBUTES
    //region GETS

    protected function getResourcesHtmlAttribute(): string{
        return $this->resources->toJson();
    }

    protected function getRawHtmlAttribute(): string{
        return $this->raw_minify ?? $this->raw ?? '';
    }

    protected function getHtmlAttribute(): string{
        return $this->resources_html . $this->raw_html;
    }

    //endregion
    //region SETS

    //endregion
    //endregion
}
