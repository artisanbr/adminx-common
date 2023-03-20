<?php

namespace ArtisanBR\Adminx\Common\App\Models\Generics\Elements\Themes;

use ArtisanBR\Adminx\Common\App\Models\Generics\Files\GenericFile;
use ArtisanBR\Adminx\Common\App\Models\Generics\Files\GenericImageFile;
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
        //'type_name'  => 'name',
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
