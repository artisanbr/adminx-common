<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\CustomListItems;

use Adminx\Common\Models\CustomLists\Abstract\CustomListItemAbstract\CustomListItemAbstract;

class CustomListItem extends CustomListItemAbstract
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
