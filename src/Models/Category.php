<?php

namespace Adminx\Common\Models;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToAccount;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\HasMorphAssigns;
use Adminx\Common\Models\Traits\Relations\HasParent;
use Adminx\Common\Models\Traits\ScopeOrganize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Category extends EloquentModelBase
{
    use HasSelect2, HasUriAttributes, ScopeOrganize, HasUriAttributes, HasMorphAssigns, HasValidation, BelongsToSite, BelongsToAccount, HasParent;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'seo',
        'parent_id',
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
                //'unique:categories,slug',
                Rule::unique('categories')->ignore($request->id)->where(function (Builder $query) use ($request) {
                    return $query->where('site_id', Auth::user()->site_id)->where('parent_id', $request->parent_id ?? null);
                }),
            ],
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|integer|exists:categories,id',
        ];
    }
    //endregion

    //region APPENDS
    protected $appends = [
        'text',
    ];

    public function text(): Attribute
    {
        return Attribute::make(get: fn() => ($this->parent && $this->parent->title ? "{$this->parent->title} &raquo; " : '') . ($this->title ?? ''),);
    }

    //endregion

    //region ATTRIBUTES

    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? Str::slug(Str::lower($this->title)),
            set: static fn($value) => Str::contains($value, ' ') ? Str::slug(Str::lower($value)) : Str::lower($value),
        );
    }

    //region GETS
    protected function getUrlAttribute()
    {
        return "/category/{$this->slug}";
    }
    //endregion
    //endregion

    //region SCOPES
    protected array $defaultOrganizeColumns = ['title', 'parent_id'];

    public function scopeRoot(Builder $query): Builder
    {
        return $query->where('parent_id', null);
    }

    public function scopeChildOf(Builder $query, $parent_id = null): Builder
    {
        return $query->where('parent_id', $parent_id);
    }

    public function scopeAssignedTo(Builder $query, $categorizable_type, $categorizable_id = null): Builder
    {
        return $this->scopeAssignedToBy($query, 'categorizables', 'categorizable_type', 'categorizable_id', $categorizable_type, $categorizable_id);
    }
    //endregion

    //region OVERRIDES
    public function save(array $options = []): bool
    {
        //Gerar slug se estiver em branco
        if (empty($this->slug)) {
            $this->slug = $this->title;
        }

        if (!$this->site_id) {
            $this->site_id = Auth::user()->site_id;
        }
        if (!$this->user_id) {
            $this->user_id = Auth::user()->id;
        }
        if (!$this->account_id) {
            $this->user_id = Auth::user()->account_id;
        }

        return parent::save($options);
    }
    //endregion

    //region RELATIONS
    public function categorizables()
    {
        return $this->hasMany(Categorizable::class, 'category_id', 'id');
    }

    public function posts()
    {
        return $this->morphedByMany(Post::class, 'categorizable');
    }

    public function pages()
    {
        return $this->morphedByMany(Page::class, 'categorizable');
    }

    public function menu_items()
    {
        return $this->morphMany(MenuItem::class, 'menuable');
    }

    //endregion
}
