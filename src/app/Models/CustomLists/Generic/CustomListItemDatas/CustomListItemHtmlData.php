<?php

namespace ArtisanBR\Adminx\Common\App\Models\CustomLists\Generic\CustomListItemDatas;

use ArtisanBR\Adminx\Common\App\Models\Casts\AsCollectionOf;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\Generic\CustomListItemDatas\Sliders\SliderDataButtons;
use ArtisanBR\Adminx\Common\App\Models\Generics\Elements\HtmlElement;
use ArtisanBR\Adminx\Common\App\Models\Generics\Files\GenericImageFile;
use ArtisanBR\Adminx\Common\App\Models\Generics\Seo;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CustomListItemHtmlData extends GenericModel
{

    protected $fillable = [
        'image',
        'description',
        'content',
        //'raw_html',
        'seo'
    ];

    protected $casts = [
        'image' => GenericImageFile::class,
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
