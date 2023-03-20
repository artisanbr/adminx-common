<?php

namespace ArtisanBR\Adminx\Common\App\Models;

use ArtisanBR\Adminx\Common\App\Elements\Forms\FormElement;
use ArtisanBR\Adminx\Common\App\Models\Bases\EloquentModelBase;
use ArtisanBR\Adminx\Common\App\Models\Casts\AsCollectionOf;
use ArtisanBR\Adminx\Common\App\Models\Generics\Configs\FormConfig;
use ArtisanBR\Adminx\Common\App\Models\Scopes\WhereSiteScope;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasSelect2;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasSlugAttribute;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasValidation;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToSite;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToUser;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasMorphAssigns;
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
    public function posts()
    {
        return $this->morphedByMany(Post::class, 'formulable');
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
