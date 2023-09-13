<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models;

use Adminx\Common\Facades\Frontend\FrontendTwig;
use Adminx\Common\Libs\Support\HtmlString;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Interfaces\UploadModel;
use Adminx\Common\Models\Menus\MenuItem;
use Adminx\Common\Models\Objects\ArticleMetaObject;
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
use Adminx\Common\Models\Traits\HasSiteRoutes;
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

/**
 * @property Seo                  $seo
 * @property FrontendAssetsBundle $assets
 */
class Article extends EloquentModelBase implements PublicIdModel, OwneredModel, UploadModel
{
    use HasUriAttributes, HasSelect2, HasPublishTimestamps, SoftDeletes, HasValidation, HasSEO, HasFiles, BelongsToPage, BelongsToUser, BelongsToSite, HasCategoriesMorph, HasTagsMorph, HasComments, HasOwners, HasPublicIdUriAttributes,
        HasSiteRoutes, HasPublicIdAttribute;

    protected $table = 'articles';

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
        'meta',
        'published_at',
        'unpublished_at',
        'unpublish',
    ];

    protected $attributes = [
        //'cover_type' => 'image',
    ];

    protected $casts = [
        /*'seo'            => Seo::class,
        'assets'         => FrontendAssetsBundle::class,*/
        'meta'           => ArticleMetaObject::class,
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

    protected $with = ['page', 'site'];

    protected $hidden = ['account_id', 'site_id', 'user_id'];


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
        return Str::replace(['<', '>'], '', $this->seo->description ?? strip_tags($this->description));
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

    public function limitContent($limit = 300, $end = ''): string
    {

        return (new HtmlString(Str::limit(strip_tags($this->content, '<p><br><strong><em><del><b><i>'), $limit, $end)))->toHtml();

        //return Str::limit(strip_tags($this->content), $limit);
    }

    public function getBuildViewPath(): string
    {
        return $this->page->getBuildViewPath('article');
    }

    public function getBuildViewData(array $merge_data = []): array
    {
        $this->page->breadcrumb->items = $this->page->breadcrumb->items->merge(['' => $this->seoTitle()]);

        if (!empty($this->cover_url)) {
            $this->page->breadcrumb->background_url = $this->cover_url;
        }

        $viewData = [
            'article'       => $this,
            'frontendBuild' => $this->prepareFrontendBuild(),
            'comments'      => $this->comments, //todo: $this->comments()->paginate(5, ['*'], 'comments_page'),
            //'breadcrumbs'   => [$this->seoTitle()],
        ];

        return [...$this->page->getBuildViewData($viewData), ...$merge_data];
    }

    public function prepareFrontendBuild($buildMeta = false): FrontendBuildObject
    {
        $frontendBuild = $this->page->prepareFrontendBuild();

        //Gtag
        $frontendBuild->head->gtag_script = $this->getGTagScript();

        //Antes inicio da tag head
        //$frontendBuild->head->addBefore(Meta::toHtml());
        $frontendBuild->head->css .= $this->assets->css_bundle_html;

        //Fim d atag head
        $frontendBuild->head->addAfter($this->assets->js->head_html ?? '');
        $frontendBuild->head->addAfter($this->assets->head_script->html ?? '');

        //JSON-LD
        $frontendBuild->head->addAfter($this->ld_json_script);

        //Inicio do body
        $frontendBuild->body->id = "article-{$this->public_id}";
        $frontendBuild->body->class .= " article-{$this->public_id}";
        $frontendBuild->body->addBefore($this->assets->js->before_body_html ?? '');

        //Fim do body
        $frontendBuild->body->addAfter($this->assets->js->after_body_html ?? '');


        $frontendBuild->seo->fill([
                                      ...$this->seo->toArray(),
                                      'title'       => $this->getTitle(),
                                      'description' => $this->getDescription(),
                                      'keywords'    => $this->getKeywords(),
                                      'image_url'   => $this->seoImage(),
                                  ]);

        if ($buildMeta) {
            $frontendBuild->meta->reset();
            $frontendBuild->meta->registerSeoForArticle($this);
            //$frontendBuild->head->addBefore($frontendBuild->meta->toHtml());
            $frontendBuild->seo->html = $frontendBuild->meta->toHtml();
        }

        return $frontendBuild;
    }

    //endregion

    public function uploadPathTo(?string $path = null): string
    {
        $uploadPath = "articles/{$this->public_id}";

        return ($this->page ? $this->page->uploadPathTo($uploadPath) : $uploadPath) . ($path ? "/{$path}" : '');
    }

    //region ATTRIBUTES

    protected function buildedHtml(): Attribute
    {
        return Attribute::make(
            get: fn() => FrontendTwig::article($this),
        );
    }

    protected function getSeoAttribute(): Seo
    {
        return $this->meta->seo;
    }

    protected function setSeoAttribute($value): static
    {
        if (is_array($value)) {
            $this->meta->seo->fill($value);
        }
        else /*if(get_class($value) === Seo::class)*/ {
            $this->meta->seo = $value;
        }

        return $this;
    }

    protected function getAssetsAttribute(): FrontendAssetsBundle
    {
        return $this->meta->assets;
    }

    protected function setAssetsAttribute($value): static
    {
        $this->meta->assets->fill($value);

        return $this;
    }

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

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Str::of($value ?? Str::limit(strip_tags($this->content), 300))->replaceMatches('/\r\n+/', "\r\n"),
            set: static fn($value) => $value,
        );
    }

    protected function descriptionHtml(): Attribute
    {
        return Attribute::make(
            get: fn() => nl2br($this->description),
        );
    }

    protected function short_content(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->limitContent(),
        );
    }

    protected function hasCustomDescription(): Attribute
    {
        return Attribute::make(
            get: fn() => (bool)($this->attributes['description'] ?? false),
        );
    }

    protected function publishedAtLong(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->updated_at->translatedFormat(config('location.formats.datetime.full'))
        );
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value,
            set: static fn($value) => Str::slug(Str::lower($value)),
        );
    }

    protected ?self $nextArticleCache = null, $previousArticleCache = null;

    protected function nextArticle(): Attribute
    {
        if ($this->attributes['id'] && !$this->nextArticleCache) {
            $this->nextArticleCache = self::where('id', '>', $this->attributes['id'])->orderBy('id', 'asc')->withCount('comments')->first();
        }

        return Attribute::make(
            get: fn($value) => $this->nextArticleCache
        );

    }

    protected function previousArticle(): Attribute
    {
        if (!$this->previousArticleCache) {
            $this->previousArticleCache = ($this->id ? self::where('id', '<', $this->id) : self::query())->orderBy('id', 'desc')->withCount(['comments'])->first();
        }

        return Attribute::make(
            get: fn($value) => $this->previousArticleCache
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

        return $this->page->public_id_url ? "{$this->page->public_id_url}/article/{$this->public_id}" : '';
    }

    protected function getUrlAttribute(): string
    {
        $urlId = $this->slug ?? $this->public_id;

        return $this->page->urlTo("{$urlId}/");
        //return $this->page->url ? "{$this->page->url}/article/" . ($this->slug ?? $this->public_id) . '/' : '';
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
        });
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

    public function scopeWithBasic(Builder $query)
    {
        return $query->with(['categories', 'tags', 'comments']);
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

    /*public function cover()
    {
        return $this->hasOne(File::class, 'id', 'cover_id');
    }*/

    //endregion
}
