<?php

namespace Adminx\Common\Models\Pages\Types\Abstract;

use Adminx\Common\Facades\Frontend\FrontendHtml;
use Adminx\Common\Libs\FrontendEngine\AdvancedHtmlEngine;
use Adminx\Common\Libs\FrontendEngine\FrontendHtmlEngine;
use Adminx\Common\Libs\Helpers\HtmlHelper;
use Adminx\Common\Models\Pages\Objects\PageConfig;
use Adminx\Common\Models\Interfaces\HtmlModel;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Pages\Modules\Traits\HasPageModulesManager;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\Theme;
use Adminx\Common\Models\Traits\HasSelect2;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Collection;

/**
 * @property array{slug: string, title: string, description: string, allowed_modules: Collection|string[],enabled_modules: Collection|string[] }|null $attributes
 * @param array{slug: string, title: string, description: string, allowed_modules: Collection|string[],enabled_modules: Collection|string[] }|null $attributes
 */
abstract class AbstractPageType extends GenericModel
{
    use HasPageModulesManager, HasSelect2;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'allowed_modules',
        'enabled_modules',
    ];

    protected $casts = [
        'config'      => PageConfig::class,
        'slug'        => 'string',
        'description' => 'string',
        'title'       => 'string',

        'allowed_modules' => 'collection',
        'enabled_modules' => 'collection',

        'can_use_forms'    => 'bool',
        'can_use_articles' => 'bool',
    ];

    protected $attributes = [
        'title'           => 'string',
        'description'     => 'string',
        'slug'            => 'string',
        'allowed_modules' => [],
        'enabled_modules' => [],
    ];

    protected $appends = [
        //'can_use_forms',
        //'can_use_articles',
    ];

    //region Attributes
    //region GET's
    protected function getConfigAttribute()
    {
        return new PageConfig([
                                  ...$this->attributes['config'] ?? [],
                                  'allowed_modules' => $this->attributes['allowed_modules'],
                                  'enabled_modules' => $this->attributes['enabled_modules'],
                              ]);
    }
    //endregion

    //region SET's
    //protected function setConfigAttribute(){}

    //endregion
    //endregion

}