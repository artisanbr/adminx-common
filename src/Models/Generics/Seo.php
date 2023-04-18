<?php

namespace Adminx\Common\Models\Generics;

use Adminx\Common\Models\File;
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
    ];

    protected $attributes = [
        'title'       => '',
        'description' => '',
        'keywords'    => '',
        'robots'      => 'index, follow',
    ];

    protected $casts = [
        'title'          => 'string',
        'image'          => 'object',
        'description'    => 'string',
        'keywords'       => 'string',
        'keywords_array' => 'collection',
        'robots'         => 'string',
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
