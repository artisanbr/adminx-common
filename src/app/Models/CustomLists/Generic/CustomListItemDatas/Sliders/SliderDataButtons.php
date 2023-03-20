<?php

namespace ArtisanBR\Adminx\Common\App\Models\CustomLists\Generic\CustomListItemDatas\Sliders;

use ArtisanBR\Adminx\Common\App\Models\Generics\Files\GenericImageFile;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Facades\Blade;

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
