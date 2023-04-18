<?php

namespace Adminx\Common\Models\CustomLists;

use Adminx\Common\Models\Bases\CustomListBase;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemHtml;

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
