<?php

namespace Adminx\Common\Models\CustomLists\Generic\CustomListItemDatas;

use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\CustomLists\Generic\CustomListItemDatas\Sliders\SliderDataButtons;
use Adminx\Common\Models\Generics\Files\GenericImageFile;
use ArtisanLabs\GModel\GenericModel;

class CustomListItemImageSliderData extends GenericModel
{

    protected $fillable = [
        'image_url',
        'image',
        'description',
        'buttons',
    ];

    protected $casts = [
        'image_url' => 'string',
        'image' => GenericImageFile::class, //todo: remove
        'description' => 'string',
        'buttons' => AsCollectionOf::class.':'.SliderDataButtons::class,
    ];

}
