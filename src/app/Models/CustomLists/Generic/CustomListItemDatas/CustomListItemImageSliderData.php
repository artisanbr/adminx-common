<?php

namespace ArtisanBR\Adminx\Common\App\Models\CustomLists\Generic\CustomListItemDatas;

use ArtisanBR\Adminx\Common\App\Models\Casts\AsCollectionOf;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\Generic\CustomListItemDatas\Sliders\SliderDataButtons;
use ArtisanBR\Adminx\Common\App\Models\Generics\Files\GenericImageFile;
use ArtisanLabs\GModel\GenericModel;

class CustomListItemImageSliderData extends GenericModel
{

    protected $fillable = [
        'image',
        'description',
        'buttons',
    ];

    protected $casts = [
        'image' => GenericImageFile::class,
        'description' => 'string',
        'buttons' => AsCollectionOf::class.':'.SliderDataButtons::class,
    ];

}
