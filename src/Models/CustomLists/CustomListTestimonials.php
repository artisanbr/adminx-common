<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists;

use Adminx\Common\Models\CustomLists\Abstract\CustomListAbstract;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemTestimonials;

class CustomListTestimonials extends CustomListAbstract
{

    protected string $listItemClass = CustomListItemTestimonials::class;


}
