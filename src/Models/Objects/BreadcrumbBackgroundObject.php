<?php

namespace Adminx\Common\Models\Objects;

use Adminx\Common\Models\File;
use ArtisanLabs\GModel\GenericModel;

/**
 * @property File $file
 */
class BreadcrumbBackgroundObject extends GenericModel
{

    protected $fillable = [
        'file_id',
        'url',
        'type',
    ];

    protected $attributes = [
        'file_id'         => null,
        'type'            => 'image',
    ];

    protected $casts = [
        'file_id'                => 'int',
        'url'                   => 'string',
    ];

    protected $temporary = [
        'html',
        'file'
    ];


    //region HELPERS
    public function loadFile(){
        if ($this->file_id ?? false) {

            if (!$this->attributes['file'] || (int)$this->attributes['file']->id !== (int)$this->file_id) {
                $this->attributes['file'] = File::find($this->file_id);
            }
        }
        else {
            $this->attributes['file'] = null;
        }

        return $this->attributes['file'];
    }
    //endregion

    //region ATTRIBUTES
    //region SET

    //endregion

    //region GET
    protected function getFileAttribute(): File|null
    {
        return $this->loadFile();
    }

    /*protected function getNameAttribute()
    {
        return $this->file->name ?? '';
    }

    protected function getUrlAttribute()
    {
        return $this->file->url ?? '';
    }

    protected function getUriAttribute()
    {
        return $this->file->uri ?? '';
    }*/
    //endregion
    //endregion

}
