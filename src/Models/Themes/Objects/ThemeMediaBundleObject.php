<?php

namespace Adminx\Common\Models\Themes\Objects;

use Adminx\Common\Models\Generics\Files\GenericFile;
use Adminx\Common\Models\Generics\Files\GenericImageFile;
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
        //'type_name'  => 'name',
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
