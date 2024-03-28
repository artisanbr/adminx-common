<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Bases\Generic;

use Adminx\Common\Models\File;
use ArtisanBR\GenericModel\GenericModel;

/**
 * @property File $file
 */
abstract class GenericFileBase extends GenericModel
{

    protected $fillable = [
        'file_id',
        'html_attributes',
        //'file',
    ];

    protected $attributes = [
        'file_id'         => null,
        'html_attributes' => [],
        'html'            => '',
        'file'            => null,
    ];

    protected $casts = [
        'file_id'                => 'int',
        'html'                   => 'string',
        'html_attributes'        => 'collection',
        'render_html_attributes' => 'string',
    ];

    protected $temporary = [
        'html',
        'file'
    ];

    /*protected $appends = [
        'html',
        'render_html_attributes',
    ];*/


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

    protected function getNameAttribute()
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
    }

    protected function getRenderHtmlAttributesAttribute()
    {
        return $this->html_attributes->merge([
                                                 'alt' => $this->html_attributes->get('title'),
                                             ])->reduce(fn($carry, $value, $key) => $carry . $key . '="' . $value . '" ');
    }

    protected function getHtmlAttribute(): string
    {
        $fileUrl = $this->file->url ?? '';
        $fileTitle = $this->file->title ?? '';

        return "<a src='{$fileUrl}' {$this->render_html_attributes}>{$fileTitle}</a>";
    }
    //endregion
    //endregion

}
