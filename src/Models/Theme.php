<?php

namespace Adminx\Common\Models;

use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\Assets\GenericAssetElementCSS;
use Adminx\Common\Models\Generics\Assets\GenericAssetElementJS;
use Adminx\Common\Models\Generics\Configs\ThemeConfig;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Interfaces\UploadModel;
use Adminx\Common\Models\Objects\Frontend\Assets\FrontendAssetsBundle;
use Adminx\Common\Models\Objects\Themes\ThemeFooterObject;
use Adminx\Common\Models\Objects\Themes\ThemeHeaderObject;
use Adminx\Common\Models\Objects\Themes\ThemeMediaBundleObject;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasFiles;
use Adminx\Common\Models\Traits\Relations\HasParent;
use App\Providers\AppMetaTagsServiceProvider;
use Butschster\Head\Contracts\Packages\ManagerInterface;
use Butschster\Head\Facades\Meta;
use Butschster\Head\Facades\PackageManager;
use Butschster\Head\Packages\Package;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\View;
use voku\helper\HtmlMin;

class Theme extends EloquentModelBase implements PublicIdModel, OwneredModel, UploadModel
{
    use SoftDeletes, HasUriAttributes, BelongsToSite, BelongsToUser, HasSelect2, HasParent, HasValidation, HasOwners, HasFiles, HasPublicIdAttribute;

    protected $fillable = [
        'account_id',
        'site_id',
        'user_id',
        'parent_id',
        //'menu_id',
        //'menu_footer_id',
        'public_id',
        'title',
        'media',
        'assets',
        'css',
        'js',
        'config',
        'header',
        //'header_old',
        'footer',
        //'footer_old',
    ];

    protected $casts = [
        'title'       => 'string',
        'config'      => ThemeConfig::class,
        'media'       => ThemeMediaBundleObject::class,
        'assets'      => FrontendAssetsBundle::class,
        'css'         => GenericAssetElementCSS::class,
        'js'          => GenericAssetElementJS::class,
        'header'      => ThemeHeaderObject::class,
        //'header_old'  => ThemeHeaderElement::class,
        'header_html' => 'string',
        'footer'      => ThemeFooterObject::class,
        //'footer_old'  => ThemeFooterElement::class,
        'footer_html' => 'string',
        'created_at'  => 'datetime:d/m/Y H:i:s',
    ];

    protected $appends = [
        'text',
    ];

    //region VALIDATION
    public static function createRules(FormRequest $request = null): array
    {
        return [
            'title'               => ['required'],
            'media.*.file_upload' => ['nullable', 'image'],
            'parent_id'           => ['nullable', 'integer', 'exists:themes,id'],
        ];
    }
    //endregion

    //region HELPERS
    public function uploadPathTo(?string $path = null): string
    {
        $uploadPath = "themes/{$this->public_id}";
        return ($this->page ? $this->page->uploadPathTo($uploadPath) : $uploadPath) . ($path ? "/{$path}" : '');
    }

    public function prepareHtml()
    {
        $this->append(['logo', 'logo_secondary', 'favicon']);

        /*$this->media->logo->append('file');
        $this->media->logo_secondary->append('file');
        $this->media->favicon->append('file');*/

        if ($this->menu && $this->menu->id) {
            $this->menu->append('html');
        }

        if ($this->menu_footer && $this->menu_footer->id) {
            $this->menu_footer->append('html');
        }
    }

    public function compile()
    {
        if (!$this->site) {
            $this->load(['site']);
        }

        //Meta::reset();
        AppMetaTagsServiceProvider::registerFrontendPackages();


        $meta = new \Butschster\Head\MetaTags\Meta(
            app(ManagerInterface::class),
            app('config')
        );

        //$meta->addCsrfToken();
        $meta->initialize();

        /*$this->registerMetaPackage();

        $meta->setFavicon($this->media->favicon->url ?? '');

        $meta->includePackages([$this->meta_pkg_name, 'frontend.pos']);*/

        $meta->registerFromSiteTheme($this);
        $meta->registerFromSite($this->site);
        $meta->removeTag('description');
        $meta->removeTag('keywords');
        $meta->removeTag('viewport');
        $meta->removeTag('charset');

        /*Meta::addCsrfToken();
        Meta::initialize();

        Meta::registerFromSiteTheme($this);
        Meta::registerFromSite($this->site);
        Meta::removeTag('description');
        Meta::removeTag('csrf-token');
        Meta::removeTag('keywords');
        Meta::removeTag('viewport');
        Meta::removeTag('charset');*/

        //$pages = $this->site->pages;

        $htmlMin = new HtmlMin();

        $themeBuild = $this->build()->firstOrNew([
                                                     'site_id'    => $this->site_id,
                                                     'user_id'    => $this->user_id,
                                                     'account_id' => $this->account_id,
                                                 ]);

        $headerHtml = View::make('adminx-frontend::layout.partials.header', [
            'site'  => $this->site,
            'theme' => $this,
            'meta' => $meta,
        ])->render();

        $footerHtml = View::make('adminx-frontend::layout.partials.footer', [
            'site'  => $this->site,
            'theme' => $this,
            'meta' => $meta,
        ])->render();

        $themeBuild->fill([
                              'header' => $this->site->config->enable_html_minify ? $htmlMin->minify($headerHtml) : $headerHtml,
                              'footer' => $this->site->config->enable_html_minify ? $htmlMin->minify($footerHtml) : $footerHtml,
                          ]);

        $retorno = $themeBuild->save() ? $themeBuild : null;

        $this->unregisterMetaPackage();

        return $retorno;
    }

