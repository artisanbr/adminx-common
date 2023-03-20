<?php

namespace ArtisanBR\Adminx\Common\App\Models\CustomLists;

use ArtisanBR\Adminx\Common\App\Models\Bases\CustomListBase;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems\CustomListItemImageSlider;

class CustomListImageSlider extends CustomListBase
{

    protected string $listItemClass = CustomListItemImageSlider::class;

}
