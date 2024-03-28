<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Assets\Abstract;

use Adminx\Common\Libs\Support\Str;

abstract class AbstractFrontendAssetsResourceScript extends GenericModel
{
    protected $fillable = [
        'path',
        'url',
        'external',
        //'theme_path',
        'position',
        'load_mode',
        'html_attributes',
    ];

    protected $casts = [
        'path'            => 'string',
        'url'            => 'string',
        'name'            => 'string',
        //'theme_path'      => 'string',
        'position'        => 'int',
        'load_mode'           => 'string',
        'external'        => 'boolean',
        'html_attributes' => 'collection',
        //Computed
        'html'            => 'string',
    ];

    protected $attributes = [
        'position' => 0,
        'load_mode'    => 'default',
    ];

    abstract protected function getHtmlAttribute();


    //region Attributes
    //region GET's
    protected function getIdAttribute(): string
    {
        return 'file-' . Str::replace(['/', '.'], '-', $this->path);
    }

    protected function getNameAttribute(): string
    {
        return collect(explode('/', $this->path))->last();
    }

    protected function getPathAttribute()
    {
        return $this->url;
    }
    //endregion

    //region SET's
    protected function setPathAttribute($value): self
    {
        return $this->setUrlAttribute($value);
    }

    protected function setUrlAttribute($value): self
    {
        $this->attributes["url"] = $value;

        if (Str::startsWith($value, ['http', '//'])) {
            $this->external = true;
        }

        return $this;
    }

    //endregion
    //endregion
}
