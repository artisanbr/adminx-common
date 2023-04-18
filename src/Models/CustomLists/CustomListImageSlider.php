<?php

namespace Adminx\Common\Models\CustomLists;

use Adminx\Common\Models\Bases\CustomListBase;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemImageSlider;

class CustomListImageSlider extends CustomListBase
{

    protected string $listItemClass = CustomListItemImageSlider::class;

}
