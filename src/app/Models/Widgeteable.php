<?php

namespace ArtisanBR\Adminx\Common\App\Models;

use ArtisanBR\Adminx\Common\App\Enums\CustomLists\CustomListType;
use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use ArtisanBR\Adminx\Common\App\Models\Generics\Configs\WidgetConfig;
use ArtisanBR\Adminx\Common\App\Models\Generics\DataSource;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\OwneredModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\PublicIdModel;
use ArtisanBR\Adminx\Common\App\Models\Scopes\WhereSiteScope;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasOwners;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasPublicIdAttribute;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToSite;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Widgeteable extends MorphPivot implements PublicIdModel, OwneredModel
{
    use HasPublicIdAttribute, HasOwners, BelongsToSite;

    protected array $ownerTypes = ['site'];

    protected $table        = 'widgeteables';
    public    $timestamps   = false;
    public    $incrementing = true;

    protected $fillable = [
        'site_id',
        'widget_id',
        'widgeteable_id',
        'widgeteable_type',
        'source_ids',
        'source_type',
        'source',
        'title',
        'config',
    ];

    protected $casts = [
        'config'      => WidgetConfig::class,
        'source_ids'  => 'collection',
        'source_type' => 'string',
        'source'      => DataSource::class,
        'public_id'   => 'string',
        'title'       => 'string',
        'sources'     => 'collection',
        'css_class'   => 'string',
        'variables'     => 'collection',
    ];

    protected $attributes = [
        'config' => [],
    ];

    ////region HELPERS

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

    /*public function save(array $options = [])
    {
        $retorno = parent::save($options);

        if(empty($this->public_id)){
            $this->public_id = Random::base32String($this->id.time());
            $retorno = parent::save($options);
        }

        return $retorno;
    }*/

    //endregion

    //region Relations
    /*public function site()
    {
        return $this->hasOne(Site::class, 'id', 'site_id');
    }*/

    public function widget()
    {
        return $this->belongsTo(Widget::class);
    }

    public function widgeteable()
    {
        return $this->morphTo(__FUNCTION__);
    }
    //endregion
}
