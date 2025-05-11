<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages;

use Adminx\Common\Enums\ContentEditorType;
use Adminx\Common\Enums\Pages\PageType;
use Adminx\Common\Exceptions\FrontendException;
use Adminx\Common\Facades\Frontend\FrontendTwig;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\CustomLists\CustomList;
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
use Adminx\Common\Models\Scopes\WhereSiteScope;
use Adminx\Common\Models\Sites\SiteRoute;
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
use Adminx\Common\Models\Traits\Relations\Categorizable;
use Adminx\Common\Models\Traits\Relations\HasArticles;
use Adminx\Common\Models\Traits\Relations\HasParent;
use Adminx\Common\Models\Traits\Relations\HasTagsMorph;
use Barryvdh\Debugbar\Facades\Debugbar;
use Butschster\Head\Contracts\MetaTags\RobotsTagsInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;

/**
 * @var BreadcrumbConfig $breadcrumb_config
 * @property ?CustomList $pageable
 */
class Page extends EloquentModelBase implements BuildableModel,
                                                FrontendModel,
                                                OwneredModel,
                                                PublicIdModel,
                                                RobotsTagsInterface,
    /*SeoMetaTagsInterface,*/
                                                UploadModel
{
    use BelongsToSite,
        BelongsToUser,
        Categorizable,
        HasArticles,
        HasBreadcrumbs,
        //HasFiles,
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

        'type',
        'model_id',
        'template_name',
        'title',
        'slug',

        'content',
        'assets',

        'pageable_type',
        'pageable_id',


        'html',
        'css',
        'js',


        'is_home',
        'config',
        'seo',
        'published_at',
        'unpublished_at',
    ];

    protected $casts = [
        'text'  => 'string',
        'title' => 'string',
        'slug'  => 'string',


        'content'     => 'string',
        'content_old' => PageContent::class,
        'assets'      => FrontendAssetsBundle::class,


        'type'           => PageType::class,
        'config'         => PageConfig::class,
        'seo'            => Seo::class,
        'css'            => GenericAssetElementCSS::class,
        'js'             => GenericAssetElementJS::class,
        'frontend_build' => FrontendBuildObject::class,
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
        //'url',

    ];

    //protected $guarded = ['url', 'uri'];

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
            'type'  => ['required'],
            //'model_id' => ['required'],
            'title' => ['required'],
        ];
    }


    public static function createMessages(): array
    {
        return [
            'title.required' => 'O título da página é obrigatório',
            'type.required'  => 'Selecione o tipo da página',
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
            'page'   => $this,
            //Blade::render('<x-common::recaptcha :site="$page->site" no-ajax/>', ['page' => $this]),
            'errors' => $errors,
            //'breadcrumb' => $this->show_breadcrumb ? $this->breadcrumb() : false,
        ];

        $breadcrumbAdd = collect();

        $requestData = request()->all() ?? [];

        //region Prepare categories
        $categorySlug = Route::current()->parameter('categorySlug') ?? $requestData['categorySlug'] ?? $merge_data['categorySlug'] ?? $requestData['category'] ?? $requestData['categories'] ?? $merge_data['categorySlug'] ?? false;

        $categories = !is_array($categorySlug) ? str($categorySlug)->explode(',') : collect($categorySlug);

        $categories = $categories->filter()->values();

        if ($categories->count()) {

            if ($this->has_pageable) {
                $categoryQuery = $this->pageable->categories()->whereUrlIn($categorySlug);

            }
            else {
                $categoryQuery = $this->categories()->whereUrlIn($categorySlug);
            }

            if ($categories->count() === 1) {
                if ($category = $categoryQuery->first()) {
                    $viewData['category'] = $category;
                    $viewData['categories'] = collect($category);
                    $breadcrumbAdd = $breadcrumbAdd->merge(['' => $category->title]);
                }
                else if (Route::current()->parameter('categorySlug')) {
                    throw new FrontendException('Categoria não encontrada');
                }


            }
            else {
                $viewData['categories'] = $categoryQuery->get();
                $viewData['category'] = $categoryQuery->first();
            }
        }

        //endregion

        ////region Busca e paginação
        $requestSearch = $requestData['q'] ?? $requestData['search'] ?? null;
        $viewData['search'] = $hasSearch = !blank($requestSearch);
        if ($hasSearch) {
            $viewData['searchTerm'] = $requestSearch;
        }

        $paginationPage = $requestData['page'] ?? 1;
        $paginationPerPage = $requestData['per_page'] ?? $requestData['perPage'] ?? null;
        //endregion


        if ($this->articles()->published()->count()) {

            $paginationPerPage = $paginationPerPage ?? $this->config->items_per_page ?? 9; //Definir default na pagina

            //Posts
            $articles = $this->articles()->published();

            if ($categories->count()) {

                $articles = $articles->hasAnyCategory($categories->toArray());

            }

            //Tags
            if ($requestData['tagSlug'] ?? false) {
                $tag = $this->tags()->whereSlug($requestData['tagSlug'])->first();
                $articles = $articles->whereHas('tags', function (Builder $query) use ($tag) {
                    $query->where('id', $tag->id);
                });
            }


            if ($hasSearch) {
                $articles = $articles->whereLike(['title', 'description', 'content', 'slug'], $requestSearch);


                $breadcrumbAdd->put($this->uri . '?' . http_build_query([
                                                                            'q' => $requestSearch,
                                                                        ]), 'Buscando por: "' . $requestSearch . '"');
            }

            $viewData['articles'] = $articles->paginate(perPage: $paginationPerPage, page: $paginationPage);
            //Meta::setPaginationLinks($articles);
        }
        else {
            $viewData['articles'] = [];
        }

        if ($this->pageable_id && $this->pageable_type && $this->pageable?->exists()) {

            $paginationPerPage = $viewData['items_page'] = $paginationPerPage ?? $this->config->items_per_page ?? 0;

            $viewData['items_per_page'] = $paginationPerPage;

            $viewData['list'] = $this->pageable;

            $listItemsQuery = $this->pageable->items();


            if ($categories->count()) {
                $listItemsQuery = $listItemsQuery->hasAnyCategory($categories->toArray());
                $viewData['items_categories'] = $viewData['categories'] ?? (!blank($viewData['category']) ? collect([$viewData['category']]) : null);
                $viewData['items_category'] = $viewData['category'] ?? null;
                $viewData['category_slugs'] = $categories->values() ?? null;
            }

            if ($hasSearch) {
                $listItemsQuery = $listItemsQuery->search(searchTerm: $requestSearch, searchInCategories: true);
            }

            $viewData['list_items'] = $listItems = $paginationPerPage ? $listItemsQuery->paginate(perPage: $paginationPerPage, page: $paginationPage) : $listItemsQuery->get();


            $viewData['items_page_count'] = $listItems->count();
            $viewData['items_last_page'] = $paginationPerPage ? $listItems->lastPage() : 1;
            $viewData['items_total_count'] = $paginationPerPage ? $listItems->total() : $listItems->count();
        }

        $viewData['breadcrumb'] = $this->show_breadcrumb ? $this->breadcrumb($breadcrumbAdd->toArray()) : false;

        return [...$viewData, ...$merge_data];
    }

    public function prepareFrontendBuild($buildMeta = false): FrontendBuildObject
    {


        $frontendBuild = $this->site->frontendBuild();

        $frontendBuild->head->gtag_script = $this->getGTagHeadScript();
        $frontendBuild->body->gtag_script = $this->getGTagBodyScript();

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
                                      'title'         => "{{ page.seoTitle() }}",
                                      'title_prefix'  => "{{ site.seoTitle() }}",
                                      'description'   => $this->seoDescription(),
                                      'keywords'      => $this->seoKeywords(),
                                      'image_url'     => $this->seoImage($this->site?->seoImage()),
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

    public function builtHtml()
    {
        return FrontendTwig::page($this);
    }

    public function slugOrPublicId()
    {
        return !blank($this->slug) ? $this->slug : $this->public_id;
    }
    //endregion

    //region ATTRIBUTES

    protected function hasPageable(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->pageable_id && $this->pageable_type && $this->pageable?->exists(),
        );
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $value,
            set: fn($value, array $attributes) => match (true) {
                $this->is_home => null,
                !blank($value) => str($value)->slug()->toString(),
                default => str($attributes['title'] ?? '')->slug()->toString(),
            }
        );
    }

    protected function getShowBreadcrumbAttribute(): bool
    {
        return $this->is_home ? false : ($this->breadcrumb_config->enable ?? false);
    }

    protected function getBreadcrumbConfigAttribute()
    {
        return $this->config->breadcrumb ?? $this->site->theme->config->breadcrumb ?? new BreadcrumbConfig();
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

    protected function usingArticles(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->type?->useArticles() || $this->articles()->count()
        );
    }

    //region Modules
    /*protected function usingAdvancedHtml(): Attribute
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
            get: fn() => ($this->config?->isUsingModule('articles') ?? false) || ($this->type?->isUsingModule('articles') ?? false) || $this->articles()->count()
        );
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
    }*/

    //endregion

    protected function text(): Attribute
    {
        $pageTitle = $this->label;
        $textHtml = "<h4 class='mb-1 d-flex gap-2'><span class='flex-grow-1'>{$pageTitle}</span><small class='badge badge-light-dark'>{$this->type?->title()}</small></h4>";

        $textHtml .= "<small class='text-muted fw-bold w-100 mb-1'>{$this->uri}</small><div class=\"w-100 d-flex align-items-center gap-2\">";

        if ($this->parent_id) {
            $this->load('parent');
            $textHtml .= "<span><small>Sub-Página de:</small> <b class=\"badge badge-light-info\">{$this->parent->title}</b></span>";
        }

        if ($this->pageable?->exists()) {
            $this->load('parent');
            $textHtml .= "<span><small>Fonte de Dados:</small> <b class=\"badge badge-light-info\">{$this->pageable->title}</b></span>";
        }

        $textHtml .= "<small class='fst-italic ms-auto me-0 text-end'>Criada em {$this->created_at?->format('d/m/Y H:i:s')}<br>Atualizada pela última vez em {$this->updated_at?->format('d/m/Y H:i:s')}</small>";


        $textHtml .= "</div>";

        return Attribute::make(
            get: fn() => $textHtml,
        );
    }

    protected function label(): Attribute
    {

        $label = $this->title;

        if ($this->pageable?->exists()) {
            $label = blank($label) ? "Página Dinâmica Sem Título" : $label;
            $label .= " ({$this->pageable->title})";
        }

        return Attribute::make(
            get: fn() => $label ?? 'Página Sem Título',
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


    //region GETS

    protected function getHtmlAttribute()
    {
        return $this->content ?? '';
    }

    protected function getUriAttribute()
    {
        return $this->generateUri();


    }

    protected function getUrlAttribute(): string
    {
        return $this->generateUrl();

    }

    public function generateUri()
    {
        if (!$this->site) {

        }

        if (blank($this->parent_id) && $this->is_home) {

            return $this->site->uri;

        }


        if ($this->parent_id && $this->parent->exists()) {

            $urlId = str($this->parent->slug)->finish('/')->append($this->slug);

        }
        else {
            $urlId = str($this->slug);
        }

        return $this->site->uriTo($urlId->toString());


    }

    public function generateUrl(): string
    {

        if (blank($this->parent_id) && $this->is_home) {

            return '/';

        }

        if ($this->parent_id && $this->parent->exists()) {

            $urlId = str($this->parent->slug)->finish('/')->append($this->slug);

        }
        else {
            $urlId = str($this->slug);
        }

        return $urlId->start('/')->finish('/')->toString();


    }

    protected function getCoverUrlAttribute()
    {
        if (!blank($this->breadcrumb_config->background_url ?? null)) {
            return $this->breadcrumb_config->background_url;
        }

        if (!blank($this->seo->image_url ?? null)) {
            return $this->seo->image_url;
        }

        return null;
    }
    //endregion

    //region SETS
    protected function setHtmlAttribute($value): void
    {
        $this->content = $value;
    }



    //endregion

    //endregion

    //region SCOPES
    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id')->without('parent');
    }

    public function scopeWithRelations(Builder $query): Builder
    {
        return $query->with(['site', 'parent', 'pageable']);
    }

    public function scopeWhereUrl(Builder $query, ?string $url = null): Builder
    {

        return $query->when(empty($url), static function (Builder $q) {
            $q->emptySlug()->orWhere('is_home', true);
        })->when($url, static function (Builder $q) use ($url) {
            return $q->where('slug', $url)
                     ->orWhere('public_id', $url)
                     ->orWhere('id', $url);
        });
    }

    public function scopeWhereUrlIn(Builder $query, string|array $urls): Builder
    {

        $urls = is_array($urls) ? $urls : [$urls];

        return $query->where(function (Builder $q) use ($urls) {
            return $q->whereIn('slug', $urls)
                     ->orWhereIn('public_id', $urls)
                     ->orWhereIn('id', $urls);
        });
    }

    public function scopeHomePage(Builder $query): Builder
    {
        return $query->where(fn(Builder $q) => $q->parents()->isHome())->orWhere(fn(Builder $q) => $q->parents()->emptySlug());
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
        return $this->morphToMany(CustomList::class, 'pageable');
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


    /*public function categories()
    {
        return $this->hasMany(Category::class);
    }*/

    public function pageable(): MorphTo
    {
        return $this->morphTo();
    }

    //endregion
}
