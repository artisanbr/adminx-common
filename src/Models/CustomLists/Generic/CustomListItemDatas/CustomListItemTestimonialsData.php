<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Generic\CustomListItemDatas;

use ArtisanLabs\GModel\GenericModel;

class CustomListItemTestimonialsData extends GenericModel
{

    protected $fillable = [
        'image_url',
        //'image',
        'content',
    ];

    protected $casts = [
        'image_url' => 'string',
        //'image' => GenericImageFile::class, //todo: remove
        'content' => 'string',
    ];

    protected $appends = [
    ];

}
