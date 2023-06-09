<?php

namespace Adminx\Common\Models\CustomLists\Generic\CustomListItemDatas;

use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\CustomLists\Generic\CustomListItemDatas\Sliders\SliderDataButtons;
use Adminx\Common\Models\Generics\Elements\HtmlElement;
use Adminx\Common\Models\Generics\Files\GenericImageFile;
use Adminx\Common\Models\Objects\Seo\Seo;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CustomListItemHtmlData extends GenericModel
{

    protected $fillable = [
        'image_url',
        'image',
        'description',
        'content',
        //'raw_html',
        'seo'
    ];

    protected $casts = [
        'image_url' => 'string',
        'image' => GenericImageFile::class, //todo: remove
        'description' => 'string',
        'content' => HtmlElement::class,
        //'raw_html' => 'string',
        'seo' => Seo::class,
    ];

    protected $attributes = [
        'content' => [],
    ];

    protected $appends = [
        'html'
    ];

    protected $temporary = ['raw_html'];

    public function getHtmlAttribute() {
        return $this->content->raw;
    }

}
