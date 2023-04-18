<?php

namespace Adminx\Common\Models;

use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Illuminate\Database\Eloquent\Casts\Attribute;

class WidgetType extends EloquentModelBase
{
    use HasValidation, HasSelect2;

    protected $fillable = [
        'description',
        'slug',
        'title',
    ];

    protected $appends = [
        'text',
    ];

    //region Attributes
    protected function text(): Attribute
    {
        return Attribute::make(
            get: fn() => "<h2>{$this->title}</h2>{$this->description}",
        );
    }
    //endregion

    //region Relations

    public function widgets(){
        return $this->hasMany(Widget::class, 'type_id', 'id');
    }

    //endregion
}
