<?php

namespace ArtisanBR\Adminx\Common\App\Models\Generics\Configs;

use ArtisanBR\Adminx\Common\App\Models\Casts\AsCollectionOf;
use ArtisanBR\Adminx\Common\App\Models\Generics\Widgets\WidgetConfigPaging;
use ArtisanBR\Adminx\Common\App\Models\Generics\Widgets\WidgetConfigSorting;
use ArtisanBR\Adminx\Common\App\Models\Generics\Widgets\WidgetConfigVariable;
use ArtisanBR\Adminx\Common\App\Models\Page;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;

/**
 * @property Collection|WidgetConfigVariable[] $variables
 * @property string|null                       $sort_column
 * @property string                            $sort_direction
 *
 */
class WidgetConfig extends GenericModel
{

    //protected static $nullable = true;

    protected $fillable = [
        'variables',
        'defines',
        'require_source',

        'sorting',
        'paging',

        'source_types',
        'ajax_render',
    ];

    protected $attributes = [
        'require_source' => true,
        'ajax_render'    => true,
        'source_types'   => [],
        'variables'      => [],
        'sorting'        => [],
        'paging'         => [],
    ];

    protected $casts = [
        'require_source' => 'bool',

        'ajax_render'  => 'bool',
        'source_types' => 'collection',
        'variables'    => AsCollectionOf::class . ':' . WidgetConfigVariable::class,

        'sorting' => WidgetConfigSorting::class,
        'paging'  => WidgetConfigPaging::class,
    ];

    protected $temporary = ['reference_page'];

    protected $hidden = [
        'reference_page',
    ];

    //region HELPERS
    public function variable($slug, $attribute = null)
    {
        $variable = $this->variables->firstWhere('slug', $slug);

        return $attribute ? $variable->{$attribute} ?? null : $variable;
    }

    public function variableValue($slug, $defaultValue = null)
    {
        return $this->variable($slug)->value ?? $defaultValue;
    }

    public function loadReferencePage()
    {
        if ($this->reference_page_id ?? false) {

            if (!$this->attributes['reference_page'] || (int)$this->attributes['reference_page']->id !== (int)$this->reference_page_id) {
                $this->attributes['reference_page'] = Page::find($this->reference_page_id);
            }
        }
        else {
            $this->attributes['reference_page'] = null;
        }

        return $this->attributes['reference_page'];
    }

    //endregion

    protected function getReferencePageAttribute()
    {
        return $this->loadReferencePage();
    }

    protected function getSortColumnAttribute()
    {
        return $this->sorting->columns->keys()[0] ?? false;
    }

    protected function getSortDirectionAttribute()
    {
        return $this->sorting->columns->values()[0] ?? 'desc';
    }
}
