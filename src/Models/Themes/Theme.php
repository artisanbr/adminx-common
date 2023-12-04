<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\File;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Objects\Frontend\Assets\Abstract\AbstractFrontendAssetsResourceScript;
use Adminx\Common\Models\Objects\Frontend\Assets\FrontendAssetsBundle;
use Adminx\Common\Models\Themes\Objects\Config\ThemeConfig;
use Adminx\Common\Models\Themes\Objects\ThemeCopyrightObject;
use Adminx\Common\Models\Themes\Objects\ThemeFooterObject;
use Adminx\Common\Models\Themes\Objects\ThemeHeaderObject;
use Adminx\Common\Models\Themes\Objects\ThemeMediaBundleObject;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasFiles;
use Adminx\Common\Models\Traits\Relations\HasParent;
use App\Libs\Utils\FrontendUtils;
use App\Providers\AppMetaTagsServiceProvider;
use Butschster\Head\Contracts\Packages\ManagerInterface;
use Butschster\Head\Facades\Meta as MetaTags;
use Butschster\Head\Facades\PackageManager;
use Butschster\Head\MetaTags\Meta;
use Butschster\Head\Packages\Package;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\View;
use voku\helper\HtmlMin;

class Theme extends EloquentModelBase implements PublicIdModel, OwneredModel
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
        'copyright',
        //'footer_old',
    ];

    protected $casts = [
        'title'       => 'string',
        'config'      => ThemeConfig::class,
        'media'       => ThemeMediaBundleObject::class,
        'assets'      => FrontendAssetsBundle::class,
        //'css'         => GenericAssetElementCSS::class,
        //'js'          => GenericAssetElementJS::class,
        'header'      => ThemeHeaderObject::class,
        'header_html' => 'string',
        'footer'      => ThemeFooterObject::class,
        'copyright'   => ThemeCopyrightObject::class,
        'footer_html' => 'string',
        'created_at'  => 'datetime:d/m/Y H:i:s',
    ];

    protected $appends = [
        'text',
    ];

    //protected $with = ['site'];

    //region VALIDATION
    public static function createRules(FormRequest $request = null): array
    {
        return [
            'title'                     => ['required'],
            'media.*.file_upload'       => ['nullable', 'mimetypes:image/png,image/jpeg,image/webp,image/svg+xml'],
            'media.favicon.file_upload' => [
                'nullable',
                'mimetypes:image/png,image/jpeg,image/webp,image/x-icon,image/svg+xml',
            ],
            'parent_id'                 => ['nullable', 'integer', 'exists:themes,id'],
        ];
    }
    //endregion

    //region HELPERS
    public function uploadPathTo(?string $path = null): string
    {
        return $this->storagePathTo('upload' . ($path ? "/{$path}" : ''));
    }

    public function storagePathTo(?string $path = null): string
    {
        $uploadPath = "themes/{$this->public_id}";

        return ($this->site ? $this->site->uploadPathTo($uploadPath) : $uploadPath) . ($path ? "/{$path}" : '');
    }

    public function traitCdnPath(string $path): string
    {
        if (Str::startsWith($path, $this->site->cdn_proxy_url)) {
            $path = Str::remove($this->site->cdn_proxy_url, $path);
        }

        if (Str::startsWith($path, $this->site->upload_path)) {
            $path = Str::remove($this->site->upload_path . "/", $path);
        }

        //dd($path, $this->storage_relative_path);

        if (!Str::startsWith($path, $this->storage_relative_path)) {
            $path = $this->storage_relative_path . "/" . $path;
        }

        return $path;
    }


    public function assetResourceUrl(AbstractFrontendAssetsResourceScript $assetScript): string
    {
        return $assetScript->external ? $assetScript->url : $this->cdnUrlTo("upload/{$assetScript->url}");
    }

    public function cdnUrlTo(string $path = ''): string
    {

        return $this->site->cdnUrlTo($this->traitCdnPath($path));

    }

    public function cdnUriTo(string $path = ''): string
    {
        return $this->site->cdnUriTo($this->traitCdnPath($path));
    }

    public function cdnProxyUrlTo(string $path = ''): string
    {
        return $this->site->cdnProxyUrlTo($this->traitCdnPath($path));
    }

    public function cdnProxyUriTo(string $path = ''): string
    {
        return $this->site->cdnProxyUriTo($this->traitCdnPath($path));
    }

    public function compile()
    {
        if (!$this->site) {
            $this->load(['site']);
        }

        //Meta::reset();
        AppMetaTagsServiceProvider::registerFrontendPackages();


        $themeMeta = new Meta(
            app(ManagerInterface::class),
            app('config')
        );

        //$meta->addCsrfToken();
        $themeMeta->initialize();

        /*$this->registerMetaPackage();

        $meta->setFavicon($this->media->favicon->url ?? '');

        $meta->includePackages([$this->meta_pkg_name, 'frontend.pos']);*/

        $themeMeta->reset();
        $themeMeta->registerFromSiteTheme($this);
        $themeMeta->registerFromSite($this->site);
        $themeMeta->removeTag('description');
        $themeMeta->removeTag('keywords');
        $themeMeta->removeTag('viewport');
        $themeMeta->removeTag('charset');

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

        $headHtml = View::make('common-frontend::layout.partials.head', [
            'site'      => $this->site,
            'theme'     => $this,
            'themeMeta' => $themeMeta,
        ])->render();

        $headerHtml = View::make('common-frontend::layout.partials.header', [
            'site'      => $this->site,
            'theme'     => $this,
            'themeMeta' => $themeMeta,
        ])->render();

        $footerHtml = View::make('common-frontend::layout.partials.footer', [
            'site'      => $this->site,
            'theme'     => $this,
            'themeMeta' => $themeMeta,
        ])->render();

        $themeBuild->fill([
                              'head'   => $this->site->config->enable_html_minify ? $htmlMin->minify($headHtml) : $headHtml,
                              'header' => $this->site->config->enable_html_minify ? $htmlMin->minify($headerHtml) : $headerHtml,
                              'footer' => $this->site->config->enable_html_minify ? $htmlMin->minify($footerHtml) : $footerHtml,
                          ]);

        $retorno = $themeBuild->save() ? $themeBuild : null;

        $this->unregisterMetaPackage();

        return $retorno;
    }

    public function registerMetaPackage(): void
    {
        PackageManager::create($this->meta_pkg_name, function (Package $package) {

            //Libraries
            $this->config->libs->registerMetaPackage($package);


            //Frameworks
            /*if ($this->config->jquery_enable) {
                $package->addScript('jquery.js', "https://code.jquery.com/jquery-{$this->config->jquery_version}.min.js");
            }

            if ($this->config->jquery_ui_enable) {

                $juVersion = $this->config->jquery_ui_version;
                $juStrict = $this->config->jquery_ui_strict;
                //dd($juVersion, $juStrict, "https://code.jquery.com/ui/{$juVersion}/jquery-ui.min.js");

                //JQuery Ui Js
                if (!$juStrict || $juStrict === 'js') {
                    $package->addScript('jquery-ui-core.js', "https://code.jquery.com/ui/{$juVersion}/jquery-ui.min.js");
                }


                //JQuery Ui Css
                if (!$juStrict || $juStrict === 'css') {
                    //dd("https://code.jquery.com/ui/{$juVersion}/themes/base/jquery-ui.min.css");
                    $package->addStyle('jquery-ui-core.css', "https://code.jquery.com/ui/{$juVersion}/themes/base/jquery-ui.min.css");
                }

            }

            if ($this->config->bootstrap_enable) {

                $bsVersion = $this->config->bootstrap_version ?? collect(config('adminx.themes.versions.bootstrap'))->first();

                $bsStrict = $this->config->bootstrap_strict;

                //Bs Js
                if (!$bsStrict || $bsStrict === 'js') {
                    $package->addScript('bootstrap.bundle.js', "https://cdn.jsdelivr.net/npm/bootstrap@{$bsVersion}/dist/js/bootstrap.bundle.min.js", [
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',

                    ]);
                }


                //Bs Css
                if (!$bsStrict || $bsStrict === 'css') {
                    $package->addStyle('bootstrap.css', "https://cdn.jsdelivr.net/npm/bootstrap@{$bsVersion}/dist/css/bootstrap.min.css", [
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',

                    ]);
                }


                //Theme js

                if (Str::startsWith($bsVersion, '5')) {
                    $package->addScript('theme.bs5.js', FrontendUtils::asset('js/theme.main.bs5.js'));
                }
                else if (Str::startsWith($bsVersion, '4')) {
                    $package->addScript('theme.bs4.js', FrontendUtils::asset('js/theme.main.bs4.js'));
                }
            }
            */

            //region Theme Plugins

            $plataformPlugins = config('adminx.themes.plugins');
            $themePlugins = $this->config->plugins->toArray();

            foreach ($themePlugins as $pluginName) {

                $plugin = $plataformPlugins[$pluginName] ?? null;

                if ($plugin) {
                    if (isset($plugin['css'])) {
                        foreach ($plugin['css'] as $nameCSS => $pluginCSS) {
                            $package->addStyle($nameCSS, $pluginCSS['src'], $pluginCSS['attributes'] ?? []);
                        }
                    }

                    if (isset($plugin['js'])) {
                        foreach ($plugin['js'] as $nameJS => $pluginJS) {
                            $package->addScript($nameJS, $pluginJS['src'], $pluginJS['attributes'] ?? []);
                        }

                    }
                }


                //dd($pluginName, $plugin);

                /*PackageManager::create($name, function (Package $package) use ($plugin) {

                    if (isset($plugin['css'])) {
                        foreach ($plugin['css'] as $nameCSS => $pluginCSS) {
                            $package->addStyle($nameCSS, $pluginCSS['src'], $pluginCSS['attributes'] ?? []);
                        }
                    }

                    if (isset($plugin['js'])) {
                        foreach ($plugin['js'] as $nameJS => $pluginJS) {
                            $package->addScript($nameJS, $pluginJS['src'], $pluginJS['attributes'] ?? []);
                        }

                    }
                });*/
            }

            //endregion
            //}

            //region Theme Files

            foreach ($this->assets->resources->css->items as $assetItem) {
                $optArray = $assetItem->defer ? [
                    'rel'    => 'stylesheet',
                    'media'  => 'print',
                    'onload' => "this.media='all'",
                ] : [];
                //dd($assetItem->name, $assetItem->path, $assetItem->defer, $this->cdnUriTo($assetItem->path));
                $package->addStyle($assetItem->name, $this->assetResourceUrl($assetItem), $optArray);
            }

            foreach ($this->assets->resources->js->items as $assetItem) {
                $optArray = $assetItem->defer ? ['defer'] : [];
                $package->addScript($assetItem->name, $this->assetResourceUrl($assetItem), $optArray);
            }

            foreach ($this->assets->resources->head_js->items as $assetItem) {
                $optArray = $assetItem->defer ? ['defer'] : [];
                $package->addScript($assetItem->name, $this->assetResourceUrl($assetItem), $optArray, Meta::PLACEMENT_HEAD);
            }

            /**
             * todo: remove
             * @var File $file
             */
            /*foreach ($this->files()->themeBundleSortened()->values() as $file) {

                if ($file->extension === 'css') {
                    $package->addStyle($file->name, $file->url);
                }
                if ($file->extension === 'js') {
                    $package->addScript($file->name, $file->url, ['defer']);
                }
            }*/
            //endregion

            //region Pos
            $package
                //CSS
                ->addStyle('pace-theme-minimal.css',
                           'https://cdn.jsdelivr.net/npm/pace-js@1.2.4/themes/blue/pace-theme-minimal.css')
                /*->addStyle('font-awesome',
                           'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css',
                           [
                               'integrity'      => 'sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==',
                               'crossorigin'    => 'anonymous',
                               'referrerpolicy' => 'no-referrer',
                               'rel'            => 'stylesheet',
                               'media'          => 'print',
                               'onload'         => "this.media='all'",

                           ])*/
                /*->addStyle('all.min.css',
                           frontendThemeAsset('css/all.min.css'),
                )
                ->addStyle('main.c64b6eb2.css',
                           frontendThemeAsset('static/css/main.c64b6eb2.css'))*/

                //JS
                ->addScript('functions.js',
                            appAsset('js/functions.js'))
                ->addScript('pace.js',
                            'https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js')
                ->addScript('jquery.formHelper.js',
                            appAsset('js/plugins/jquery/jquery.formHelper.js'), ['defer'])
                ->addScript('modules.bundle.js', FrontendUtils::asset('js/modules.bundle.js'), ['defer'])
                ->addScript('theme.main.js',
                            FrontendUtils::asset('js/theme.main.js'), ['defer'])/* ->addScript('slick.min.js',
                             '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js')*/
            ;

            $package
                //CSS
                ->addStyle('theme.main.css',
                           FrontendUtils::asset('css/theme.main.css'), [
                               'rel'    => 'stylesheet',
                               'media'  => 'print',
                               'onload' => "this.media='all'",

                           ])
                ->addStyle('theme.custom.css',
                           FrontendUtils::asset('css/theme.custom.css'), [
                               'rel'    => 'stylesheet',
                               'media'  => 'print',
                               'onload' => "this.media='all'",

                           ]);
            //endregion


        });
    }

    public function unregisterMetaPackage()
    {
        MetaTags::removePackage('frontend.pre');
        MetaTags::removePackage($this->meta_pkg_name);
        MetaTags::removePackage('frontend.pos');
    }
    //endregion

    //region ATTRIBUTES
    protected function isMain(): Attribute
    {
        $isMain = $this->id && $this->site && (int)$this->site?->theme_id === $this->id;

        return Attribute::make(get: fn() => $isMain);
    }

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
        return Attribute::make(get: fn() => "{$this->storage_path}/upload");
    }

    protected function uploadRelativePath(): Attribute
    {
        return Attribute::make(get: fn() => "{$this->storage_relative_path}/upload");
    }

    protected function storagePath(): Attribute
    {
        return Attribute::make(get: fn() => "{$this->site->upload_path}/{$this->storage_relative_path}");
    }

    protected function storageRelativePath(): Attribute
    {
        return Attribute::make(get: fn() => "themes/{$this->public_id}");
    }

    protected function cdnUri(): Attribute
    {
        $cdnDomain = config('common.app.cdn_domain');

        return Attribute::make(get: fn() => "https://{$cdnDomain}/{$this->cdn_url}");
    }

    protected function cdnUrl(): Attribute
    {
        return Attribute::make(get: fn() => ($this->site?->cdn_url ?? '') . "/{$this->storage_relative_path}");
    }

    protected function cdnProxyUri(): Attribute
    {
        $cdnDomain = config('common.app.cdn_domain');

        return Attribute::make(get: fn() => "https://{$cdnDomain}/{$this->cdn_proxy_url}");
    }

    protected function cdnProxyUrl(): Attribute
    {
        return Attribute::make(get: fn() => ($this->site?->cdn_proxy_url ?? '') . "/{$this->storage_relative_path}");
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

    protected function logo(): Attribute
    {
        return Attribute::make(get: fn() => $this->media->logo->url);
    }

    protected function logoSecondary(): Attribute
    {
        return Attribute::make(get: fn() => $this->media->logo_secondary->url);
    }

    protected function favicon(): Attribute
    {
        return Attribute::make(get: fn() => $this->media->favicon->url);
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


    /*public function menu()
        {
            return $this->hasOne(Menu::class, 'id', 'menu_id');
        }

        public function menu_footer()
        {
            return $this->hasOne(Menu::class, 'id', 'menu_footer_id');
        }*/

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
