<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages;

use Adminx\Common\Enums\ContentEditorType;
use Adminx\Common\Exceptions\FrontendException;
use Adminx\Common\Facades\Frontend\FrontendHtml;
use Adminx\Common\Facades\Frontend\FrontendTwig;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Category;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListHtml;
use Adminx\Common\Models\Generics\Assets\GenericAssetElementCSS;
use Adminx\Common\Models\Generics\Assets\GenericAssetElementJS;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use Adminx\Common\Models\Interfaces\BuildableModel;
use Adminx\Common\Models\Interfaces\FrontendModel;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Interfaces\UploadModel;
use Adminx\Common\Models\Menus\MenuItem;
use Adminx\Common\Models\Objects\Frontend\Assets\FrontendAssetsBundle;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Objects\Seo\Seo;
use Adminx\Common\Models\Pages\Objects\PageBreadcrumb;
use Adminx\Common\Models\Pages\Objects\PageConfig;
use Adminx\Common\Models\Pages\Objects\PageContent;
use Adminx\Common\Models\Pages\Types\Abstract\AbstractPageType;
use Adminx\Common\Models\Pages\Types\Manager\Facade\PageTypeManager;
use Adminx\Common\Models\Scopes\WhereSiteScope;
use Adminx\Common\Models\Sites\SiteRoute;
use Adminx\Common\Models\Templates\Global\Abstract\AbstractTemplate;
use Adminx\Common\Models\Templates\Global\Manager\Facade\GlobalTemplateManager;
use Adminx\Common\Models\Traits\HasBreadcrumbs;
use Adminx\Common\Models\Traits\HasGenericConfig;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasPublicIdUriAttributes;
use Adminx\Common\Models\Traits\HasPublishTimestamps;
use Adminx\Common\Models\Traits\HasRelatedCache;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasSEO;
use Adminx\Common\Models\Traits\HasSiteRoutes;
use Adminx\Common\Models\Traits\HasSlugAttribute;
use Adminx\Common\Models\Traits\HasTemplates;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasVisitCounter;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasArticles;
use Adminx\Common\Models\Traits\Relations\HasFiles;
use Adminx\Common\Models\Traits\Relations\HasParent;
use Adminx\Common\Models\Traits\Relations\HasTagsMorph;
use Barryvdh\Debugbar\Facades\Debugbar;
use Butschster\Head\Contracts\MetaTags\RobotsTagsInterface;
use Butschster\Head\Contracts\MetaTags\SeoMetaTagsInterface;
use Butschster\Head\Facades\Meta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;

/**
 * @property Collection|CustomList[]|CustomListHtml[] $data_sources
 * @property AbstractPageType                         $type
 * @property AbstractTemplate                         $template_global
 * @property BreadcrumbConfig                         $breadcrumb_config
 */
