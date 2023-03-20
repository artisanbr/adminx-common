<?php

namespace ArtisanBR\Adminx\Common\App\Models;

use ArtisanBR\Adminx\Common\App\Models\Traits\HasSelect2;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasValidation;
use ArtisanBR\Adminx\Common\App\Models\Bases\EloquentModelBase;
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
