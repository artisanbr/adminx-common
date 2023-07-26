<?php

namespace Adminx\Common\Models\Pages;

use Adminx\Common\Enums\ContentEditorType;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\CustomListBase;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Interfaces\UploadModel;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Pages\Objects\PageBreadcrumb;
use Adminx\Common\Models\Pages\Objects\PageConfig;
use Adminx\Common\Models\Objects\Frontend\Assets\FrontendAssetsBundle;
use Adminx\Common\Models\Pages\Objects\PageModelConfig;
use Adminx\Common\Models\Traits\HasBreadcrumbs;
use Adminx\Common\Models\Traits\HasGenericConfig;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasPublicIdUriAttributes;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToPage;
use Butschster\Head\Facades\Meta;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property EloquentModelBase|CustomListBase $model
 */
class PageModel extends EloquentModelBase implements PublicIdModel, UploadModel

{
    use HasSelect2,
        HasValidation,
        HasGenericConfig,
        HasUriAttributes,
        HasPublicIdAttribute,
        HasPublicIdUriAttributes,
        HasBreadcrumbs,
        BelongsToPage;

    protected $fillable = [
        'page_id',
        'model_type',
        'model_id',
        'slug',
        'content',
        'config',
        'assets',
    ];

    protected $casts = [
        'content' => 'string',
        'config'  => PageModelConfig::class,
        'assets'  => FrontendAssetsBundle::class,
    ];

    protected $attributes = [
        'slug'       => null,
        'model_id'   => null,
        'model_type' => 'list',
        //'config'     => [],
    ];

    protected $appends = [
        //'can_use_forms',
        //'can_use_articles',
    ];

    //region VALIDATIONS
    public static function createRules(?FormRequest $request = null): array
    {
        return [
            //'slug' => ['required'],
            'model_id' => ['required'],
        ];
    }

    public static function createMessages(?FormRequest $request = null): array
    {
        return [
            'slug.required' => 'A URL é obrigatória',
        ];
    }
    //endregion

    //region HELPERS

    public function uploadPathTo(?string $path = null): string
    {
        $uploadPath = "models/{$this->public_id}";

        return ($this->page ? $this->page->uploadPathTo($uploadPath) : $uploadPath) . ($path ? "/{$path}" : '');
    }

    public function frontendBuild(): FrontendBuildObject
    {
        $frontendBuild = $this->page->frontendBuild();

        //Antes inicio da tag head
        //$frontendBuild->head->addBefore(Meta::toHtml());
        $frontendBuild->head->css .= $this->assets->css_bundle_html;

        //Fim d atag head
        $frontendBuild->head->addAfter($this->assets->js->head_html ?? '');
        $frontendBuild->head->addAfter($this->assets->head_script->html ?? '');

        //Inicio do body
        $frontendBuild->body->id = "article-{$this->public_id}";
        $frontendBuild->body->class .= " article-{$this->public_id}";
        $frontendBuild->body->addBefore($this->assets->js->before_body_html ?? '');

        //Fim do body
        $frontendBuild->body->addAfter($this->assets->js->after_body_html ?? '');

        return $frontendBuild;
    }

    //endregion

    //region ATTRIBUTES

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: static fn($value) => Str::contains($value, ' ') ? Str::slug(Str::ucfirst($value)) : Str::ucfirst($value),
        );
    }

    protected function editorType(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->config->editor_type ?? auth()->user()->config->editor_type ?? ContentEditorType::from('tinymce')
        );
    }

    protected function text(): Attribute
    {
        $title = $this->model->title ?? 'Sem Título';
        $type = $this->model?->type?->title() ?? '';
        return Attribute::make(
            get: fn() => "<h4>{$title}</h4> {$type}",
        );
    }

    //region GETS
    protected function getBreadcrumbConfigAttribute()
    {
        return $this->config->breadcrumb ?? $this->page->breadcrumb_config;
    }
    protected function getUrlAttribute(): string
    {
        return $this->page->urlTo($this->slug ? "{$this->slug}/" : '');
    }
    //endregion

    //endregion

    //region RELATIONS

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    //endregion
}
