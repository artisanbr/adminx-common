<?php

namespace ArtisanBR\Adminx\Common\App\Models;

use ArtisanBR\Adminx\Common\App\Libs\FrontendEngine\AdvancedHtmlEngine;
use ArtisanBR\Adminx\Common\App\Models\Bases\EloquentModelBase;
use ArtisanBR\Adminx\Common\App\Models\Casts\AsCollectionOf;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomList;
use ArtisanBR\Adminx\Common\App\Models\Generics\Assets\GenericAssetElementCSS;
use ArtisanBR\Adminx\Common\App\Models\Generics\Assets\GenericAssetElementJS;
use ArtisanBR\Adminx\Common\App\Models\Generics\Configs\PageConfig;
use ArtisanBR\Adminx\Common\App\Models\Generics\Elements\HtmlElement;
use ArtisanBR\Adminx\Common\App\Models\Generics\Elements\PageElements;
use ArtisanBR\Adminx\Common\App\Models\Generics\Seo;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\BuildableModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\HtmlModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\OwneredModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\PublicIdModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\WidgeteableModel;
use ArtisanBR\Adminx\Common\App\Models\Scopes\WhereSiteScope;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasAdvancedHtml;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasGenericConfig;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasHtmlBuilds;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasOwners;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasPublicIdAttribute;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasPublicIdUriAttributes;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasRelatedCache;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasSelect2;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasSEO;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasSlugAttribute;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasUriAttributes;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasVisitCounter;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToSite;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToUser;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasCategoriesMorph;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasFiles;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasParent;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasPosts;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasTagsMorph;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasWidgets;
use ArtisanLabs\LaravelVisitTracker\Traits\Visitable;
use Barryvdh\Debugbar\Facades\Debugbar;
use Butschster\Head\Contracts\MetaTags\RobotsTagsInterface;
use Butschster\Head\Contracts\MetaTags\SeoMetaTagsInterface;
use Butschster\Head\Facades\Meta;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\View;

