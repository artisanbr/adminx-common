<?php

namespace ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems;

use ArtisanBR\Adminx\Common\App\Models\Bases\CustomListItemBase;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListTestimonials;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\Generic\CustomListItemDatas\CustomListItemTestimonialsData;

class CustomListItemTestimonials extends CustomListItemBase
{
    protected string $listClass = CustomListTestimonials::class;

    /*protected $casts = [
        'title' => 'string',
        'slug' => 'string',
        'position' => 'int',
        'type' => CustomListItemType::class,
        'config' => 'object',
        'data' => CustomListItemTestimonialsData::class,
        'created_at' => 'datetime:d/m/Y H:i:s',
    ];*/

    protected $attributes = [
        'type' => 'testimonial',
    ];

    public function __construct(array $attributes = [])
    {
        $this->mergeCasts([
                              'data' => CustomListItemTestimonialsData::class,
                          ]);

        parent::__construct($attributes);
    }

    //region RELATIONS

    //endregion
}
