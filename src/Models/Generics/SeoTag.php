<?php

namespace Adminx\Common\Models\Generics;

use ArtisanLabs\GModel\GenericModel;

class SeoTag extends GenericModel
{

    protected $fillable = [
        'type_name',
        'type_value',
        'content',
        'attrs',
    ];

    protected $attributes = [
        'type_name'  => 'name',
        'type_value' => '',
        'content'    => '',
        'attrs'      => [],
    ];

    protected $casts = [
        'type_name'  => 'string',
        'type_value' => 'string',
        'content'    => 'string',
        'html_tags'    => 'string',
        'attrs'      => 'collection',
    ];

    protected $appends = [
        'html_tag'
    ];

    public static function makeNameType($type, $content = null, array $data = []): SeoTag
    {
        return new self(array_merge(
            $data,
            [
                'type_name' => 'name',
                'type_value' => $type
            ],
            ($content ? ['content' => $content] : [])
        ));
    }
    public static function makePropertyType($type, $content = null, array $data = []): SeoTag
    {
        return new self(array_merge(
            $data,
            [
                'type_name' => 'property',
                'type_value' => $type
            ],
            ($content ? ['content' => $content] : [])
        ));
    }

    protected function getHtmlTagAttribute(): string
    {
        $extraAttrs = $this->attrs->transform(fn($attr, $value) => "{$attr}='{$value}'")->join(' ');
        //dump('<meta '.$this->type_name.'="'.$this->type_value.'" content="'.$this->content.'" '.$extraAttrs.' />');
        return '<meta '.$this->type_name.'="'.$this->type_value.'" content="'.$this->content.'" '.$extraAttrs.' />';
    }

}
