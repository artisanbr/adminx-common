<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Templates\Objects;

use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\Pages\Modules\Traits\HasPageModulesManager;
use Adminx\Common\Models\Templates\Enums\TemplateRenderEngine;
use Adminx\Common\Models\Templates\Enums\TemplateRenderMode;
use ArtisanLabs\GModel\GenericModel;


class TemplateConfig extends GenericModel
{
    use HasPageModulesManager;

    protected $fillable = [
        'required_modules',

        'require_source',
        'source_types',

        'variables',
        'sorting',
        'paging',

        'render_mode',
        'render_engine',

        'use_files',

        'is_editable',
    ];

    protected $attributes = [
        'render_mode'   => 'ajax',
        'render_engine' => 'twig',

        'is_editable'      => true,
        'require_source'   => true,
        'source_types'     => [],
        'required_modules' => [],
        'variables'        => [],
        'sorting'          => [],
        'paging'           => [],
    ];

    protected $casts = [
        'variables' => AsCollectionOf::class . ':' . TemplateConfigVariable::class,

        'sorting' => TemplateConfigSorting::class,
        'paging'  => TemplateConfigPaging::class,


        'render_mode'   => TemplateRenderMode::class,
        'render_engine' => TemplateRenderEngine::class,

        'is_editable'      => 'boolean',
        'use_files'        => 'boolean',
        'require_source'   => 'boolean',
        'source_types'     => 'collection',
        'required_modules' => 'collection',
    ];

    //region Attributes
    //region Sets

    //endregion

    //region Gets

    //endregion
    //endregion


}
