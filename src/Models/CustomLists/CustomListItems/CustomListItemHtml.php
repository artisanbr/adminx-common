<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\CustomListItems;

use Adminx\Common\Models\CustomLists\Abstract\CustomListItemBase;
use Adminx\Common\Models\CustomLists\CustomListHtml;
use Adminx\Common\Models\CustomLists\Object\CustomListItemDatas\CustomListItemHtmlData;
use Adminx\Common\Models\Objects\Seo\Seo;
use Adminx\Common\Models\Traits\HasSEO;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CustomListItemHtml extends CustomListItemBase
{
    use HasSEO;

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
    protected function getSeoAttribute(): Seo
    {
        return $this->data->seo;
    }

    protected function setSeoAttribute($value): static
    {
        if(is_array($value)){
            $this->data->seo->fill($value);
        }else /*if(get_class($value) === Seo::class)*/ {
            $this->data->seo = $value;
        }

        return $this;
    }
    
    public function html(): Attribute {
        return Attribute::make(get: fn() => $this->data->html);
    }
    //endregion

    //region RELATIONS

    //endregion
}
