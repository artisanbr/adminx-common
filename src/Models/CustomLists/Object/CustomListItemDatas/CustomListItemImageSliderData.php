<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\CustomListItemDatas;

use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\CustomLists\Object\CustomListItemDatas\Sliders\SliderDataButtons;
use ArtisanLabs\GModel\GenericModel;

class CustomListItemImageSliderData extends GenericModel
{

    protected $fillable = [
        'image_url',
        'description',
        'content',
        'buttons',
    ];

    protected $casts = [
        'image_url' => 'string',
        'description' => 'string',
        'content' => 'string',
        'buttons' => AsCollectionOf::class.':'.SliderDataButtons::class,
    ];

}
