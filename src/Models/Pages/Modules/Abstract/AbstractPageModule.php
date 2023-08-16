<?php

namespace Adminx\Common\Models\Pages\Modules\Abstract;

use Adminx\Common\Facades\Frontend\FrontendHtml;
use Adminx\Common\Libs\FrontendEngine\AdvancedHtmlEngine;
use Adminx\Common\Libs\FrontendEngine\FrontendHtmlEngine;
use Adminx\Common\Libs\Helpers\HtmlHelper;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Pages\Objects\PageConfig;
use Adminx\Common\Models\Interfaces\HtmlModel;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\Themes\Theme;
use Adminx\Common\Models\Traits\HasSelect2;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Database\Eloquent\Casts\Attribute;


abstract class AbstractPageModule extends GenericModel
{
    use HasSelect2;

    public string $moduleName;
    public string $moduleRelatedModel;

    protected $fillable = [
        'title',
        'description',
        'slug',
    ];

    protected $casts = [
        'slug'             => 'string',
        'description'      => 'string',
        'title'            => 'string',
    ];

    /**
     * Allowed Modules in child Pages
     */
    public array $allowedModules;

    /**
     * Auto-enabled Modules in child Pages
     */
    public array $enabledModules;

    protected $attributes = [
        'title'       => 'string',
        'description' => 'string',
        'slug'        => 'string',
    ];

    protected $appends = [
    ];

    //region Attributes
    //region GET's
    /*protected function getConfigAttribute()
    {
        return [
            ...$this->attributes['config'] ?? [],
            'enabled_modules' => $this->enabledModules,
        ];
    }*/
    //endregion

    //region SET's
    //protected function setConfigAttribute(){}

    //endregion
    //endregion

}
