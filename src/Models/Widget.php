<?php

namespace Adminx\Common\Models;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Widgets\Objects\WidgetConfigObject;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasTemplates;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\HasMorphAssigns;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Adminx\Common\Models\Pages\Page;

class Widget extends EloquentModelBase
{
    use HasMorphAssigns, HasValidation, HasSelect2, HasTemplates;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'config',
        'type_id',
        'site_id',
        'account_id',
        'user_id',
    ];

    protected $casts = [
        'config' => WidgetConfigObject::class,
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
                Rule::unique('widgets', 'slug')->ignore($request->id),
            ],
            'description' => 'nullable|string',
            'type_id'     => 'nullable|integer|exists:widget_types,id',
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

    protected function templateName(): Attribute
    {
        $slugTrait = '';

        if(Str::contains($this->slug, '.')){
            $slugPaths = collect(explode('.', $this->slug));
            $slugTrait = Str::kebab($slugPaths->forget(0)->implode('.'));
        }
        return Attribute::make(
            get: fn() => Str::of($slugTrait)->lower()->replace('.', '')->toString(),
        );
    }

    protected function templatePath(): Attribute
    {
        $slugPath = Str::contains($this->slug, '.') ? Str::kebab(explode('.', $this->slug)[0] ?? '') : '';

        $pathBase = Str::of("widgets/" . Str::kebab($this->type->slug))->lower()->replace('.', '-')->toString();

        return Attribute::make(
            get: static fn() => $pathBase . (empty($slugPath) ? $slugPath : "/{$slugPath}"),
        );
    }
    //endregion

    //region Relations
    public function widgeteables()
    {
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

    public function widget_template(){
        return $this->modelTemplate();
    }

    //endregion
}
