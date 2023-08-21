<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Generic\CustomListItemDatas;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Objects\Seo\Seo;
use ArtisanLabs\GModel\GenericModel;

class CustomListItemHtmlData extends GenericModel
{

    protected $fillable = [
        'image_url',
        //'image',
        'description',
        'content',
        'seo'
    ];

    protected $casts = [
        'image_url' => 'string',
        'description' => 'string',
        'content' => 'string',
        'seo' => Seo::class,
    ];

    /*protected $attributes = [
    ];*/

    protected $appends = [
        'html'
    ];

    //region Attributes
    //region GET's
    protected function getDescriptionAttribute(){

        return !empty($this->attributes["description"] ?? null) ? $this->attributes["description"] : Str::limit(Str::removeHTML($this->content), 150);
    }

    protected function getHtmlAttribute(){
        return $this->content;
    }
    //endregion

    //region SET's
    protected function setDescriptionAttribute($value){
        if((string) $value !== Str::limit(Str::removeHTML($this->content), 150)){
            $this->attributes["description"] = $value;
        }else{
            $this->attributes["description"] = null;
        }
    }
    //endregion
    //endregion

}
