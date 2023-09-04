<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\CustomListItems;

use Adminx\Common\Models\CustomLists\Abstract\CustomListItemBase;
use Adminx\Common\Models\CustomLists\CustomListHtml;
use Adminx\Common\Models\CustomLists\Object\CustomListItemDatas\CustomListItemHtmlData;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CustomListItemHtml extends CustomListItemBase
{
    protected string $listClass = CustomListHtml::class;

    /*protected $casts = [
        'title' => 'string',
        'slug' => 'string',
        'position' => 'int',
        'type' => CustomListItemType::class,
        'config' => 'object',
        'data' => CustomListItemHtmlData::class,
        'created_at' => 'datetime:d/m/Y H:i:s',
    ];*/

    protected $attributes = [
        'type' => 'html',
    ];

    public function __construct(array $attributes = [])
    {
        $this->mergeCasts([
                              'data' => CustomListItemHtmlData::class,
                          ]);

        parent::__construct($attributes);
    }

    //region Attributes
    public function html(): Attribute {
        return Attribute::make(get: fn() => $this->data->html);
    }
    //endregion

    //region RELATIONS

    //endregion
}
