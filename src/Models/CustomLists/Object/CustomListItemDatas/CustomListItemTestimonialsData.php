<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\CustomListItemDatas;

use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;

class CustomListItemTestimonialsData extends GenericModel
{

    protected $fillable = [
        'subtitle',
        'image_url',
        //'image',
        'content',
        'rating',
        'max_rating',
        'frontend_build',
    ];

    protected $casts = [
        'image_url'   => 'string',
        'subtitle' => 'string',
        //'image' => GenericImageFile::class, //todo: remove
        'content'     => 'string',
        'rating'      => 'int',
        'max_rating'  => 'int',
        'frontend_build' => FrontendBuildObject::class,
    ];

    protected $attributes = [
        'rating' => 5,
        'max_rating' => 5,
    ];

    protected $appends = [
    ];

}
