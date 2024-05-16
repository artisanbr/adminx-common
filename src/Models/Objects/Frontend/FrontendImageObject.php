<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend;

use Adminx\Common\Models\File;
use ArtisanLabs\GModel\GenericModel;

/**
 * @property File $file
 * @property string $html
 */
class FrontendImageObject extends GenericModel
{

    protected $fillable = [
        'file_id',
        'html_attributes',
        //'file',

        'url',
        'external',
        'attrs',
    ];

    protected $attributes = [
        'file_id'         => null,
        'html_attributes' => [],
        'html'            => '',
        //'file'            => null,

        'external'            => false,
        'attrs' => [],
    ];

    protected $casts = [
        'file_id'                => 'int',
        'html'                   => 'string',
        'html_attributes'        => 'collection',
        'render_html_attributes' => 'string',

        'url' => 'string',
        'external' => 'bool',
        'attrs'        => 'collection',
        'render_attrs' => 'string',
    ];

    protected $temporary = [
        'html',
        //'file'
    ];

    protected $appends = [
        'html',
        //'file'
    ];



    //region ATTRIBUTES
    //region SET

    //endregion

    //region GET
    protected function getRenderHtmlAttributesAttribute()
    {
        return $this->html_attributes->merge([
                                                 'alt' => $this->html_attributes->get('title'),
                                             ])->reduce(fn($carry, $value, $key) => $carry . $key . '="' . $value . '" ');
    }

    protected function getHtmlAttribute(): string
    {
        return "<img src=\"{$this->url}\" {$this->render_html_attributes} />";
    }

    protected function getImageAttribute(): File|null
    {
        return $this->getFileAttribute();
    }

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
