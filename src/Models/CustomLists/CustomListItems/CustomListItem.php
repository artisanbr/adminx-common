<?php

namespace Adminx\Common\Models\CustomLists\CustomListItems;

use Adminx\Common\Models\Bases\CustomListItemBase;

class CustomListItem extends CustomListItemBase
{

   /* protected $casts = [
        'title' => 'string',
        'slug' => 'string',
        'position' => 'int',
        'type' => CustomListItemType::class,
        'config' => 'object',
        'data' => 'object',
        'created_at' => 'datetime:d/m/Y H:i:s',
    ];*/

    public function __construct(array $attributes = [])
    {
        $this->mergeCasts([
                              'data' => 'object',
                          ]);

        parent::__construct($attributes);
    }

    //region RELATIONS

    //endregion
}
