<?php

namespace ArtisanBR\Adminx\Common\App\Models\CustomLists;

use ArtisanBR\Adminx\Common\App\Models\Bases\CustomListBase;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems\CustomListItemHtml;

class CustomListHtml extends CustomListBase
{

    protected string $listItemClass = CustomListItemHtml::class;

    /*protected $casts = [
        'title' => 'string',
        'description' => 'string',
        'type' => CustomListType::class,
        'config' => 'object',
    ];*/




}
