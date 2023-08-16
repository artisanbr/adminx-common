<?php

namespace Adminx\Common\Models\Templates\Objects;

use Adminx\Common\Enums\ContentEditorType;
use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use Adminx\Common\Models\Generics\DataSource;
use Adminx\Common\Models\Generics\Widgets\WidgetConfigPaging;
use Adminx\Common\Models\Generics\Widgets\WidgetConfigSorting;
use Adminx\Common\Models\Generics\Widgets\WidgetConfigVariable;
use Adminx\Common\Models\Pages\Modules\Traits\HasPageModulesManager;
use Adminx\Common\Models\Templates\Enums\TemplateRenderMode;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;


class TemplateConfig extends GenericModel
{
    use HasPageModulesManager;

    protected $fillable = [
        'required_modules',

        'variables',
        'sorting',
        'paging',

        'render_mode',
        'is_editable',
    ];

    protected $attributes = [
        'render_mode'      => 'ajax',
        'is_editable'      => true,
        'required_modules' => [],
        'variables'        => [],
        'sorting'          => [],
        'paging'           => [],
    ];

    protected $casts = [
        'required_modules' => 'collection',
        'variables'        => AsCollectionOf::class . ':' . TemplateConfigVariable::class,

        'sorting' => TemplateConfigSorting::class,
        'paging'  => TemplateConfigPaging::class,
        'render_mode'  => TemplateRenderMode::class,
        'is_editable'      => 'boolean',
    ];

    //region Attributes
    //region Sets

    //endregion

    //region Gets

    //endregion
    //endregion


}
