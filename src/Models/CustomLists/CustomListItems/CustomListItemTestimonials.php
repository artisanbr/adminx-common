<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\CustomListItems;

use Adminx\Common\Models\CustomLists\Abstract\CustomListItemBase;
use Adminx\Common\Models\CustomLists\CustomListTestimonials;
use Adminx\Common\Models\CustomLists\Object\CustomListItemDatas\CustomListItemTestimonialsData;

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
