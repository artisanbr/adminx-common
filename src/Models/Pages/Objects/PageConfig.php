<?php
/*
 * Copyright (c) 2024-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages\Objects;

use Adminx\Common\Enums\ContentEditorType;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use Adminx\Common\Models\Generics\Configs\CaptchaConfig;
use ArtisanLabs\GModel\GenericModel;


class PageConfig extends GenericModel
{

    /*public const MODULE_LIST = [
        'articles', 'forms', 'widgets', 'list', //todo: 'products'
    ];*/

    protected $fillable = [
        'breadcrumb',
        'editor_type',
        'captcha',
        'items_per_page',
    ];

    protected $attributes = [
        'captcha' => [],
        'editor_type' => null,
    ];

    protected $casts = [
        'breadcrumb'           => BreadcrumbConfig::class,
        'editor_type'          => ContentEditorType::class,
        'allowed_source_types' => 'collection',
        'require_source'       => 'bool',
        'items_per_page'       => 'int',
        'captcha'              => CaptchaConfig::class,
    ];

    //region Attributes
    //region Sets
    protected function setModulesAttribute($value)
    {
        $this->enabled_modules = $value;
    }
    //endregion

    //region Gets
    /*protected function getEnabledModulesAttribute(): Collection
    {
        return collect($this->attributes['enabled_modules'] ?? $this->attributes['modules']);
    }*/

    protected function getModulesAttribute()
    {
        return $this->enabled_modules;
    }
    //endregion
    //endregion


}
