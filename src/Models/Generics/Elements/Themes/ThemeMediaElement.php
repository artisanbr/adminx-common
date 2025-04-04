<?php
/*
 * Copyright (c) 2024-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Elements\Themes;

use Adminx\Common\Models\Generics\Files\GenericImageFile;
use ArtisanLabs\GModel\GenericModel;

class ThemeMediaElement extends GenericModel
{

    protected $fillable = [
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
        'logo'           => GenericImageFile::class,
        'logo_secondary' => GenericImageFile::class,
        'favicon'        => GenericImageFile::class,
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
