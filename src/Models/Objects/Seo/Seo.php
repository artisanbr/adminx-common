<?php

namespace Adminx\Common\Models\Objects\Seo;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\File;
use Adminx\Common\Models\Objects\Seo\Config\SeoConfig;
use Illuminate\Support\Collection;
use ArtisanLabs\GModel\GenericModel;


class Seo extends GenericModel
{

    protected $fillable = [
        'title',
        'image_url',
        'description',
        'keywords',
        'robots',
        'config',
        'gtag'
    ];

    protected $attributes = [
        'title'       => '',
        'description' => '',
        'keywords'    => '',
        'robots'      => 'index, follow',
        'config'      => [],
        'gtag'    => null,
    ];

    protected $casts = [
        'title'          => 'string',
        'image_url'          => 'string',
        'description'    => 'string',
        'keywords'       => 'string',
        'keywords_array' => 'collection',
        'robots'         => 'string',
        'config'         => SeoConfig::class,
    ];

    protected $appends = [
        'keywords_array',
        //'image',
    ];


    //region ATTRIBUTES

    //region  SETS
    protected function setKeywordsAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['keywords'] = implode(',', $value);
        }
        else {
            $this->attributes['keywords'] = (string)$value;
        }

        $this->attributes['keywords'] = Str::lower($this->attributes['keywords']);
    }
    //endregion

    //region GETS
    protected function getKeywordsAttribute(): string
    {
        return $this->attributes['keywords'] ?? '';
    }
    protected function getKeywordsArrayAttribute(): Collection
    {
        return collect(explode(',', $this->attributes['keywords']));
    }

    //endregion

    //endregion

}
