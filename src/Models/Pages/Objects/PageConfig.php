<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages\Objects;

use Adminx\Common\Enums\ContentEditorType;
use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use Adminx\Common\Models\Generics\DataSource;
use Adminx\Common\Models\Pages\Modules\Traits\HasPageModulesManager;
use Illuminate\Support\Collection;
use ArtisanBR\GenericModel\GenericModel;

/**
 * @property Collection|DataSource[] $sources
 */
class PageConfig extends GenericModel
{
    use HasPageModulesManager;

    /*public const MODULE_LIST = [
        'articles', 'forms', 'widgets', 'list', //todo: 'products'
    ];*/

    protected $fillable = [
        'breadcrumb',
        'allowed_modules',
        'enabled_modules',
        'editor_type',

        //todo: remove
        'modules',
        'sources',
        'allowed_source_types',
        'require_source',
        'internal_html_raw',
        //todo: template
    ];

    protected $attributes = [
        'allowed_modules'      => [],
        'enabled_modules'      => [],

        //todo: remove
        'source'               => [],
        'sources'              => [],
        'allowed_source_types' => [],
        'require_source'       => false,
        'editor_type'          => null,
    ];

    protected $casts = [
        'allowed_modules'      => 'collection',
        'enabled_modules'      => 'collection',
        'modules'              => 'collection', //todo: change to mutator
        'breadcrumb'           => BreadcrumbConfig::class,
        'editor_type'          => ContentEditorType::class,
        //todo: remove
        //'source' => DataSource::class,
        'sources'              => AsCollectionOf::class . ':' . DataSource::class,
        'allowed_source_types' => 'collection',
        'require_source'       => 'bool',
        'internal_html_raw'    => 'string',
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
