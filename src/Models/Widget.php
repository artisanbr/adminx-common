<?php

namespace Adminx\Common\Models;

use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\Configs\WidgetConfig;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\HasMorphAssigns;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Widget extends EloquentModelBase
{
    use HasMorphAssigns, HasValidation, HasSelect2;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'config',
        'type_id'
    ];

    protected $casts = [
        'config' => WidgetConfig::class
    ];

    protected $appends = [
        'text',
    ];

    //region VALIDATION
    public static function createRules(FormRequest $request = null): array
    {
        return [
            'title'       => 'required|string|max:255',
            'slug'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('widgets', 'slug')->ignore($request->id)
            ],
            'description' => 'nullable|string',
            'type_id'   => 'nullable|integer|exists:widget_types,id',
        ];
    }
    //endregion

    //region Attributes
    protected function text(): Attribute
    {
        return Attribute::make(
            get: fn() => "<h2>{$this->title}</h2>{$this->description}",
        );
    }
    //endregion

    //region Relations
    public function widgeteables(){
        return $this->hasMany(SiteWidget::class, 'widget_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(WidgetType::class);
    }

    public function pages()
    {
        return $this->morphedByMany(Page::class, 'widgeteable')->using(SiteWidget::class);
    }

    //endregion
}
