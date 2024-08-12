<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\CustomListItems;

use Adminx\Common\Models\CustomLists\Abstract\CustomListItemAbstract\CustomListItemAbstract;
use Adminx\Common\Models\CustomLists\CustomListImageSlider;
use Adminx\Common\Models\CustomLists\Object\CustomListItemDatas\CustomListItemImageData;

class CustomListItemImageSlider extends CustomListItemAbstract
{
    protected string $listClass = CustomListImageSlider::class;

    /*protected $casts = [
        'title' => 'string',
        'slug' => 'string',
        'position' => 'int',
        'type' => CustomListItemType::class,
        'config' => 'object',
        'data' => CustomListItemImageSliderData::class,
        'created_at' => 'datetime:d/m/Y H:i:s',
    ];*/

    protected $attributes = [
        'type' => 'slide.image',
    ];

    public function __construct(array $attributes = [])
    {
        $this->mergeCasts([
                              'data' => CustomListItemImageData::class,
                          ]);

        parent::__construct($attributes);
    }

    //region RELATIONS

    //endregion
}
