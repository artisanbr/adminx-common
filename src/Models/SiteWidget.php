<?php

namespace Adminx\Common\Models;

use Adminx\Common\Enums\CustomLists\CustomListType;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\Configs\WidgetConfig;
use Adminx\Common\Models\Generics\DataSource;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Objects\Frontend\FrontendHtmlObject;
use Adminx\Common\Models\Scopes\WhereSiteScope;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Barryvdh\Debugbar\Facades\Debugbar;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class SiteWidget extends EloquentModelBase implements PublicIdModel, OwneredModel
{
    use HasPublicIdAttribute, HasOwners, BelongsToSite, BelongsToUser;

    protected array $ownerTypes = ['site','user'];

    protected $table        = 'site_widgets';
    public    $timestamps   = false;

    protected $fillable = [
        'site_id',
        'user_id',
        'widget_id',
        'vars',
        'source',
        'title',
        'config',
        'content',
    ];

    protected $casts = [
        'config'      => WidgetConfig::class,
        'vars'  => 'collection',
        'source'      => DataSource::class,
        'public_id'   => 'string',
        'title'       => 'string',
        'sources'     => 'collection',
        'css_class'   => 'string',
        'content'   => FrontendHtmlObject::class,
    ];

    protected $attributes = [
    ];

    ////region HELPERS
    public function getBuildViewData(array $merge_data = []): array
    {
        $viewData = [
            'widgeteable' => $this,
            'site' => $this->site,
            'variables'   => $this->variables,
        ];

        //Debugbar::debug($this->source->type);

        switch (true) {
            case $this->source->type === 'posts':
                /**
                 * @var Page|null $page ;
                 */

                $page = $this->source->data;

                if ($page) {

                    $postsQuery = $page->posts()->published();
                    if ($this->config->sorting->enable || $this->widget->config->sorting->enable) {
                        $postsQuery = $postsQuery->orderBy($this->config->sort_column, $this->config->sort_direction);
                    }
                    $viewData['page'] = $page;
                    $viewData['posts'] = $postsQuery->take(10)->get();
                }
                break;
            case Str::contains($this->source->type, 'list'):

                $customList = $this->source->data;
                $page = $customList->page;

                $viewData['page'] = $page;
                $viewData['customList'] = $customList;
                //Todo: personalizar quantidade de itens
                $viewData['customListItems'] = $customList->items()->with(['list','list.page'])->take(10)->get();
                break;
            case $this->source->type === 'form':
                $viewData['form'] = $this->source->data;
                break;
            default:
                break;
            //Todo:
            /*case 'page':
            case 'products':
            case 'form':
            case 'post':
            case 'address':*/

        }

        return !empty($merge_data) ? [...$viewData, ...$merge_data] : $viewData;
    }

    public static function getSourcesByType($source_type, Site $site = null): Collection
    {
        if (!$site) {
            $site = Auth::user()->site;
        }

        $pages = $site->pages->append(['text']);

        $sources = collect();

        switch ($source_type) {
            //Todo:
            case 'page':
                //Página
                $sources = $sources->merge($pages);
                break;
            case 'form':
                //Formulário
                $items = $site->forms;
                $sources = $sources->merge($items);
                break;
            case 'page.posts':
                //Posts da Página
                $items = $pages->where('using_posts', true);
                $sources = $sources->merge($items);
                break;
            //Todo \/
            case 'page.products':
                //Produtos da Página

                break;
            case 'list':
                //Listas Customizadas
                $items = $site->lists;
                $sources = $sources->merge($items);
                break;
            case 'post':
            case 'address':
                break;

        }

        //CustomLists Types
        foreach (CustomListType::array() as $value => $name) {
            if ($source_type === "list.{$value}") {
                $items = $site->lists()->whereType($value)->get();
                $sources = $sources->merge($items);
            }
        }

        return $sources;
    }

    public function getSortingColumns(): array
    {
        $sourceType = $this->source->type ?? $this->widget->config->source_types->first() ?? false;

        return $sourceType ? config("adminx.data-sources.types.widget.{$sourceType}.sorting_columns", []) : [];

        //return DataSource::getSourceTypeConfig($this->source->type ?? $this->widget->config->source_types->first(), 'sorting_columns', []);
    }
    //endregion

    //region  Attributes

    //region Get's

    protected function variables(): Attribute
    {
        return new Attribute(
            get: fn() => collect($this->widget->config ? $this->widget->config->variables->pluck('default_value', 'slug') : [])->merge($this->config ? $this->config->variables->pluck('value', 'slug') : [])->toArray(),
        );
    }


    protected function sourcesList(): Attribute
    {
        return new Attribute(
            get: fn() => $this->source_type && $this->site ? self::getSourcesByType($this->source_type, $this->site) : collect(),
        );
    }

    protected function sources(): Attribute
    {
        return new Attribute(
            get: function () {

                if ($this->source_type && $this->site && $this->source_id) {
                    return $this->sources_list->whereIn('id', $this->source_ids->values());
                }

                return collect();
            },
        );
    }

    protected function sourceId(): Attribute
    {
        return new Attribute(
            get: fn() => $this->source_ids && $this->source_ids->count() ? $this->source_ids->first() : null,
        );
    }

    /*protected function source(): Attribute
    {
        return new Attribute(
            get: fn() => $this->sources->firstWhere('id', $this->source_id),
        );
    }*/

    protected function cssClass(): Attribute
    {
        return new Attribute(
            get: fn() => Str::replaceNative('.', '-', "widget-{$this->widget->type->slug}-{$this->widget->slug} widget-{$this->public_id}"),
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

        $renderView = $this->config->ajax_render ? 'adminx-common::Elements.Widgets.renders.ajax-render' : 'adminx-common::Elements.Widgets.renders.static-render';

        $renderData = $this->config->ajax_render ? [
            'siteWidget' => $this,
        ] : $this->getBuildViewData();

        $widgetView = View::make($renderView, $renderData);

        $this->content->html = $widgetView->render();

        return parent::save($options);
    }

    //endregion

    //region Relations
    public function widget()
    {
        return $this->belongsTo(Widget::class);
    }
    //endregion
}
