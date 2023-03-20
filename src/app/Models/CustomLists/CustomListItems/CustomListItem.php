<?php

namespace ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems;

use ArtisanBR\Adminx\Common\App\Models\Bases\CustomListItemBase;

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
