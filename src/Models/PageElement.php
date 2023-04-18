<?php

namespace Adminx\Common\Models;

use Adminx\Common\Elements\Collections\PageElementCollection;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\Configs\PageConfig;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageElement extends EloquentModelBase
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'parent_id',
        'user_id',
        'widget_id',
        'position',
        'type',
        'title',
        'html',
        'css',
        'js',
        'config',
    ];

    protected $casts = [
        'config' => PageConfig::class,
    ];

    //region TYPES DE ELEMENTOS
    public const ELEMENT_TYPE_SECTION = 0;
    public const ELEMENT_TYPE_GROUP = 1;
    public const ELEMENT_TYPE_COMPONENT = 2;
    public const ELEMENT_TYPE_WIDGET = 3;
    //endregion

    //region OVERRIDES

    public function newCollection(array $models = []): PageElementCollection
    {
        return new PageElementCollection($models);
    }

    public function newQuery()
    {
        return parent::newQuery()->orderBy('position')->orderBy('created_at');
    }

    //endregion

    //region SCOPES

    public function scopeType(Builder $query, $type): Builder
    {
        return $query->where('type', $type);
    }
    public function scopeSections(Builder $query): Builder
    {
        return $query->where('type', self::ELEMENT_TYPE_SECTION);
    }
    public function scopeGroups(Builder $query): Builder
    {
        return $query->where('type', self::ELEMENT_TYPE_GROUP);
    }
    public function scopeComponents(Builder $query): Builder
    {
        return $query->where([
            ['type', '!=', self::ELEMENT_TYPE_SECTION],
            ['type', '!=', self::ELEMENT_TYPE_GROUP]
        ]);
    }

    //endregion

    //region RELATIONS

    public function page(){
        return $this->belongsTo(Page::class);
    }

    public function children(){
        return $this->hasMany(PageElement::class, 'parent_id', 'id');
    }

    public function groups(){
        return $this->childs()->groups();
    }

    public function parent(){
        return $this->belongsTo(PageElement::class);
    }

    public function widget(){
        return $this->hasOne(Widget::class, 'id', 'widget_id');
    }

    //endregion

}