class Page extends EloquentModelBase implements BuildableModel,
                                                FrontendModel,
                                                OwneredModel,
                                                PublicIdModel,
                                                RobotsTagsInterface,
                                                SeoMetaTagsInterface,
                                                UploadModel
{
    use BelongsToSite,
        BelongsToUser,
        HasArticles,
        HasBreadcrumbs,
        HasFiles,
        HasGenericConfig,
        HasOwners,
        HasParent,
        HasPublicIdAttribute,
        HasPublicIdUriAttributes,
        HasPublishTimestamps,
        HasRelatedCache,
        HasSelect2,
        HasSEO,
        HasSlugAttribute,
        HasTagsMorph,
        HasTemplates,
        HasUriAttributes,
        HasVisitCounter,
        HasSiteRoutes,
        SoftDeletes;

    protected $connection = 'mysql';

    protected $fillable = [
        'site_id',
        'user_id',
        'account_id',
        'parent_id',
        //'type_id',
        'type_name',
        'model_id',
        'template_name',
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
        //'elements',
    ];

    protected $casts = [
        'text'  => 'string',
        'title' => 'string',
        'slug'  => 'string',


        'content'    => PageContent::class,
        'assets_old' => 'object',
        'assets'     => FrontendAssetsBundle::class,


        'config'         => PageConfig::class,
        'seo'            => Seo::class,
        'css'            => GenericAssetElementCSS::class,
        'js'             => GenericAssetElementJS::class,
        'frontend_build' => FrontendBuildObject::class,
        //'elements' => PageElements::class,
        'html'           => 'string',
        'html_raw'       => 'string',


        'is_home'         => 'boolean',
        'show_breadcrumb' => 'boolean',
        'published_at'    => 'datetime:d/m/Y H:i:s',
        'unpublished_at'  => 'datetime:d/m/Y H:i:s',

        'breadcrumb' => PageBreadcrumb::class,
    ];

    protected $appends = [
        //'content',
        //'assets',
        //'html',
        'url',

    ];

    protected $attributes = [
        'is_home' => 0,
        //'content' => [],
        //'assets' => [],
    ];

    protected $hidden = ['account_id', 'site_id', 'user_id', 'parent_id'];

    //protected $with = ['site'];
    //protected ViewContract|null $viewCache = null;

    //region VALIDATIONS
    public static function createRules(?FormRequest $request = null): array
    {
        return [
            'type_name' => ['required'],
            //'model_id' => ['required'],
            'title'     => ['required'],
        ];
    }


    public static function createMessages(): array
    {
        return [
            'title.required'     => 'O título da página é obrigatório',
            'type_name.required' => 'Selecione o tipo da página',
            //'model_id.required' => 'Selecione o modelo da página',
        ];
    }
    //endregion

    //region HELPERS

    public function uploadPathTo(?string $path = null): string
    {
        $uploadPath = "pages/{$this->public_id}";

        return ($this->site ? $this->site->uploadPathTo($uploadPath) : $uploadPath) . ($path ? "/{$path}" : '');
    }

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

        /*$pageViewPathType = "common-frontend::pages.{$this->type->slug}" . ($append ? ".{$append}" : '');
        $pageViewPathModel = "common-frontend::pages.{$this->type->slug}.{$this->model->slug}" . ($append ? ".{$append}" : '');

        $pageViewFinalPath = 'common-frontend::pages.@default';


        if (View::exists($pageViewPathModel)) {
            $pageViewFinalPath = $pageViewPathModel;
        }
        else if (View::exists($pageViewPathType)) {
            $pageViewFinalPath = $pageViewPathType;
        }

        return $pageViewFinalPath;*/


        $pageDefaultView = 'common-frontend::pages.@default';

        $pageView = $pageDefaultView;

        if (!empty($this->template_name)) {
            $pageTemplateView = "pages-templates::{$this->template_name}." . ($append ?? 'index');
            if (View::exists($pageTemplateView)) {
                $pageView = $pageTemplateView;
            }
        }

        return $pageView;
    }

    public function getBuildViewData(array $merge_data = []): array
    {
        $errors = request()->session()->has('errors') ? request()->session()->get('errors') : new ViewErrorBag();
        //$errors = $errors->getBag('default');
        $viewData = [
            ...$this->site->getBuildViewData(),
            'page'       => $this,
            'recaptcha'  => '<div class="g-recaptcha mb-3" data-sitekey="' . $this->site->config->recaptcha_site_key . '"></div>',
            //Blade::render('<x-common::recaptcha :site="$page->site" no-ajax/>', ['page' => $this]),
            'errors'     => $errors,
            //'breadcrumb' => $this->show_breadcrumb ? $this->breadcrumb() : false,
        ];

        $breadcrumbAdd = collect();

        $requestData = request()->all() ?? [];

        if ($this->can_use_articles && !Route::current()->parameter('first_url')) {

            //Posts
            $articles = $this->articles()->published();

            //Categorias
            //dd(Route::current()->parameter('categorySlug'));
            $categorySlug = Route::current()->parameter('categorySlug') ?? $requestData['categorySlug'] ?? $merge_data['categorySlug'] ?? false;

            if ($categorySlug) {
                $category = $this->categories()->where('slug', $categorySlug)->orWhere('id', $categorySlug)->first();
                if ($category) {
                    $articles = $articles->whereHas('categories', function (Builder $query) use ($category) {
                        $query->where('id', $category->id);
                    });
                    $viewData['category'] = $category;

                    $breadcrumbAdd = $breadcrumbAdd->merge([$category->url => $category->title]);

                    //Meta::registerSeoMetaTagsForCategory($this, $category);
                }
                else {
                    throw new FrontendException('Categoria não encontrada');
                }
            }

            //Tags
            if ($requestData['tagSlug'] ?? false) {
                $tag = $this->tags()->whereSlug($requestData['tagSlug'])->first();
                $articles = $articles->whereHas('tags', function (Builder $query) use ($tag) {
                    $query->where('id', $tag->id);
                });
            }

            //Busca
            if ($requestData['q'] ?? false) {
                $articles = $articles->whereLike(['title', 'description', 'content', 'slug'], $requestData['q']);

                Meta::setTitle('Resultados da pesquisa: ' . $requestData['q']);
            }

            $viewData['articles'] = $articles->paginate(9);
            //Meta::setPaginationLinks($articles);
        }
        //todo: remove
        /*else if ($this->config->sources && $this->config->sources->count()) {

            $sourceData = [];

            foreach ($this->config->sources as $source) {
                $sourceData[$source->name] = $source->data;
            }

            $viewData['data'] = $sourceData;
        }*/

        $viewData['breadcrumb'] = $this->show_breadcrumb ? $this->breadcrumb($breadcrumbAdd->toArray()) : false;

        //dd($breadcrumbAdd->toArray());


        return [...$viewData, ...$merge_data];
    }

    public function prepareFrontendBuild($buildMeta = false): FrontendBuildObject
    {

        $frontendBuild = $this->site->frontendBuild();

        $frontendBuild->head->gtag_script = $this->getGTagScript();
        //$frontendBuild->head->addBefore($meta ? $meta->toHtml() : Meta::toHtml());
        $frontendBuild->head->css = $this->assets->css_bundle_html;
        $frontendBuild->head->addAfter($this->assets->js->head->html ?? '');
        $frontendBuild->head->addAfter($this->assets->head_script->html ?? '');

        $slug = $this->is_home ? 'home' : $this->slug;

        $frontendBuild->body->id = "page-{$slug}";
        $frontendBuild->body->class = "page-{$slug} page-{$this->public_id}";
        $frontendBuild->body->addBefore($this->assets->js->before_body->html ?? '');
        $frontendBuild->body->addAfter($this->assets->js->after_body->html ?? '');

        $frontendBuild->seo->fill([
                                      'title'         => "{{ page.getTitle() }}",
                                      'title_prefix'  => "{{ site.getTitle() }}",
                                      'description'   => $this->getDescription(),
                                      'keywords'      => $this->getKeywords(),
                                      'image_url'     => $this->seoImage(),
                                      'published_at'  => $this->published_at->toIso8601String(),
                                      'updated_at'    => $this->updated_at->toIso8601String(),
                                      'canonical_uri' => $this->uri,
                                      'html'          => '',
                                  ]);

        /* if($buildMeta){
             $frontendBuild->meta->reset();
             $frontendBuild->meta->registerSeoForPage($this);

             //$frontendBuild->head->addBefore($frontendBuild->meta->toHtml());
             $frontendBuild->seo->html = $frontendBuild->meta->toHtml();
         }*/

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

    protected function getShowBreadcrumbAttribute(): bool
    {
        return $this->is_home ? false : ($this->breadcrumb_config->enable ?? false);
    }

    protected function getBreadcrumbConfigAttribute()
    {
        return $this->config->breadcrumb ?? $this->site->theme->config->breadcrumb ?? new BreadcrumbConfig();
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn() => PageTypeManager::getType($this->type_name)
        );
    }

    protected function templateGlobal(): Attribute
    {
        return Attribute::make(
            get: fn() => GlobalTemplateManager::getTemplate($this->template_name)
        );
    }

    protected function editorType(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->config->editor_type ?? auth()->user()->config->editor_type ?? ContentEditorType::from('tinymce')
        );
    }

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

    protected function usingArticles(): Attribute
    {
        return Attribute::make(
            get: fn() => ((bool)$this->config?->isUsingModule('articles')) || ((bool)$this->type?->isUsingModule('articles')));
    }

    protected function canUseArticles(): Attribute
    {
        return Attribute::make(
            get: fn() => ((bool)$this->config?->canUseModule('articles')) || ((bool)$this->type?->canUseModule('articles')));
    }

    protected function usingList(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->can_use_list && $this->articles()->count()); //todo
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
            get: fn() => "<h4>" . ($this->title ?? 'Página sem Título') . "</h4><div class=\"d-flex align-items-center\"><small class=\"ms-0 me-2 w-100\">{$this->type?->title}</small><small class=\"ms-auto me-0\">{$this->created_at?->shortRelativeToNowDiffForHumans()}</small></div>",
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

    protected function builtHtml(): Attribute
    {
        $page = $this;

        return Attribute::make(
            get: static fn() => FrontendTwig::page($page),
        );
    }

    protected function dataSources(): Attribute
    {

        $sources = collect();

        //CustomLists
        /*if ($this->custom_lists->count()) {
            $sources = $this->custom_lists->keyBy(fn($customList, $key) => Str::camel($customList->slug))
                                          ->map(fn($customList) => $customList->mountModel());
        }*/

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
        return $this->content->html ?? '';
    }

    protected function getUrlAttribute()
    {

        if ($this->is_home) {
            return '/';
        }


        $urlId = $this->slug ?? $this->public_id;

        return "/{$urlId}/";
    }

    protected function getCoverUrlAttribute()
    {
        if (!empty($this->breadcrumb_config->background_url ?? null)) {
            return $this->breadcrumb_config->background_url;
        }

        if (!empty($this->seo->image_url ?? null)) {
            return $this->seo->image_url;
        }

        return null;
    }
    //endregion

    //region SETS
    protected function setHtmlAttribute($value): void
    {
        $this->content->html = $value;
    }

    /*protected function setInternalHtmlAttribute($value): void
    {
        $this->content->internal->html = $value;
    }*/

    //endregion

    //endregion

    //region SCOPES

    public function scopeWhereUrl(Builder $query, ?string $url = null): Builder
    {

        return $query->when(empty($url), static function (Builder $q) {
            $q->emptySlug()->orWhere('is_home', true);
        })->when($url, static function (Builder $q) use ($url) {
            $q->where('slug', $url);
            $q->orWhere([
                            'public_id' => $url,
                            'id'        => $url,
                        ]);
        });
    }

    public function scopeHomePage(Builder $query): Builder
    {
        return $query->isHome()->orWhere(function (Builder $q) {
            $q->emptySlug();
        });
    }

    public function scopeIsHome(Builder $query, $is_home = true): Builder
    {
        return $query->where('is_home', $is_home);
    }

    public function scopeEmptySlug(Builder $query): Builder
    {
        return $query->whereNull('slug')->orWhere('slug', '');
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
    public function page_template()
    {
        return $this->modelTemplate();
    }

    public function custom_lists()
    {
        return $this->morphToMany(CustomList::class, 'model', 'page_internals');
    }

    //region Articles
    public function articles()
    {
        return $this->hasMany(Article::class, 'page_id', 'id')->ordered();
    }

    public function last_articles()
    {
        return $this->articles()->published();
    }
    //endregion

    /**
     * Rotas vinculadas á página ou a seus objetos
     */
    public function related_routes(): HasMany
    {
        return $this->hasMany(SiteRoute::class);
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

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Páginas Internas
     */
    public function page_internals(): HasMany
    {
        return $this->hasMany(PageInternal::class);
    }

    //endregion
}
