<?php

namespace Adminx\Common\Models\Pages;

use Adminx\Common\Facades\Frontend\FrontendHtml;
use Adminx\Common\Libs\FrontendEngine\AdvancedHtmlEngine;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListHtml;
use Adminx\Common\Models\Generics\Assets\GenericAssetElementCSS;
use Adminx\Common\Models\Generics\Assets\GenericAssetElementJS;
use Adminx\Common\Models\Generics\Configs\PageConfig;
use Adminx\Common\Models\Generics\Elements\PageElements;
use Adminx\Common\Models\Generics\Seo\Seo;
use Adminx\Common\Models\Interfaces\BuildableModel;
use Adminx\Common\Models\Interfaces\HtmlModel;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\MenuItem;
use Adminx\Common\Models\Objects\Frontend\Assets\FrontendAssetsBundle;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Pages\Objects\PageContent;
use Adminx\Common\Models\Post;
use Adminx\Common\Models\Scopes\WhereSiteScope;
use Adminx\Common\Models\Traits\HasAdvancedHtml;
use Adminx\Common\Models\Traits\HasGenericConfig;
use Adminx\Common\Models\Traits\HasHtmlBuilds;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasPublicIdUriAttributes;
use Adminx\Common\Models\Traits\HasRelatedCache;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasSEO;
use Adminx\Common\Models\Traits\HasSlugAttribute;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasVisitCounter;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasCategoriesMorph;
use Adminx\Common\Models\Traits\Relations\HasFiles;
use Adminx\Common\Models\Traits\Relations\HasParent;
use Adminx\Common\Models\Traits\Relations\HasPosts;
use Adminx\Common\Models\Traits\Relations\HasTagsMorph;
use Barryvdh\Debugbar\Facades\Debugbar;
use Butschster\Head\Contracts\MetaTags\RobotsTagsInterface;
use Butschster\Head\Contracts\MetaTags\SeoMetaTagsInterface;
use Butschster\Head\Facades\Meta;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;

/**
 * @property Collection|CustomList[]|CustomListHtml[] $data_sources
 */
class Page extends EloquentModelBase implements PublicIdModel, OwneredModel, HtmlModel, BuildableModel, SeoMetaTagsInterface, RobotsTagsInterface
{
    use HasUriAttributes, HasSelect2, SoftDeletes, HasSlugAttribute, HasSEO, HasFiles, HasCategoriesMorph, HasTagsMorph, HasPosts, BelongsToSite, BelongsToUser, HasPublicIdAttribute, HasPublicIdUriAttributes, HasParent, HasOwners, HasGenericConfig, HasVisitCounter, HasAdvancedHtml, HasRelatedCache, HasHtmlBuilds;

    protected $connection = 'mysql';

    protected $fillable = [
        'site_id',
        'user_id',
        'account_id',
        'parent_id',
        'type_id',
        'model_id',
        //'form_id',
        'title',
        'slug',

        'content',
        'assets',


        'html',
        'html_raw',
        'internal_html',
        'css',
        'js',


        'is_home',
        'config',
        'seo',
        'published_at',
        'unpublished_at',
        'elements',
    ];

    protected $casts = [
        'text'  => 'string',
        'title' => 'string',
        'slug'  => 'string',


        'content' => PageContent::class,
        'assets_old'  => 'object',
        'assets'  => FrontendAssetsBundle::class,


        'config'   => PageConfig::class,
        'seo'      => Seo::class,
        'css'      => GenericAssetElementCSS::class,
        'js'       => GenericAssetElementJS::class,
        'elements' => PageElements::class,
        'html'     => 'string',
        'html_raw' => 'string',


        'is_home'        => 'boolean',
        'published_at'   => 'datetime:d/m/Y H:i:s',
        'unpublished_at' => 'datetime:d/m/Y H:i:s',
    ];

    protected $appends = [
        'content',
        'assets',
        //'html',
        //'html_raw',
        //'internal_html',
        'url',

    ];

    protected $attributes = [
        'is_home' => 0,
        //'content' => [],
        //'assets' => [],
    ];

    public $buildSchema = [
        'posts' => [
            'categories',
            'user',
        ],
        'categories',
        'tags',
    ];


    protected ViewContract|null $viewCache = null;

    //region VALIDATIONS
    public static function createRules(FormRequest $request = null): array
    {
        return [
            'type_id'  => ['required'],
            'model_id' => ['required'],
            'title'    => ['required'],
        ];
    }


