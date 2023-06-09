<?php

namespace Adminx\Common\Models\CustomLists\Generic\CustomListItemDatas;

use Adminx\Common\Models\Generics\Files\GenericImageFile;
use ArtisanLabs\GModel\GenericModel;

class CustomListItemTestimonialsData extends GenericModel
{

    protected $fillable = [
        'image_url',
        'image',
        'content',
    ];

    protected $casts = [
        'image_url' => 'string',
        'image' => GenericImageFile::class, //todo: remove
        'content' => 'string',
    ];

    protected $appends = [
    ];

}
