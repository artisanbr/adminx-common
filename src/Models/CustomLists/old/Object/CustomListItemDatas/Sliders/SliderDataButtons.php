<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\CustomListItemDatas\Sliders;

use ArtisanLabs\GModel\GenericModel;

class SliderDataButtons extends GenericModel
{

    protected $fillable = [
        'content',
        'url',
        'html_attributes',
        'external',
    ];

    protected $casts = [
        'content'         => 'string',
        'url'             => 'string',
        'external'        => 'bool',
        'html_attributes' => 'collection',
    ];

    protected $attributes = [
        'html_attributes' => [],
    ];

    protected function getRenderHtmlAttributesAttribute()
    {
        return $this->html_attributes->reduce(fn($carry, $value, $key) => $carry . $key . '="' . $value . '" ');
    }

    protected function getHtmlAttribute()
    {
        return "<a href=\"{$this->url}\" " . ($this->external ? ' target="_blank"' : '') . " {$this->render_html_attributes}>{$this->content}</a>";
    }
}