    public static function createMessages(): array
    {
        return [
            'title.required'    => 'O título da página é obrigatório',
            'type_id.required'  => 'Selecione o tipo da página',
            'model_id.required' => 'Selecione o modelo da página',
        ];
    }
    //endregion

    //region HELPERS

    public function buildedInternalHtml($dataItem): string
    {
        if (!$this->id || !$this->site) {
            return '';
        }

        return FrontendHtml::html($this->content->internal->html, [
            ...$this->getBuildViewData(),
            'currentItem' => $dataItem,
        ]);

    }

    public function internalUrl($dataItem, $prefix = null): string
    {
        return "{$this->url}/" . ($prefix ? "{$prefix}/" : '') . ($dataItem->slug ?? $dataItem->public_id);
    }

    public function internalUri($dataItem): string
    {
        return "{$this->uri}/" . ($dataItem->slug ?? $dataItem->public_id);
    }

    public function getBuildViewPath($append = null): string
    {
        $pageViewPathType = "adminx-frontend::pages.{$this->type->slug}" . ($append ? ".{$append}" : '');
        $pageViewPathModel = "adminx-frontend::pages.{$this->type->slug}.{$this->model->slug}" . ($append ? ".{$append}" : '');

        $pageViewFinalPath = 'adminx-frontend::pages.@default';


        if (View::exists($pageViewPathModel)) {
            $pageViewFinalPath = $pageViewPathModel;
        }
        else if (View::exists($pageViewPathType)) {
            $pageViewFinalPath = $pageViewPathType;
        }

        return $pageViewFinalPath;
    }

    public function getBuildViewData(array $merge_data = []): array
    {
        $viewData = [
            ...$this->site->getBuildViewData(),
            'page'           => $this,
            'showBreadcrumb' => !$this->is_home && ($this->config->breadcrumb ? $this->config->breadcrumb->enable : $this->site->theme->config->breadcrumb->enable),
        ];

        $requestData = request()->all() ?? [];

        if ($this->using_posts) {

            //Posts
            $posts = $this->posts()->published();

            //Categorias
            if ($requestData['categorySlug'] ?? false) {
                $category = $this->categories()->where('slug', $requestData['categorySlug'])->first();
                if ($category) {
                    $posts = $posts->whereHas('categories', function (Builder $query) use ($category) {
                        $query->where('id', $category->id);
                    });
                    Meta::registerSeoMetaTagsForCategory($this, $category);
                }
            }

            //Tags
            if ($requestData['tagSlug'] ?? false) {
                $tag = $this->tags()->whereSlug($requestData['tagSlug'])->first();
                $posts = $posts->whereHas('tags', function (Builder $query) use ($tag) {
                    $query->where('id', $tag->id);
                });
            }

            //Busca
            if ($requestData['q'] ?? false) {
                $posts = $posts->whereLike(['title', 'description', 'content', 'slug'], $requestData['q']);

                Meta::setTitle('Resultados da pesquisa: ' . $requestData['q']);
            }


            $posts = $posts->paginate(9);

            $viewData['posts'] = $posts;
            //Meta::setPaginationLinks($posts);
        }
        else if ($this->config->sources && $this->config->sources->count()) {

            $sourceData = [];

            foreach ($this->config->sources as $source) {
                $sourceData[$source->name] = $source->data;
                /*if ($this->config->isUsingModule('internal_pages')) {
                    foreach ($sourceData[$source->name]->items as $dataItem) {
                        $dataItem->internal_url = $this->internalUrl($dataItem, $source->internal_url);
                    }
                }*/
            }

            $viewData['data'] = $sourceData;
        }


        return [...$viewData, ...$merge_data];
    }

    public function frontendBuild(\Butschster\Head\MetaTags\Meta|null $meta = null): FrontendBuildObject
    {


        $frontendBuild = $this->site->frontendBuild();

        $frontendBuild->head->gtag_script = $this->getGTagScript();
        $frontendBuild->head->addBefore($meta ? $meta->toHtml() : Meta::toHtml());
        $frontendBuild->head->css = $this->assets->css_bundle_html;
        $frontendBuild->head->addAfter($this->assets->js->head->html ?? '');
        $frontendBuild->head->addAfter($this->assets->head_script->html ?? '');

        $slug = $this->is_home ? 'home' : $this->slug;

        $frontendBuild->body->id = "page-{$slug}";
        $frontendBuild->body->class = "page-{$slug} page-{$this->public_id}";
        $frontendBuild->body->addBefore($this->assets->js->before_body->html ?? '');
        $frontendBuild->body->addAfter($this->assets->js->after_body->html ?? '');

        return $frontendBuild;
    }

