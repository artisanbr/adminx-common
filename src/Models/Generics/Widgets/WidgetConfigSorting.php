<?php
namespace Adminx\Common\Models\Generics\Widgets;

use ArtisanLabs\GModel\GenericModel;
use Illuminate\Contracts\Database\Query\Builder;

class WidgetConfigSorting extends GenericModel
{

    protected $fillable = [
        'enable',
        'columns',

        'sort_column',
        'sort_direction',
    ];

    protected $attributes = [
        'enable' => false,
        'columns' => [],
    ];

    protected $casts = [
        'enable' => 'boolean',
        'columns' => 'collection'
    ];

    protected $appends = ['sort_column', 'sort_direction'];

    //region Attributes

    //region SET's
    protected function setSortColumnAttribute($value){
        $this->columns = [$value => 'desc'];
    }

    protected function setSortDirectionAttribute($value){
        $this->columns[$this->sort_column] = $value;
    }
    //endregion

    //region GET's
    protected function getSortColumnAttribute()
    {
        return $this->columns->keys()[0] ?? false;
    }

    protected function getSortDirectionAttribute()
    {
        return $this->columns->values()[0] ?? 'desc';
    }
    //endregion

    //endregion

    public function applySort(Builder $query){
        if($this->enable && $this->columns){

        }

        return $query;
    }
}
