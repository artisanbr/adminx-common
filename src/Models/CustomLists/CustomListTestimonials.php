<?php

namespace Adminx\Common\Models\CustomLists;

use Adminx\Common\Models\Bases\CustomListBase;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemTestimonials;

class CustomListTestimonials extends CustomListBase
{

    protected string $listItemClass = CustomListItemTestimonials::class;


}
