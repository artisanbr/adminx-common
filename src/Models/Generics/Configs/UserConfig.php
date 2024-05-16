<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Configs;

use Adminx\Common\Enums\ContentEditorType;
use ArtisanLabs\GModel\GenericModel;

class UserConfig extends GenericModel
{

    protected $fillable = [
        'emails',
        'custom_permissions',
        'title',
        'editor_type',
    ];

    protected $attributes = [
        'receber_emails'     => 1,
        'custom_permissions' => 0,
        'title'              => 'Sr.',
        'editor_type'        => 'tinymce',
    ];

    protected $casts = [
        'receber_emails'     => 'bool',
        'custom_permissions' => 'bool',
        'editor_type'        => ContentEditorType::class,
    ];

    protected function getHasCustomPermissionsAttribute()
    {
        return $this->custom_permissions ?? false;
    }

    /*protected function setEditorTypeAttribute($value)
    {
        if (ContentEditorType::tryFrom($value)) {
            $this->attributes['editor_type'] = $value;
        }
        else {
            $this->attributes['editor_type'] = null;
        }
    }*/
}
