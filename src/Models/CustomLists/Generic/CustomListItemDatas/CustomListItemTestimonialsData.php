<?php

namespace Adminx\Common\Models\CustomLists\Generic\CustomListItemDatas;

use Adminx\Common\Models\Generics\Files\GenericImageFile;
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
