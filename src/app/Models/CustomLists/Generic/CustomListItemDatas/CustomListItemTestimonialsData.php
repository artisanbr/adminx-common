<?php

namespace ArtisanBR\Adminx\Common\App\Models\CustomLists\Generic\CustomListItemDatas;

use ArtisanBR\Adminx\Common\App\Models\Generics\Files\GenericImageFile;
use ArtisanLabs\GModel\GenericModel;

class CustomListItemTestimonialsData extends GenericModel
{

    protected $fillable = [
        'image',
        'content',
    ];

    protected $casts = [
        'image' => GenericImageFile::class,
        'content' => 'string',
    ];

    protected $appends = [
    ];

}
