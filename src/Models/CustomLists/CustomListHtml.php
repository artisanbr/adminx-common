<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists;

use Adminx\Common\Models\CustomLists\Abstract\CustomListAbstract;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemHtml;

class CustomListHtml extends CustomListAbstract
{

    protected string $listItemClass = CustomListItemHtml::class;

    /*protected $casts = [
        'title' => 'string',
        'description' => 'string',
        'type' => CustomListType::class,
        'config' => 'object',
    ];*/




}
