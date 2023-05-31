<?php

namespace Adminx\Common\Models;

use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\Assets\GenericAssetElementCSS;
use Adminx\Common\Models\Generics\Assets\GenericAssetElementJS;
use Adminx\Common\Models\Generics\Configs\ThemeConfig;
use Adminx\Common\Models\Generics\Elements\Themes\ThemeFooter;
use Adminx\Common\Models\Generics\Elements\Themes\ThemeFooterElement;
use Adminx\Common\Models\Generics\Elements\Themes\ThemeHeader;
use Adminx\Common\Models\Generics\Elements\Themes\ThemeHeaderElement;
use Adminx\Common\Models\Generics\Elements\Themes\ThemeMediaElement;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasFiles;
use Adminx\Common\Models\Traits\Relations\HasParent;
use Adminx\Common\Models\Traits\Relations\HasWidgets;
use App\Providers\AppMetaTagsServiceProvider;
use Butschster\Head\Facades\Meta;
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
        'menu_id',
        'menu_footer_id',
        'public_id',
        'title',
        'media',
        'css',
        'js',
        'config',
        'header',
        'header_old',
        'footer',
        'footer_old',
    ];

    protected $casts = [
        'title'       => 'string',
        'media'       => ThemeMediaElement::class,
        'config'      => ThemeConfig::class,
        'css'         => GenericAssetElementCSS::class,
        'js'          => GenericAssetElementJS::class,
        'header'      => ThemeHeader::class,
        'header_old'  => ThemeHeaderElement::class,
        'header_html' => 'string',
        'footer'      => ThemeFooter::class,
        'footer_old'  => ThemeFooterElement::class,
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
        $this->load(['site']);

        AppMetaTagsServiceProvider::registerFrontendPackages();

        Meta::registerSeoMetaTagsForSite($this->site);
        Meta::removeTag('description');
        Meta::removeTag('csrf-token');
        Meta::removeTag('keywords');
        Meta::removeTag('viewport');
        Meta::removeTag('charset');

        $pages = $this->site->pages;

        $theme = $this->site->theme;

        $htmlMin = new HtmlMin();

        $themeBuild = $theme->build()->firstOrNew([
                                                      'site_id'    => $this->site_id,
                                                      'user_id'    => $this->user_id,
                                                      'account_id' => $this->account_id,
                                                  ]);

        $headerHtml = View::make('adminx-frontend::layout.partials.header', [
            'site' => $this->site,
        ])->render();

        $footerHtml = View::make('adminx-frontend::layout.partials.footer', [
            'site' => $this->site,
        ])->render();

        $themeBuild->fill([
                              'header' => $this->site->config->enable_html_minify ? $htmlMin->minify($headerHtml) : $headerHtml,
                              'footer' => $this->site->config->enable_html_minify ? $htmlMin->minify($footerHtml) : $footerHtml,
                          ]);

        return $themeBuild->save() ? $themeBuild : null;
    }
    //endregion

    //region ATTRIBUTES
    //Select2
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

        return Attribute::make(get: fn() => preg_replace( '/[^\@]{{/m', "@{{", $this->footer->raw));
    }

    protected function headerTwigHtml(): Attribute
    {
        //return Attribute::make(get: fn() => $this->header->raw);
        return Attribute::make(get: fn() => preg_replace( '/[^\@]{{/m', "@{{", $this->header->raw));
    }

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

    protected function getJsHtmlAttribute()
    {
        return $this->js->html;
    }

    protected function getCssHtmlAttribute()
    {
        return $this->css->html;
    }

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

        //Minifies
        $this->css->minify();
        $this->js->minify();

        /*if (!$this->id) {
            //Salvar antes de gerar o HTML Avançado no caso de um novo tema
            parent::save($options);
            $this->refresh();
        }*/

        //Cache dos HTMLs
        //$this->header_old->flushHtmlCache($this->site, $this);
        //$this->footer_old->flushHtmlCache($this->site, $this);

        return parent::save($options);
    }

    public function delete()
    {
        //Todo: permissions, parents e childs

        return parent::delete(); // TODO: Change the autogenerated stub
    }

    //endregion
}
