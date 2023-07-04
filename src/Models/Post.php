<?php

namespace Adminx\Common\Models;

use Adminx\Common\Facades\Frontend\FrontendHtml;
use Adminx\Common\Libs\Helpers\DateTimeHelper;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Interfaces\UploadModel;
use Adminx\Common\Models\Objects\Frontend\Assets\FrontendAssetsBundle;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Objects\Seo\Seo;
use Adminx\Common\Models\Scopes\WhereSiteScope;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasPublicIdUriAttributes;
use Adminx\Common\Models\Traits\HasPublishTimestamps;
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
use Butschster\Head\Facades\Meta;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Post extends EloquentModelBase implements PublicIdModel, OwneredModel, UploadModel
{
    use HasUriAttributes, HasSelect2, HasPublishTimestamps, SoftDeletes, HasValidation, HasSEO, HasFiles, BelongsToPage, BelongsToUser, BelongsToSite, HasCategoriesMorph, HasTagsMorph, HasComments, HasOwners, HasPublicIdUriAttributes, HasPublicIdAttribute;

    protected $fillable = [
        'site_id',
        'user_id',
        'page_id',
        'account_id',
        'title',
        'description',
        'content',
        'slug',
        'cover_url',
        'cover_id',
        'assets',
        'seo',
        'published_at',
        'unpublished_at',
        'unpublish',
    ];

    protected $attributes = [
        //'cover_type' => 'image',
    ];

    protected $casts = [
        'seo'            => Seo::class,
        'assets'         => FrontendAssetsBundle::class,
        'published_at'   => 'datetime:d/m/Y H:i:s',
        'unpublished_at' => 'datetime:d/m/Y H:i:s',
        'created_at'     => 'datetime:d/m/Y H:i:s',
        'updated_at'     => 'datetime:d/m/Y H:i:s',
        'content'        => 'string',
        'is_published'   => 'bool',
        'is_unpublished' => 'bool',
        //'html'     => 'string',
        //'description'    => 'string',
    ];

    protected $appends = [
        'text',
    ];

    protected $touches = ['page'];


    //region VALIDATION
    public static function createRules(FormRequest $request = null): array
    {
        return [
            'title'      => ['required'],
            'content'    => [new HtmlEmptyRule],
            'cover_file' => ['nullable', 'file'],
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

    //region SEO
    public function seoDescription(): string
    {
        return $this->seo->description ?? $this->limitContent();
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

    public function limitContent($limit = 300): string
    {

        return new HtmlString(Str::limit(strip_tags($this->content, '<p><br><strong><em><del><b><i>'), $limit));

        //return Str::limit(strip_tags($this->content), $limit);
    }

    public function getBuildViewPath(): string
    {
        return $this->page->getBuildViewPath('post');
    }

    public function getBuildViewData(array $merge_data = []): array
    {
        $viewData = [
            'post'          => $this,
            'frontendBuild' => $this->frontendBuild(),
            'comments'      => $this->comments()->paginate(5, ['*'], 'comments_page'),
            'breadcrumbs'   => [$this->seoTitle()],
        ];

        return [...$this->page->getBuildViewData($viewData), ...$merge_data];
    }

    public function frontendBuild(): FrontendBuildObject
    {
        $frontendBuild = $this->page->frontendBuild();

        //Gtag
        $frontendBuild->head->gtag_script = $this->getGTagScript();

        //Antes inicio da tag head
        $frontendBuild->head->addBefore(Meta::toHtml());
        $frontendBuild->head->css .= $this->css_html;

        //Fim d atag head
        $frontendBuild->head->addAfter($this->assets->js->head_html ?? '');
        $frontendBuild->head->addAfter($this->assets->head_script->html ?? '');

        //JSON-LD
        $frontendBuild->head->addAfter($this->ld_json_script);

        //Inicio do body
        $frontendBuild->body->id = "post-{$this->public_id}";
        $frontendBuild->body->class .= " post-{$this->public_id}";
        $frontendBuild->body->addBefore($this->assets->js->before_body_html ?? '');

        //Fim do body
        $frontendBuild->body->addAfter($this->assets->js->after_body_html ?? '');

        return $frontendBuild;
    }

    //endregion

    public function uploadPathTo(?string $path = null): string
    {
        $uploadPath = "posts/{$this->public_id}";

        return ($this->page ? $this->page->uploadPathTo($uploadPath) : $uploadPath) . ($path ? "/{$path}" : '');
    }

    //region ATTRIBUTES
    protected function ldJsonScript(): Attribute
    {
        return Attribute::make(
            get: fn() => '<script type="application/ld+json">' . json_encode(
                    [
                        "@context"      => "https://schema.org",
                        "@type"         => "NewsArticle",
                        "headline"      => $this->title,
                        "image"         => [
                            $this->seoImage(),
                        ],
                        "datePublished" => $this->published_at->toIso8601String(),
                        "dateModified"  => $this->updated_at->toIso8601String(),
                        "author"        => [
                            [
                                "@type" => "Organization",
                                "name"  => $this->site->title,
                                "url"   => $this->uri,
                            ],
                        ],
                    ]
                ) . '</script>',
        );
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value,
            set: static fn($value) => Str::slug(Str::lower($value)),
        );
    }

    protected function next(): Attribute
    {
        // get next user
        return Attribute::make(
            get: fn($value) => self::where('id', '>', $this->id)->orderBy('id', 'asc')->first()
        );

    }

    protected function previous(): Attribute
    {
        // get next user
        return Attribute::make(
            get: fn($value) => self::where('id', '<', $this->id)->orderBy('id', 'desc')->first()
        );

    }

    protected function buildedHtml(): Attribute
    {
        $page = $this;

        return Attribute::make(
            get: fn() => FrontendHtml::post($this),
        );
    }

    protected function isPublished(): Attribute
    {

        $published = true;

        if ($this->published_at && $this->published_at->greaterThan(Carbon::now())) {
            $published = false;
        }

        if ($this->unpublished_at && $this->unpublished_at->lessThan(Carbon::now())) {
            $published = false;
        }


        return Attribute::make(
            get: fn() => $published
        );
    }

    protected function isUnpublished(): Attribute
    {
        return Attribute::make(
            get: fn() => !$this->is_published
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
        $urlId = $this->slug ?? $this->public_id;

        return $this->page->urlTo("post/{$urlId}/");
        //return $this->page->url ? "{$this->page->url}/post/" . ($this->slug ?? $this->public_id) . '/' : '';
    }

    /*protected function getDescriptionAttribute()
    {
        return $this->attributes['description'] ?? $this->limitContent();
    }*/
    //endregion

    //region SETS

    //endregion
    //endregion

    //region SCOPES

    public function scopePublished(Builder $query)
    {
        return $query->where(function (Builder $q) {
            $q->where('published_at', null)->orWhere('published_at', '<=', Carbon::now());
            $q->where('unpublished_at', null)->orWhere('unpublished_at', '>=', Carbon::now());
        })->with(['categories', 'tags', 'comments']);
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
        return $query->with(['page', 'site', 'categories', 'tags', 'comments']);
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

    public function menu_items()
    {
        return $this->morphMany(MenuItem::class, 'menuable',);
    }

    public function cover()
    {
        return $this->hasOne(File::class, 'id', 'cover_id');
    }

    //endregion
}
