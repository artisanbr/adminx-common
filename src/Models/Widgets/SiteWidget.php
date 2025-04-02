<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Widgets;

use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\DataSource;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Scopes\WhereSiteScope;
use Adminx\Common\Models\Templates\Enums\TemplateRenderEngine;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasTemplates;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Widget;
use Adminx\Common\Models\Widgets\Objects\WidgetConfigObject;
use Adminx\Common\Models\Widgets\Objects\WidgetContentObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\View;

class SiteWidget extends EloquentModelBase implements PublicIdModel, OwneredModel
{
    use HasPublicIdAttribute, HasOwners, BelongsToSite, BelongsToUser, HasTemplates;

    protected array $ownerTypes = ['site', 'user'];

    protected $table      = 'site_widgets';
    public    $timestamps = false;

    protected $fillable = [
        'site_id',
        'user_id',
        'widget_id',
        'vars',
        'source',
        'title',
        'slug',
        'config',
        'content',
        'template_content',
    ];

    protected $casts = [
        'config'    => WidgetConfigObject::class,
        'vars'      => 'collection',
        'source'    => DataSource::class,
        'public_id' => 'string',
        'title'     => 'string',
        //'sources'    => 'collection',
        'css_class' => 'string',
        'slug'      => 'string',
        'content'   => WidgetContentObject::class,
        //'use_widget' => 'boolean',
        //'no_widget'  => 'boolean',
    ];

    /*protected $attributes = [
    ];*/

    protected $with = ['widget'];

    protected $appends = [
        //'use_widget',
        ///'no_widget',
    ];

    protected $hidden = ['template_content'];

    protected array $viewRenderData = [];

    //region HELPERS


    public function compile($save = false): static
    {
        if ($this->config->ajax_render) {
            $renderView = 'common::Elements.Widgets.renders.ajax-render';

            $renderData = $this->config->ajax_render ? [
                'siteWidget' => $this,
            ] : $this->getViewRenderData();

            $widgetView = View::make($renderView, $renderData);

            $this->content->portal = $widgetView->render();
        }
        else {
            $this->content->portal = null;
        }

        /*if($this->template) {
            $this->content->html = $this->template->content;
        }*/

        if ($save) {
            $this->save();
        }

        return $this;
    }

    public function getViewRenderData(array $merge_data = []): array
    {
        if(!count($this->viewRenderData)){
            $site = FrontendSite::current() ?? $this->site;
            //Debugbar::startMeasure('start view data');
            $viewData = [
                'site'      => $site,
                //'variables' => $this->variables,
                'widget'    => $this,
            ];
            //Debugbar::stopMeasure('start view data');

            //Debugbar::startMeasure('require source');
            //Debugbar::debug($this->source->type);
            if ($this->config->require_source) {
                switch (true) {
                    case $this->source->type === 'articles':
                    case $this->source->type === 'posts':
                        /**
                         * @var Page|null $page ;
                         */

                        if ($this->source->type === 'posts') {
                            $this->source->type = 'articles';
                            $this->save();
                        }

                        $page = $this->source->page;

                        if ($page) {
                            $articlesQuery = $this->source->dataQuery()->published();


                            $articlesQuery = $this->config->sorting->applySort($articlesQuery);

                            $viewData['page'] = $page->makeHidden(['site']);
                            $viewData['sourceData'] = $viewData['articles'] = $articlesQuery->take(10)->get();
                        }
                        break;
                    case Str::contains($this->source->type, 'list'):

                        $customList = $this->source->data;
                        //$page = $customList->page;
                        //$viewData['page'] = $page;
                        $viewData['sourceData'] = $viewData['customList'] = $customList->toArray();
                        //Todo: personalizar quantidade de itens
                        $viewData['customListItems'] = $customList->items()->take(10)->get();
                        break;
                    case $this->source->type === 'form':
                        $viewData['sourceData'] = $viewData['form'] = $this->source->data;
                        break;
                    default:
                        break;
                    //Todo:
                    /*case 'page':
                    case 'products':
                    case 'form':
                    case 'article':
                    case 'address':*/

                }
            }

            //Debugbar::stopMeasure('require source');

            $this->viewRenderData = !empty($merge_data) ? [...$viewData, ...$merge_data] : $viewData;
        }

        return $this->viewRenderData;
    }

    public function getTwigRenderData(array $merge_data = []): array
    {
        $twigData = $this->getViewRenderData([...$merge_data, 'widget' => $this->toCleanArray()]);
        //$twigData = $this->getViewRenderData($merge_data);

        //$twigData['widget'] = $this->toCleanArray();

        return $twigData;
    }

