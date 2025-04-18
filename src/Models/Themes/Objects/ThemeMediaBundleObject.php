<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects;

use Adminx\Common\Models\Objects\Frontend\FrontendImageObject;
use ArtisanLabs\GModel\GenericModel;

class ThemeMediaBundleObject extends GenericModel
{

    protected $fillable = [
        'logo_url',
        'logo_secondary_url',
        'favicon_url',

        'logo',
        'logo_secondary',
        'favicon',


        //todo:external media
        'logo_external',
        'logo_secondary_external',
        'favicon_external',
    ];

    protected $attributes = [
    ];

    protected $casts = [
        'logo_url'           => 'string',
        'logo_secondary_url'           => 'string',
        'favicon_url'           => 'string',

        'logo'           => FrontendImageObject::class,
        'logo_secondary' => FrontendImageObject::class,
        'favicon'        => FrontendImageObject::class,
    ];

    //protected $appends = [];

    public function getHtmlAttributes($media)
    {
        return $this->{$media}->html;
    }

    protected function getLogoUrlAttribute()
    {
        return $this->logo->url;
    }

    protected function getLogoSecondaryUrlAttribute()
    {
        return $this->logo_secondary->url;
    }

    protected function getFaviconUrlAttribute()
    {
        return $this->favicon->url;
    }


    
}
