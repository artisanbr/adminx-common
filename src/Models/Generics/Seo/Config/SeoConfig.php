<?php

namespace Adminx\Common\Models\Generics\Seo\Config;

use ArtisanLabs\GModel\GenericModel;

class SeoConfig extends GenericModel
{

    protected $fillable = [
        'show_parent_title',
        'use_defaults',
    ];

    protected $attributes = [
        'show_parent_title'       => true,
        'use_defaults' => true,
    ];

    protected $casts = [
        'show_parent_title'          => 'bool',
        'use_defaults'          => 'bool'
        ];

    protected $appends = [
    ];

}
