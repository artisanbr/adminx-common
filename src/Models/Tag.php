<?php

namespace Adminx\Common\Models;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\Relations\BelongsToAccount;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;

class Tag extends EloquentModelBase
{
    use HasSelect2, HasUriAttributes, BelongsToSite, BelongsToAccount;

    protected $fillable = [
        'site_id',
        'user_id',
        'account_id',
        'title',
    ];

    //region APPENDS
    protected $appends = [
        'text',
        'slug'
    ];
    //endregion

    //region ATTRIBUTES

    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? Str::slug(Str::lower($this->title)),
            set: fn($value) => Str::slug(Str::lower($value)),
        );
    }

    //region GETS
    protected function getUrlAttribute(){
        return "/tag/{$this->slug}";
    }
    //endregion
    //endregion

    //region SCOPES
    public function scopeOrganize(Builder $query, $parent_id = null): Builder
    {
        return $query->orderBy('title')->orderBy('updated_at')->orderBy('created_at');
    }

    public function scopeTaggableBy(Builder $query, $taggable_type, $taggable_id = null): Builder
    {

        $taggable_type = Str::contains(Str::lower($taggable_type), 'models') ? $taggable_type : "App\\Models\\{$taggable_type}";

        return $query->whereHas('taggable', function (Builder $q) use ($taggable_type, $taggable_id) {

            $q->where('taggable_type', $taggable_type);

            if ($taggable_id) {
                $q->where('taggable_id', $taggable_id);
            }
        });
    }
    //endregion

    //region OVERRIDES
    public function save(array $options = []): bool
    {
        if(!$this->site_id){
            $this->site_id = Auth::user()->site_id;
        }
        if(!$this->user_id){
            $this->user_id = Auth::user()->id;
        }
        if(!$this->account_id){
            $this->user_id = Auth::user()->account_id;
        }

        $this->slug = $this->title;
        return parent::save($options);
    }
    //endregion

    //region RELATIONS
    public function taggable(){
        return $this->morphTo();
        //return $this->hasMany(Taggable::class, 'tag_id', 'id');
    }

    public function posts(){
        return $this->morphedByMany(Post::class, 'taggable');
    }
    public function pages(){
        return $this->morphedByMany(Page::class, 'taggable');
    }

    //endregion
}
