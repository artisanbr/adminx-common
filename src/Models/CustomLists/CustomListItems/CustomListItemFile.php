<?php

namespace Adminx\Common\Models\CustomLists\CustomListItems;

use Adminx\Common\Models\Bases\CustomListItemBase;
use Adminx\Common\Models\CustomLists\Generic\CustomListItemDatas\CustomListItemFileData;

class CustomListItemFile extends CustomListItemBase
{

    /*protected $casts = [
        'title' => 'string',
        'slug' => 'string',
        'position' => 'int',
        'type' => CustomListItemType::class,
        'config' => 'object',
        'data' => CustomListItemFileData::class,
        'created_at' => 'datetime:d/m/Y H:i:s',
    ];*/

    public function __construct(array $attributes = [])
    {
        $this->mergeCasts([
                              'data' => CustomListItemFileData::class,
                          ]);

        parent::__construct($attributes);
    }

    //region RELATIONS

    //endregion
}
