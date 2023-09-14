<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages;

use Adminx\Common\Enums\ContentEditorType;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\CustomLists\Abstract\CustomListAbstract;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Interfaces\UploadModel;
use Adminx\Common\Models\Objects\Frontend\Assets\FrontendAssetsBundle;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Pages\Objects\PageInternalConfig;
use Adminx\Common\Models\Traits\HasBreadcrumbs;
use Adminx\Common\Models\Traits\HasGenericConfig;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasPublicIdUriAttributes;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToPage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property EloquentModelBase|CustomListAbstract $model
 */
class PageInternal extends EloquentModelBase implements PublicIdModel, UploadModel

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
        'title',
        'slug',
        'content',
        'config',
        'assets',
        'frontend_build',
    ];

    protected $casts = [
        'title'          => 'string',
        'content'        => 'string',
        'config'         => PageInternalConfig::class,
        'assets'         => FrontendAssetsBundle::class,
        'frontend_build' => FrontendBuildObject::class,
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
        $uploadPath = "internals/{$this->public_id}";

        return ($this->page ? $this->page->uploadPathTo($uploadPath) : $uploadPath) . ($path ? "/{$path}" : '');
    }

    public function prepareFrontendBuild($buildMeta = false): FrontendBuildObject
    {
        //$frontendBuild = $this->page->frontend_build;
        $frontendBuild = new FrontendBuildObject();

        //Antes inicio da tag head
        //$frontendBuild->head->addBefore(Meta::toHtml());
        $frontendBuild->head->css .= $this->assets->css_bundle_html;

        //Fim d atag head
        $frontendBuild->head->addAfter($this->assets->js->head_html ?? '');
        $frontendBuild->head->addAfter($this->assets->head_script->html ?? '');

        //Inicio do body
        $frontendBuild->body->id = "internal-{$this->public_id}";
        $frontendBuild->body->class .= " internal-{$this->public_id}";
        $frontendBuild->body->addBefore($this->assets->js->before_body_html ?? '');

        //Fim do body
        $frontendBuild->body->addAfter($this->assets->js->after_body_html ?? '');

        /*$frontendBuild->seo->fill([
                                      'title'         => $this->getTitle(),
                                      'title_prefix' => "{{ site.getTitle() }} - {{ page.getTitle() }}",
                                      'description'   => $this->getDescription(),
                                      'keywords'      => $this->getKeywords(),
                                      'image_url'     => $this->seoImage(),
                                      'published_at'  => $this->created_at->toIso8601String(),
                                      'updated_at'    => $this->updated_at->toIso8601String(),
                                      'canonical_uri' => $this->uri,
                                      'document_type' => 'page',
                                      'html'          => '',
                                  ]);*/

        /*if($buildMeta){
            $frontendBuild->meta->reset();
            $frontendBuild->meta->registerSeoForPageInternal($this);

            //$frontendBuild->head->addBefore($frontendBuild->meta->toHtml());
            $frontendBuild->seo->html = $frontendBuild->meta->toHtml();
        }*/

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
        return $this->page->urlTo(!empty($this->slug) ? "{$this->slug}/" : '');
    }
    //endregion

    //endregion

    //region OVERRIDES

    public function save(array $options = [])
    {
        if (parent::save($options)) {
            $this->frontend_build = $this->prepareFrontendBuild();

            return parent::save($options);
        }


        return false;
    }
    //endregion

    //region RELATIONS

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    //endregion
}