    public function registerMetaPackage()
    {
        PackageManager::create($this->meta_pkg_name, function (Package $package) {

            $packagesInclude = [];

            //Frameworks
            if ($this) {

                if ($this->config->jquery) {
                    $packagesInclude[] = 'jquery';
                }

                if (!$this->config->no_framework) {
                    $packagesInclude[] = $this->config->framework->value;
                }

                $packagesInclude = [...$packagesInclude, ...($this->config->plugins->toArray() ?? [])];
            }

            $packagesInclude[] = 'frontend.pre';

            $package->requires($packagesInclude);

            /**
             * @var File $file
             */
            foreach ($this->files()->themeBundleSortened()->values() as $file) {

                if ($file->extension === 'css') {
                    //Todo: habilitar DEFER

                    /*[
                        'rel'    => 'stylesheet',
                        'media'  => 'print',
                        'onload' => "this.media='all'",
                    ]*/
                    //dump($file->path);
                    $package->addStyle($file->name, $file->url);
                }
                if ($file->extension === 'js') {
                    $package->addScript($file->name, $file->url, ['defer']);
                }
            }
        });
    }

    public function unregisterMetaPackage()
    {
        Meta::removePackage('frontend.pre');
        Meta::removePackage($this->meta_pkg_name);
        Meta::removePackage('frontend.pos');
    }
    //endregion

    //region ATTRIBUTES
    protected function metaPkgName(): Attribute
    {
        return Attribute::make(get: fn() => "theme_meta_pkg_{$this->id}");
    }

    protected function text(): Attribute
    {
        return Attribute::make(get: fn() => ($this->parent && $this->parent->title ? "{$this->parent->title} &raquo; " : '') . ($this->title ?? ''),);
    }

    protected function uploadPath(): Attribute
    {
        return Attribute::make(get: fn() => "{$this->site->upload_path}/themes/{$this->public_id}");
    }

    protected function footerTwigHtml(): Attribute
    {

        return Attribute::make(get: fn() => preg_replace('/[^\@]{{/m', "@{{", $this->footer->raw));
    }

    protected function headerTwigHtml(): Attribute
    {
        //return Attribute::make(get: fn() => $this->header->raw);
        return Attribute::make(get: fn() => preg_replace('/[^\@]{{/m', "@{{", $this->header->raw));
    }


    /*protected function getJsHtmlAttribute()
    {
        return $this->js->html;
    }

    protected function getCssHtmlAttribute()
    {
        return $this->css->html;
    }*/


    /*protected function getLogoAttribute()
    {
        return  $this->media->logo->file->url ?? config('adminx.defines.files.default.files.theme.media.logo');
    }

    protected function getLogoSecondaryAttribute()
    {
        return $this->media->logo_secondary->file->url ?? config('adminx.defines.files.default.files.theme.media.logo_secondary');
    }

    protected function getFaviconAttribute()
    {
        return $this->media->favicon->file->url ?? config('adminx.defines.files.default.files.theme.media.favicon');
    }*/


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

    //region RELATIONS

    public function build()
    {
        return $this->hasOne(ThemeBuild::class, 'theme_id', 'id');
    }

    public function menu()
    {
        return $this->hasOne(Menu::class, 'id', 'menu_id');
    }

    public function menu_footer()
    {
        return $this->hasOne(Menu::class, 'id', 'menu_footer_id');
    }


    //endregion

    //region OVERRIDES

    public function save(array $options = []): bool
    {
        //Minify
        $this->assets->minify();

        return parent::save($options);
    }

    public function saveAndCompile(array $options = []): bool
    {
        $return = parent::save($options);

        //Compile
        if ($return) {
            sleep(1);
            $this->refresh();
            $this->compile();
        }

        return $return;
    }

    public function delete()
    {
        //Todo: permissions, parents e childs

        return parent::delete(); // TODO: Change the autogenerated stub
    }

    //endregion
}
