<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models;

use Adminx\Common\Elements\Forms\FormElement;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\Generics\Configs\FormConfig;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Scopes\WhereSiteScope;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasSlugAttribute;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasMorphAssigns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * @property Collection|FormElement[] $elements
 */
class Form extends EloquentModelBase
{
    use HasValidation, HasSelect2, HasMorphAssigns, HasSlugAttribute, BelongsToUser, BelongsToSite;

    protected $fillable = [
        'title',
        'slug',
        'config',
        'elements',
        'events',
    ];

    protected $attributes = [
        'elements' => [],
        //'events' => [],
    ];

    protected $casts = [
        'config' => FormConfig::class,
        'elements' => AsCollectionOf::class.':'.FormElement::class,
        'events' => 'collection'
    ];

    protected $with = ['site'];

    //region VALIDATIONS
    public static function createRules(FormRequest $request = null): array
    {
        return [
            'title' => ['required'],
            'elements' => ['required','array'],
        ];
    }
    //endregion

    //region APPENDS
    protected $appends = [
        'text',
    ];

    /*public function text(): Attribute
    {
        return Attribute::make(get: fn() => ($this->parent && $this->parent->title ? "{$this->parent->title} &raquo; " : '') . ($this->title ?? ''),);
    }*/

    //endregion

    //region SCOPES
    public function scopeAssignedTo(Builder $query, $formulable_type, $formulable_id = null): Builder
    {
        return $this->scopeAssignedToBy($query, 'formulables', 'formulable_type', 'formulable_id', $formulable_type, $formulable_id);
    }
    //endregion

    //region ATTRIBUTES
    //endregion

    //region OVERRIDES
    protected static function booted()
    {
        static::addGlobalScope(new WhereSiteScope);
    }

    public function save(array $options = []): bool
    {

        if (!$this->site_id) {
            $this->site_id = Auth::user()->site_id;
        }
        if (!$this->user_id) {
            $this->user_id = Auth::user()->id;
        }
        if(!$this->slug){
            $this->slug = $this->title;
        }

        //dd($this->toArray());

        return parent::save($options);
    }
    //endregion

    //region RELATIONS
    public function formulables()
    {
        return $this->hasMany(Formulable::class, 'form_id', 'id');
    }

    //region Morphs
    public function articles()
    {
        return $this->morphedByMany(Article::class, 'formulable');
    }
    public function pages()
    {
        return $this->morphedByMany(Page::class, 'formulable');
    }
    //endregion

    public function answers()
    {
        return $this->hasMany(FormAnswer::class, 'form_id', 'id');
    }
    //endregion
}