    /*public function attributesToArray()
    {

        // If an attribute is a date, we will cast it to a string after converting it
        // to a DateTime / Carbon instance. This is so we will get some consistent
        // formatting while accessing attributes vs. arraying / JSONing a model.
        $attributes = $this->addDateAttributesToArray(
            $attributes = $this->getArrayableAttributes()
        );

        $attributes = $this->addMutatedAttributesToArray(
            $attributes, $mutatedAttributes = $this->getMutatedAttributes()
        );

        // Next we will handle any casts that have been setup for this model and cast
        // the values to their appropriate type. If the attribute has a mutator we
        // will not perform the cast on those attributes to avoid any confusion.
        $attributes = $this->addCastAttributesToArray(
            $attributes, $mutatedAttributes
        );

        // Here we will grab all of the appended, calculated attributes to this model
        // as these attributes are not really in the attributes array, but are run
        // when we need to array or JSON the model for convenience to the coder.
        foreach ($this->getArrayableAppends() as $key) {
            $attributes[$key] = $this->mutateAttributeForArray($key, null);
        }

        return $attributes;
    }

    public static function cacheMutatedAttributes($classOrInstance)
    {
        $reflection = new ReflectionClass($classOrInstance);

        $class = $reflection->getName();


        static::$getAttributeMutatorCache[$class] =
            collect($attributeMutatorMethods = static::getAttributeMarkedMutatorMethods($classOrInstance))
                ->mapWithKeys(function ($match) {
                    dump($match);

                    return [lcfirst(static::$snakeAttributes ? \Illuminate\Support\Str::snake($match) : $match) => true];
                })->all();


        static::$mutatorCache[$class] = collect(static::getMutatorMethods($class))
            ->merge($attributeMutatorMethods)
            ->map(function ($match) {
                dump($match);

                return lcfirst(static::$snakeAttributes ? Str::snake($match) : $match);
            })->all();
    }

    protected static function getAttributeMarkedMutatorMethods($class)
    {
        $instance = is_object($class) ? $class : new $class;

        return collect((new ReflectionClass($instance))->getMethods())->filter(function ($method) use ($instance) {
            $returnType = $method->getReturnType();

            if ($returnType instanceof ReflectionNamedType &&
                $returnType->getName() === Attribute::class) {
                if (is_callable($method->invoke($instance)->get)) {
                    return true;
                }
            }

            return false;
        })->map->name->values()->all();
    }*/


    public function getSortingColumns(): array
    {
        $sourceType = $this->source->type ?? $this->widget?->config->source_types->first() ?? false;

        return $sourceType ? config("adminx.data-sources.types.widget.{$sourceType}.sorting_columns", []) : [];

        //return DataSource::getSourceTypeConfig($this->source->type ?? $this->widget?->config->source_types->first(), 'sorting_columns', []);
    }
    //endregion

    //region  Attributes
    protected function templateContent(): Attribute
    {
        if ($this->template?->config->use_files) {

            if ($this->template->config->render_engine === TemplateRenderEngine::Blade) {
                $templateContent = View::make($this->template->blade_file, $this->getViewRenderData())->render();
            }
            else {
                $templateContent = $this->template->file_contents;
            }
        }
        else {
            $templateContent = $this->template?->content ?? $this->template?->file_contents ?? null;
        }

        return new Attribute(
            get: static fn() => $templateContent,
        );
    }

   /* protected function contentRender(): Attribute
    {
        $contentRender = '';

        if ($this->config->use_files) {

            if ($this->config->render_engine === TemplateRenderEngine::Blade) {

                dd(View::make($this->blade_file, $this->getViewRenderData())->render());

                return View::make($this->blade_file, $this->getViewRenderData());

            }
        }

        return Attribute::make(
            get: function ($value) {

                dd($value, $this->config->use_files);

                /**$content = $value;
                 *
                 * if(empty($content)){
                 *
                 * }
                 *
                 * if($this->config->render_mode)

                return (!empty($value) && (string)$value !== (string)$this->file_contents) ? $value : null;
            }
        );
    }*/

    /*protected function html(): Attribute
    {
        $renderView = $this->config->ajax_render ? 'common::Elements.Widgets.renders.ajax-render' : 'common::Elements.Widgets.renders.static-render';

        $renderData = $this->config->ajax_render ? [
            'siteWidget' => $this,
        ] : $this->getViewRenderData();

        $widgetView = View::make($renderView, $renderData);

        return new Attribute(
            get: static fn() => $widgetView->render(),
        );
    }*/

    protected function variables(): Attribute
    {
        //dd($this->widget?->config, $this->widget?->config->variables);

        return new Attribute(
            get: fn() => collect($this->widget?->config ? $this->widget?->config->variables->pluck('default_value', 'slug') : [])->merge($this->config ? $this->config->variables->pluck('value', 'slug') : [])->toArray(),
        );
    }

    protected function useWidget(): Attribute
    {
        return new Attribute(
            get: fn() => (bool)$this->widget_id,
        );
    }

    protected function noWidget(): Attribute
    {
        return new Attribute(
            get: fn() => !$this->widget_id,
        );
    }

    //region Get's

    /*protected function sourcesList(): Attribute
    {
        return new Attribute(
            get: fn() => $this->source_type && $this->site ? self::getSourcesByType($this->source_type, $this->site) : collect(),
        );
    }*/

    /*protected function sources(): Attribute
    {
        return new Attribute(
            get: function () {

                if ($this->source_type && $this->site && $this->source_id) {
                    return $this->sources_list->whereIn('id', $this->source_ids->values());
                }

                return collect();
            },
        );
    }*/

    /*protected function sourceId(): Attribute
    {
        return new Attribute(
            get: fn() => $this->source_ids && $this->source_ids->count() ? $this->source_ids->first() : null,
        );
    }
    protected function sourceType(): Attribute
    {
        return new Attribute(
            get: fn() => $this->source_ids && $this->source_ids->count() ? $this->source_ids->first() : null,
        );
    }*/

    /*protected function source(): Attribute
    {
        return new Attribute(
            get: fn() => $this->sources->firstWhere('id', $this->source_id),
        );
    }*/

    protected function cssClass(): Attribute
    {
        $cssClass = Str::replaceNative('.', '-', ($this->widget ? "widget-{$this->widget?->type->slug}-{$this->widget?->slug} " : '') . "widget-{$this->public_id}");

        return new Attribute(
            get: fn() => $cssClass,
        );
    }
    //endregion

    //endregion

    //region Overrides

    protected static function booted()
    {
        static::addGlobalScope(new WhereSiteScope);
    }

    public function save(array $options = [])
    {
        parent::save($options);

        $this->compile();

        return parent::save($options);
    }

    //endregion

    //region Relations
    public function widget()
    {
        return $this->belongsTo(Widget::class);
    }


    public function site_widget_template()
    {
        return $this->modelTemplate();
    }
    //endregion
}