class Page extends EloquentModelBase implements WidgeteableModel, PublicIdModel, OwneredModel, HtmlModel, BuildableModel, SeoMetaTagsInterface, RobotsTagsInterface
{
    use HasUriAttributes, HasSelect2, SoftDeletes, HasSlugAttribute, HasSEO, HasFiles, HasCategoriesMorph, HasTagsMorph, HasPosts, BelongsToSite, BelongsToUser, HasPublicIdAttribute, HasPublicIdUriAttributes, HasWidgets, HasParent, HasOwners, HasGenericConfig, HasVisitCounter, Visitable, HasAdvancedHtml, HasRelatedCache, HasHtmlBuilds;

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
        'html',
        'html_raw',
        'internal_html',
        'internal_html_raw',
        'css',
        'js',
        'is_home',
        'config',
        'seo',
        'published_at',
        'unpublished_at',
    ];

    protected $casts = [
        'text'        => 'string',
        'title'        => 'string',
        'slug'        => 'string',
        'is_home'        => 'boolean',
        'config'         => PageConfig::class,
        'seo'            => Seo::class,
        'css'            => GenericAssetElementCSS::class,
        'js'             => GenericAssetElementJS::class,
        'elements'       => PageElements::class,
        'elements_old'   => AsCollectionOf::class . ':' . HtmlElement::class,
        'html'           => 'string',
        'html_raw'       => 'string',
        'internal_html_raw'       => 'string',
        'published_at'   => 'datetime:d/m/Y H:i:s',
        'unpublished_at' => 'datetime:d/m/Y H:i:s',
    ];

    protected $appends = [
        //'html',
        //'html_raw',
        'internal_html_raw',
        'url',

    ];

    protected $attributes = [
        'is_home' => 0,
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
        return $this->id && $this->site ? AdvancedHtmlEngine::start($this->site, $this, 'internal-html')->buildHtml([
            'currentItem' => $dataItem,
        ], $this->internal_html_raw) : '';
    }

    public function internalUrl($dataItem, $prefix = null): string
    {
        return "{$this->url}/i/". ($prefix ? "{$prefix}/" : '') . ($dataItem->slug ?? $dataItem->public_id);
    }

    public function internalUri($dataItem): string
    {
        return "{$this->uri}/i/" . ($dataItem->slug ?? $dataItem->public_id);
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

    public function getBuildViewData(array $requestData = [], array $merge_data = []): array
    {
        //$request = request();
        $viewData = [
            'site'        => $this->site,
            'page'        => $this,
            'searchTerm'  => $requestData['q'] ?? null,
            'breadcrumbs' => $requestData['q'] ?? null,
        ];

        if ($requestData['q'] ?? false) {
            $viewData['searchTerm'] = $requestData['q'];
            $viewData['breadcrumbs'] = ["Resultados da pesquisa: ".$requestData['q']];
        }


        //Meta::registerSeoMetaTagsForPage($this);

        if ($this->using_posts) {

            //Posts
            $posts = $this->posts()->published();

            //Categorias
            if ($requestData['categorySlug'] ?? false) {
                $category = $this->categories()->where('slug', $requestData['categorySlug'])->first();
                if($category){
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

                Meta::setTitle('Resultados da pesquisa: '.$requestData['q']);
            }


            $posts = $posts->paginate(9);

            $viewData['posts'] = $posts;
            //Meta::setPaginationLinks($posts);
        }else if($this->config->sources && $this->config->sources->count()){

            $sourceData = [];

            foreach ($this->config->sources as $source) {
                $sourceData[$source->name] = $source->data;

                if ($this->config->isUsingModule('internal_pages')) {
                    foreach ($sourceData[$source->name]->items as $dataItem) {
                        $dataItem->internal_url = $this->internalUrl($dataItem, $source->internal_url);
                    }
                }
            }

            $viewData['data'] = $sourceData;
        }



        return [...$viewData, ...$merge_data];
    }

    public function loadSEO(){

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

    protected function getJsHtmlAttribute()
    {

        //Todo: considerar tema do site e futuramente da pagina
        $finalHtml = $this->js->html;

        //Forms JS
        /*if ($this->id && $this->can_use_forms && $this->has_form) {
            $finalHtml .= $this->formView()->renderSections()['js'] ?? '';
        }*/

        return $finalHtml;
    }

    protected function getCssHtmlAttribute()
    {

        //Todo: considerar tema do site e futuramente da pagina

        return $this->css->html;
    }

    //region GETS

    /*protected function getHtmlAttribute()
    {
        return $this->buildedHtml();
    }*/

    public function getInternalHtmlRawAttribute()
    {
        return $this->elements->internal_content->raw ?? '';
    }

    protected function getHtmlRawAttribute()
    {
        return $this->elements->content->raw ?? '';
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
        $this->setHtmlRawAttribute($value);
    }

    protected function setHtmlRawAttribute($value): void
    {
        $this->elements->content->raw = $value;
    }

    protected function setInternalHtmlAttribute($value): void
    {
        $this->setInternalHtmlRawAttribute($value);
    }

    protected function setInternalHtmlRawAttribute($value): void
    {
        $this->elements->internal_content->raw = $value;
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
        //Gerar slug se estiver em branco
        if (empty($this->slug) && !$this->is_home) {
            $this->slug = $this->title;
        }


        //Pagina Incial
        if ($this->is_home) {
            //Tirar demais páginas iniciais
            $this->site->pages()->where(function (Builder $q) {
                $q->where('is_home', 'true');
                if ($this->id) {
                    $q->whereNot('id', $this->id);
                }
            })->update([
                           'is_home' => false,
                       ]);
        }

        if ($this->id) {

            //Minifies
            $this->css->minify();
            $this->js->minify();

            //Build HTML
            //Todo: CACHE: $this->attributes['html'] = $this->buildedHtml();

        }

        return parent::save($options);
    }

    //endregion

    //region RELATIONS
    public function posts(){
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