    public function loadSEO()
    {

        if (!($this->site->config->debug ?? false)) {
            Debugbar::disable();
        }
    }
    //endregion

    //region ATTRIBUTES

    //region Modules
    protected function usingAdvancedHtml(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->config->isUsingModule('advanced_html')
        );
    }

    protected function usingForms(): Attribute
    {
        return Attribute::make(
            get: fn() => ($this->can_use_forms && $this->widgets()->wherePivot('source_type', 'form')->count()) ?: @$this->model->using_forms ?: false
        );
    }

    protected function canUseForms(): Attribute
    {
        return Attribute::make(
            get: fn() => @$this->config->canUseModule('forms') ?: @$this->model->can_use_forms ?: false);
    }

    protected function usingPosts(): Attribute
    {
        return Attribute::make(
            get: fn() => @$this->config->isUsingModule('posts') ?: @$this->model->using_posts ?: false);
    }

    protected function canUsePosts(): Attribute
    {
        return Attribute::make(
            get: fn() => @$this->config->canUseModule('posts') ?: @$this->model->can_use_posts ?: false);
    }

    protected function usingList(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->can_use_list && $this->posts()->count()); //todo
    }

    protected function canUseList(): Attribute
    {
        return Attribute::make(
            get: fn() => @$this->config->canUseModule('list') ?: @$this->model->can_use_list ?: false);
    }

    //endregion

    protected function text(): Attribute
    {
        return Attribute::make(
            get: fn() => "<h2>{$this->title}</h2>{$this->model->title}",
        );
    }

    protected function jsHtml(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->assets->js->html,
        );
    }

    protected function cssHtml(): Attribute
    {

        return Attribute::make(
            get: fn() => $this->assets->css->html,
        );
    }

    protected function buildedHtml(): Attribute
    {
        $page = $this;

        return Attribute::make(
            get: fn() => FrontendHtml::page($page),
        );
    }

    protected function dataSources(): Attribute
    {

        $sources = collect();

        //CustomLists
        if ($this->custom_lists->count()) {
            $sources = $this->custom_lists->keyBy(fn($customList, $key) => Str::camel($customList->slug))
                                          ->map(fn($customList) => $customList->mountModel());
        }

        return Attribute::make(
            get: fn() => $sources,
        );
    }


    //region GETS

    public function getInternalHtmlAttribute()
    {
        return $this->content->internal->html ?? '';
    }

    protected function getHtmlAttribute()
    {
        return $this->content->main->html ?? '';
    }

    protected function getUrlAttribute()
    {

        if ($this->is_home) {
            return '/';
        }

        return "/{$this->slug}";
    }
    //endregion

    //region SETS
    protected function setHtmlAttribute($value): void
    {
        $this->content->main->html = $value;
    }

    protected function setInternalHtmlAttribute($value): void
    {
        $this->content->internal->html = $value;
    }

    //endregion

    //endregion

    //region SCOPES

    public function scopeIsHome(Builder $query, $is_home = true): Builder
    {
        return $query->where('is_home', $is_home);
    }

    public function scopeBuild(Builder $query)
    {
        return $query->with($this->buildSchema);
    }

    //endregion

    //region OVERRIDES
    protected static function booted()
    {
        static::addGlobalScope(new WhereSiteScope);
    }

    public function save(array $options = [])
    {
        return parent::save($options);
    }
    //endregion

    //region RELATIONS
    public function posts()
    {
        return $this->hasMany(Post::class, 'page_id', 'id')->ordered();
    }

    public function custom_lists()
    {
        return $this->hasMany(CustomList::class, 'page_id', 'id');
    }

    public function menu_items()
    {
        return $this->morphMany(MenuItem::class, 'menuable');
    }

    /*public function form()
    {
        return $this->hasOneThrough(Form::class,
                                    Formulable::class,
                                    'formulable_id',
                                    'id',
                                    'id',
                                    'form_id',
        )->where('formulable_type', MorphHelper::getMorphTypeTo(self::class));
    }

    public function formulable()
    {
        return $this->hasOne(Formulable::class, 'formulable_id', 'id')->where('formulable_type', MorphHelper::getMorphTypeTo(self::class));
    }

    public function form_answers()
    {
        return $this->morphMany(FormAnswer::class, 'formulable');
    }*/

    public function type()
    {
        return $this->belongsTo(PageType::class);
    }

    public function model()
    {
        return $this->belongsTo(PageModel::class);
    }

    //endregion
}
