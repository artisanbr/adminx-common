<?php

namespace ArtisanBR\Adminx\Common\App\Models;

use ArtisanBR\Adminx\Common\App\Models\Bases\EloquentModelBase;
use ArtisanBR\Adminx\Common\App\Models\Generics\Assets\GenericAssetElementCSS;
use ArtisanBR\Adminx\Common\App\Models\Generics\Assets\GenericAssetElementJS;
use ArtisanBR\Adminx\Common\App\Models\Generics\Configs\ThemeConfig;
use ArtisanBR\Adminx\Common\App\Models\Generics\Elements\Themes\ThemeFooterElement;
use ArtisanBR\Adminx\Common\App\Models\Generics\Elements\Themes\ThemeHeaderElement;
use ArtisanBR\Adminx\Common\App\Models\Generics\Elements\Themes\ThemeMediaElement;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\HtmlModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\OwneredModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\PublicIdModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\WidgeteableModel;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasOwners;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasPublicIdAttribute;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasSelect2;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasUriAttributes;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasValidation;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToSite;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToUser;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasFiles;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasParent;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasWidgets;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;

class Theme extends EloquentModelBase implements WidgeteableModel, PublicIdModel, OwneredModel
{
    use SoftDeletes, HasUriAttributes, BelongsToSite, BelongsToUser, HasSelect2, HasParent, HasValidation, HasOwners, HasFiles, HasWidgets, HasPublicIdAttribute;

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
        'footer',
    ];

    protected $casts = [
        'title'      => 'string',
        'media'      => ThemeMediaElement::class,
        'config'      => ThemeConfig::class,
        'css'        => GenericAssetElementCSS::class,
        'js'         => GenericAssetElementJS::class,
        'header'         => ThemeHeaderElement::class,
        'header_html'         => 'string',
        'footer'         => ThemeFooterElement::class,
        'footer_html'         => 'string',
        'created_at' => 'datetime:d/m/Y H:i:s',
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

        if($this->menu && $this->menu->id) {
            $this->menu->append('html');
        }

        if($this->menu_footer && $this->menu_footer->id) {
            $this->menu_footer->append('html');
        }
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

    protected function footerHtml(): Attribute
    {
        return Attribute::make(get: fn() => $this->footer->html);
    }
    protected function headerHtml(): Attribute
    {
        return Attribute::make(get: fn() => $this->header->html);
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

    protected function getJsHtmlAttribute(){
        return $this->js->html;
    }

    protected function getCssHtmlAttribute(){
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

        if(!$this->id){
            //Salvar antes de gerar o HTML Avançado no caso de um novo tema
            parent::save($options);
            $this->refresh();
        }

        //Cache dos HTMLs
        $this->header->flushHtmlCache($this->site, $this);
        $this->footer->flushHtmlCache($this->site, $this);

        return parent::save($options);
    }

    public function delete()
    {
        //Todo: permissions, parents e childs

        return parent::delete(); // TODO: Change the autogenerated stub
    }

    //endregion
}
