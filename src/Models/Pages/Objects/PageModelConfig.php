<?php

namespace Adminx\Common\Models\Pages\Objects;

use Adminx\Common\Enums\ContentEditorType;
use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use Adminx\Common\Models\Generics\DataSource;
use Adminx\Common\Models\Pages\Modules\Traits\HasPageModulesManager;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;

class PageModelConfig extends GenericModel
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
