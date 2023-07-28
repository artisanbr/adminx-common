<?php

namespace Adminx\Common\Models\Templates\Objects;

use Adminx\Common\Enums\ContentEditorType;
use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use Adminx\Common\Models\Generics\DataSource;
use Adminx\Common\Models\Pages\Modules\Traits\HasPageModulesManager;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;



class TemplateConfig extends GenericModel
{
    use HasPageModulesManager;

    protected $fillable = [
        'required_modules',
    ];

    protected $attributes = [
        'required_modules'      => [],
    ];

    protected $casts = [
        'required_modules'      => 'collection',
    ];

    //region Attributes
    //region Sets

    //endregion

    //region Gets

    //endregion
    //endregion


}
