<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects;


use Adminx\Common\Models\Objects\Frontend\Assets\FrontendAssetsBundle;
use Adminx\Common\Models\Objects\Seo\Seo;
use ArtisanLabs\GModel\GenericModel;


class ArticleMetaObject extends GenericModel
{
    protected $fillable = [
        'seo',
        'assets',
        'wp_id',
    ];

    protected $casts = [
        'seo'            => Seo::class,
        'assets'         => FrontendAssetsBundle::class,
        'wp_id'      => 'int',
    ];
}
