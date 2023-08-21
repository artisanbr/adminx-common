<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Generic\CustomListItemDatas;

use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\CustomLists\Generic\CustomListItemDatas\Sliders\SliderDataButtons;
use ArtisanLabs\GModel\GenericModel;

class CustomListItemImageSliderData extends GenericModel
{

    protected $fillable = [
        'image_url',
        //'image',
        'description',
        'content',
        'buttons',
    ];

    protected $casts = [
        'image_url' => 'string',
        //'image' => GenericImageFile::class, //todo: remove
        'description' => 'string',
        'content' => 'string',
        'buttons' => AsCollectionOf::class.':'.SliderDataButtons::class,
    ];

}
