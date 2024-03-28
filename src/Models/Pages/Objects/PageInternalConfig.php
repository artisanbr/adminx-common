<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages\Objects;

use Adminx\Common\Enums\ContentEditorType;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use ArtisanBR\GenericModel\Model as GenericModel;

class PageInternalConfig extends GenericModel
{

    protected $fillable = [
        'breadcrumb',
        'editor_type',
    ];

    protected $attributes = [
        'editor_type'          => null,
    ];

    protected $casts = [
        'breadcrumb'           => BreadcrumbConfig::class,
        'editor_type'          => ContentEditorType::class,
    ];


}
