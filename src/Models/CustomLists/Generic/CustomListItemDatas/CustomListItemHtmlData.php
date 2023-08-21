<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Generic\CustomListItemDatas;

use Adminx\Common\Models\Generics\Files\GenericImageFile;
use Adminx\Common\Models\Objects\Seo\Seo;
use ArtisanLabs\GModel\GenericModel;

class CustomListItemHtmlData extends GenericModel
{

    protected $fillable = [
        'image_url',
        'image',
        'description',
        //'content',
        'content_string',
        //'raw_html',
        'seo'
    ];

    protected $casts = [
        'image_url' => 'string',
        'image' => GenericImageFile::class, //todo: remove
        'description' => 'string',
        'content' => 'string',
        'content_string' => 'string',
        'seo' => Seo::class,
    ];

    /*protected $attributes = [
    ];*/

    protected $appends = [
        //'html'
    ];

    protected $temporary = ['raw_html'];

    /*public function getHtmlAttribute() {
        return $this->content->raw;
    }*/

}
