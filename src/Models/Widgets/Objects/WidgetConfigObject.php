<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Widgets\Objects;

use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\Generics\Widgets\WidgetConfigPaging;
use Adminx\Common\Models\Generics\Widgets\WidgetConfigSorting;
use Adminx\Common\Models\Generics\Widgets\WidgetConfigVariable;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;

/**s
 * @property Collection|WidgetConfigVariable[] $variables
 * @property string|null                       $sort_column
 * @property string                            $sort_direction
 *
 */
class WidgetConfigObject extends GenericModel
{

    //protected static $nullable = true;

    protected $fillable = [
        //'defines',

        'update_template',

        'require_source',
        'source_types',

        'variables',
        'sorting',
        'paging',

        'ajax_render',
    ];

    protected $attributes = [
        'ajax_render'    => true,
        'require_source' => true,
        'source_types'   => [],
        'variables'      => [],
        'sorting'        => [],
        'paging'         => [],
    ];

    protected $casts = [
        'ajax_render'  => 'bool',

        'require_source' => 'bool',
        'source_types' => 'collection',

        'variables'    => AsCollectionOf::class . ':' . WidgetConfigVariable::class,

        'sorting' => WidgetConfigSorting::class,
        'paging'  => WidgetConfigPaging::class,
    ];

    protected $temporary = ['sort_direction', 'sort_column'];

    /*protected $hidden = [
        'reference_page',
    ];*/

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

    protected function getSortColumnAttribute()
    {
        if (!($this->attributes['sort_column'] ?? false)) {
            $this->attributes['sort_column'] = $this->sorting->columns->keys()[0] ?? false;
        }

        return $this->attributes['sort_column'];
    }

    protected function getSortDirectionAttribute()
    {
        if (!($this->attributes['sort_direction'] ?? false)) {
            $this->attributes['sort_direction'] = $this->sorting->columns->values()[0] ?? 'desc';
        }

        return $this->attributes['sort_direction'];
    }
}
