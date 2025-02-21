<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\Values;

use ArtisanLabs\GModel\GenericModel;

class PDFValue extends GenericModel
{

    protected $fillable = [
        //'url',
        'title',
        'description',
        'path',
        'position',
    ];

    protected $casts = [
        'url'             => 'string',
        'title'             => 'string',
        'description'             => 'string',
        'path'             => 'string',
        'position' => 'int',
    ];

    protected $attributes = [];

    protected $appends = ['url'];


    protected function getUrlAttribute(): string|null
    {
        return "/storage/{$this->path}";
    }

}
