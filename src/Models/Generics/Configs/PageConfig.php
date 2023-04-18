<?php
namespace Adminx\Common\Models\Generics\Configs;

use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\Generics\DataSource;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;

/**
 * @property Collection|DataSource[] $sources
 */
class PageConfig extends GenericModel
{

    /*public const MODULE_LIST = [
        'posts', 'forms', 'widgets', 'list', //todo: 'products'
    ];*/

    protected $fillable = [
        'breadcrumb',
        'allowed_modules',
        'modules',
        'sources',
        'allowed_source_types',
        'require_source',
        'internal_html_raw',
        //todo: template
    ];

    protected $attributes = [
        'allowed_modules' => [],
        'modules' => [],
        'source' => [],
        'sources' => [],
        'allowed_source_types' => [],
        'require_source' => false,
    ];

    protected $casts = [
        'allowed_modules' => 'collection',
        'modules' => 'collection',
        //'source' => DataSource::class,
        'sources' =>  AsCollectionOf::class . ':' . DataSource::class,
        'allowed_source_types' => 'collection',
        'require_source' => 'bool',
        'breadcrumb' => BreadcrumbConfig::class,
        'internal_html_raw' => 'string',
    ];


    //region Modules
    public function canUseModule($module): bool
    {
        return $this->allowed_modules->contains($module);
    }

    public function isUsingModule($module): bool
    {
        return $this->modules->contains($module);
    }

    public function allowModule(string|array $module): self
    {
        $this->allowed_modules = $this->allowed_modules->merge(Collection::wrap($module))->unique();

        return $this;
    }

    public function useModule(string|array $module, bool $use = true): self
    {
        $this->modules = $use ? $this->modules->merge(Collection::wrap($module))->unique() : $this->modules->except(Collection::wrap($module))->unique();

        return $this;
    }
    //endregion



}
