<?php

namespace Adminx\Common\Models;

use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\Seo;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Scopes\WhereSiteScope;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasPublicIdUriAttributes;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasSEO;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToPage;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasCategoriesMorph;
use Adminx\Common\Models\Traits\Relations\HasComments;
use Adminx\Common\Models\Traits\Relations\HasFiles;
use Adminx\Common\Models\Traits\Relations\HasTagsMorph;
use Adminx\Common\Rules\HtmlEmptyRule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class Post extends EloquentModelBase implements PublicIdModel, OwneredModel
{
    use HasUriAttributes, HasSelect2, SoftDeletes, HasValidation, HasSEO, HasFiles, BelongsToPage, BelongsToUser, BelongsToSite, HasCategoriesMorph, HasTagsMorph, HasComments, HasOwners, HasPublicIdUriAttributes, HasPublicIdAttribute;

    protected $fillable = [
        'site_id',
        'user_id',
        'page_id',
        'account_id',
        'title',
        'description',
        'content',
        'slug',
        'cover_id',
        'seo',
        'published_at',
        'unpublished_at',
    ];

    protected $attributes = [
        //'cover_type' => 'image',
    ];

    protected $casts = [
        'seo'            => Seo::class,
        'published_at'   => 'datetime:d/m/Y H:i',
        'unpublished_at' => 'datetime:d/m/Y H:i',
        'created_at'     => 'datetime:d/m/Y H:i:s',
        'updated_at'     => 'datetime:d/m/Y H:i:s',
        'content'     => 'string',
        'html'     => 'string',
        'description'     => 'string',
    ];

    protected $appends = [
        'text',
    ];

    protected $touches = ['page'];

    protected $dates = ['published_at','unpublished_at'];

    //region VALIDATION
    public static function createRules(FormRequest $request = null): array
    {
        return [
            'title'   => ['required'],
            'content' => [new HtmlEmptyRule],
            'cover_file' => ['nullable', 'file']
        ];
    }

    public static function createMessages(): array
    {
        return [
            'title.required' => __('O título é obrigatório'),
        ];
    }
    //endregion

    //region HELPERS

    public function limitContent($limit = 300)
    {
        return Str::limit(strip_tags($this->content), $limit);
    }

    public function seoDescription(): string
    {
        return $this->seo->description ?? $this->description;
    }

    public function getDescription(): string
    {
        return $this->seoDescription();
    }

    public function getKeywords(): string
    {
        return $this->seoKeywords($this->page->getKeywords());
    }

    public function getRobots(): string
    {
        return $this->seo->robots ?? $this->page->getRobots();
    }

    //endregion

    //region ATTRIBUTES
    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? Str::slug(Str::lower($this->title)),
            set: static fn($value) => Str::slug(Str::lower($value)),
        );
    }

    protected function next(): Attribute
    {
        // get next user
        return Attribute::make(
            get: fn($value) => self::where('id', '>', $this->id)->orderBy('id','asc')->first()
        );

    }

    protected function previous(): Attribute
    {
        // get next user
        return Attribute::make(
            get: fn($value) => self::where('id', '<', $this->id)->orderBy('id','desc')->first()
        );

    }

    //region GETS

    /*protected function getPublishedAtAttribute($value)
    {
        return empty($value) ? $this->created_at : Carbon::parse($value) ;
    }*/

    protected function getPublicIdUrlAttribute(): string
    {

        return $this->page->public_id_url ? "{$this->page->public_id_url}/post/{$this->public_id}" : '';
    }

    protected function getUrlAttribute(): string
    {

        return $this->page->url ? "{$this->page->url}/post/" . ($this->slug ?? $this->public_id) : '';
    }

    protected function getDescriptionAttribute()
    {
        return $this->attributes['description'] ?? $this->limitContent();
    }
    //endregion
    //endregion

    //region SCOPES

    public function scopePublished(Builder $query)
    {
        return $query->where(function (Builder $q) {
            $q->where('published_at', null)->orWhere('published_at', '<=', Carbon::now());
            $q->where('unpublished_at', null)->orWhere('unpublished_at', '>=', Carbon::now());
        })->with(['cover','categories','tags','comments']);
    }

    public function scopeOrdered(Builder $query)
    {
        return $query->orderByDesc('published_at')->orderByDesc('updated_at')->orderByDesc('created_at')->orderBy('title');
    }

    public function scopeOrderedAsc(Builder $query)
    {
        return $query->orderBy('published_at')->orderBy('created_at')->orderBy('title');
    }

    public function scopeWithAll(Builder $query)
    {
        return $query->with(['page','site','user','cover','categories','tags','comments']);
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

        return parent::save($options);
    }

    public function delete()
    {
        //Todo: permissions

        return parent::delete(); // TODO: Change the autogenerated stub
    }

    //endregion

    //region RELATIONS

    public function cover()
    {
        return $this->hasOne(File::class, 'id', 'cover_id');
    }

    public function menu_items()
    {
        return $this->morphMany(MenuItem::class, 'menuable',);
    }

    //endregion
}
