<?php

namespace Adminx\Common\Models\Generics\Seo;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\File;
use Adminx\Common\Models\Generics\Seo\Config\SeoConfig;
use Illuminate\Support\Collection;
use ArtisanLabs\GModel\GenericModel;

/**
 * @property File|null $image
 */
class Seo extends GenericModel
{

    protected $fillable = [
        'title',
        'image',
        'image_id',
        'description',
        'keywords',
        'robots',
        'config'
    ];

    protected $attributes = [
        'title'       => '',
        'description' => '',
        'keywords'    => '',
        'robots'      => 'index, follow',
        'config'      => [],
    ];

    protected $casts = [
        'title'          => 'string',
        'image'          => 'object',
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

    protected File|null $imageCache = null;


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

    protected function getImageIdAttribute()
    {
        return $this->attributes['image_id'] ?? null;
    }

    protected function getImageAttribute(): File|null
    {
        if($this->image_id){
            if(empty($this->imageCache) || (int) $this->imageCache->id !== (int) $this->image_id){
                $this->imageCache = File::find($this->attributes['image_id']);
            }
        }else{
            $this->imageCache = null;
        }

        return $this->imageCache;
    }
    //endregion

    //endregion

}
