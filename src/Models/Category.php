<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models;

use Adminx\Common\Facades\Frontend\FrontendPage;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Scopes\WhereSiteScope;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToAccount;
use Adminx\Common\Models\Traits\Relations\BelongsToPage;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasMorphAssigns;
use Adminx\Common\Models\Traits\Relations\HasParent;
use Adminx\Common\Models\Traits\ScopeOrganize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Category extends EloquentModelBase implements OwneredModel
{
    use HasSelect2, HasUriAttributes, ScopeOrganize, HasUriAttributes, HasMorphAssigns, HasValidation, BelongsToSite, BelongsToAccount, HasParent, BelongsToUser, HasOwners, BelongsToPage, SoftDeletes;

    protected $fillable = [
        'site_id',
        'account_id',
        'user_id',
        'page_id',
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
        $url = "/category/{$this->slug}";

        if ($this->page_id && $this->page) {
            return $this->page->urlTo($url);
        }

        //Get by currentPage our relatedPage / todo: remove later

        $currentPage = @FrontendPage::current() ?? null;


        if (!$currentPage && @$this->pivot->pivotParent) {
            $model = $this->pivot->pivotParent;

            if (get_class($model) === Page::class) {
                $currentPage = $model;
            }
            else if (method_exists($model, 'page') && get_class($model->page) === Page::class) {
                $currentPage = $model->page;
            }
        }

        if ($currentPage) {
            return $currentPage->urlTo($url);
        }

        return $url;
    }
    //endregion
    //endregion

    //region SCOPES
    public function scopeWhereUrl(Builder $query, string $url): Builder
    {
        return $query->where(static function (Builder $q) use ($url) {
            $q->where('slug', $url);
            $q->orWhere('id', $url);
        });
    }

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
    protected static function booted()
    {
        static::addGlobalScope(new WhereSiteScope);
    }

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

    public function articles()
    {
        return $this->morphedByMany(Article::class, 'categorizable');
    }

    /*public function pages()
    {
        return $this->morphedByMany(Page::class, 'categorizable');
    }*/

    public function menu_items()
    {
        return $this->morphMany(\Adminx\Common\Models\Menus\MenuItem::class, 'menuable');
    }

    //endregion
}
