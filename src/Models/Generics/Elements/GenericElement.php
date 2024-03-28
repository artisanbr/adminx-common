<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Elements;

use Adminx\Common\Enums\ElementType;
use Adminx\Common\Libs\FrontendEngine\AdvancedHtmlEngine;
use Adminx\Common\Models\Interfaces\FrontendModel;
use Adminx\Common\Models\Sites\Site;
use ArtisanBR\GenericModel\GenericModel;

abstract class GenericElement extends GenericModel
{
    protected $fillable = [
        'elements',
        'is_raw_html',
        'is_advanced_html',
        'html',
        'html_minify',
        'raw',
        'widget_id',
        'type',
        'title',
        'position',
    ];

    protected $attributes = [
        'elements' => [],
        'html'     => '',
        'raw'     => '',
        //'is_raw'   => true,
        'type'     => ElementType::RawHtml,
    ];

    protected $casts = [
        //'elements'      => 'collection',
        //'elements_html' => 'string',

        'html'             => 'string',
        'raw'              => 'string',
        'is_raw_html'      => 'bool',
        'is_advanced_html' => 'bool',
        'type'             => ElementType::class,
    ];

    protected $appends = [
        //'elements_html',
    ];

    //region HELPERS
    public function builtHtml(Site $site, FrontendModel $model, $viewTemporaryName = 'element-html'): string
    {
        return AdvancedHtmlEngine::start($site, $model, $viewTemporaryName)->html($this->raw)->buildHtml();
    }

    public function flushHtmlCache(Site $site, FrontendModel $model, $viewTemporaryName = 'element-html')
    {
        $this->attributes['html'] = $this->builtHtml($site, $model, $viewTemporaryName);

    }
    //endregion

    //region ATTRIBUTES
    //region GETS
    protected function getIsRawHtmlAttribute(): bool
    {
        return $this->attributes['is_raw_html'] ?? (!$this->type || $this->type === ElementType::RawHtml); //$this->resources->toJson();
    }

    protected function getIsAdvancedHtmlAttribute(): bool
    {
        return $this->attributes['is_advanced_html'] ?? (!$this->type || $this->type === ElementType::AdvancedHtml); //$this->resources->toJson();
    }
    /*protected function getElementsHtmlAttribute(): string
    {
        return ''; //$this->resources->toJson();
    }*/
    //endregion

    //region SETS
    protected function setHtmlAttribute($value): static
    {
        if (!is_string($value) && is_array($value)) {

            $this->attributes['html'] = $this->is_advanced_html ? $value['advanced'] ?? '' : $value['visual'] ?? '';

        }
        else {
            $this->attributes['html'] = $value;
        }


        return $this; //$this->resources->toJson();
    }

    protected function setIsRawHtmlAttribute($value): static
    {
        $this->type = ElementType::RawHtml;

        return $this;
    }

    protected function setIsAdvancedHtmlAttribute($value): static
    {
        $this->type = ElementType::AdvancedHtml;

        return $this;
    }
    //endregion

    //endregion


}
