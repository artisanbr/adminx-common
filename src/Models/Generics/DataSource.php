<?php

namespace Adminx\Common\Models\Generics;

use Adminx\Common\Enums\CustomLists\CustomListType;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\CustomLists\Abstract\CustomListBase;
use Adminx\Common\Models\Form;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Site;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * @property Page|Form|Article|CustomListBase|null $data
 * @property Page|null                             $page
 */
class DataSource extends GenericModel
{

    protected $fillable = [
        'id',
        'name',
        'internal_url',
        'type',
        'reference_page_id',
    ];

    protected $casts = [
        'id'                 => 'int',
        'name'               => 'string',
        'internal_url'       => 'string',
        'type'               => 'string',
        'select_option_list' => 'array',
    ];

    protected $appends = [
        //'data',
    ];


    protected $temporary = ['data', 'select_option_list', 'page'];

    //protected $hidden = ['data'];

    public static function getSourcesByType($source_type, ?Site $site = null): Collection
    {
        if (!$site) {
            $site = FrontendSite::current();
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
            case 'articles':
                //Posts da Página
                $items = $pages->where('using_articles', true);
                $sources = $sources->merge($items);
                break;
            //Todo \/
            case 'products':
                //Produtos da Página

                break;
            case 'list':
                //Listas Customizadas
                $items = $site->lists;
                $sources = $sources->merge($items);
                break;
            case 'article':
            case 'address':
                break;

        }

        //CustomLists Types
        foreach (CustomListType::array() as $value => $name) {
            if ($source_type === "list.{$value}" || $source_type === "list:{$value}") {
                $items = $site->lists()->whereType($value)->get();
                $sources = $sources->merge($items);
            }
        }

        return $sources;
    }

    public static function getSourceTypeConfig($sourceType, $config = null, $defaultValue = null, $model = 'widget')
    {

        $sourceTypeConfig = "adminx.data-sources.types.{$model}.$sourceType";

        return $config ? config("{$sourceTypeConfig}.{$config}", $defaultValue) : config($sourceTypeConfig, $defaultValue);
    }

    //region Attributes

    public function dataQuery(?Site $site = null): ?Builder
    {
        if (!$site) {
            $site = FrontendSite::current();
        }

        return match ($this->type) {
            'posts', 'articles' => $site->articles()->where('page_id', $this->id),
            'page' => $site->pages()->where('id', $this->id),
            'form' => $site->forms()->where('id', $this->id),
            default => null,
        };
    }

    protected function getSiteAttribute()
    {
        return FrontendSite::current();
    }

    protected function getBelongsToPageAttribute(): bool
    {
        return match ($this->type) {
            'articles',
            'products' => true,
            default => false,
        };
    }

    protected function getPageAttribute(): ?Page
    {
        if ($this->belongs_to_page) {
            if (!($this->attributes['page'] ?? false)) {
                $site = (Auth::check() && Auth::user()->site) ? Auth::user()->site : FrontendSite::current();
                $this->attributes['page'] = $site->pages()->where('id', $this->id)->first();
            }

            return $this->attributes['page'];
        }

        return null;
    }

    protected function getDataAttribute()
    {

        if ($this->id && $this->type) {
            if (!isset($this->attributes['data']) || empty($this->attributes['data'])) {

                $this->attributes['data'] = null;

                $site = $this->site;

                switch ($this->type) {
                    //Todo:
                    case 'page':
                        $this->attributes['data'] = $this->dataQuery()->first();
                        break;
                    case 'articles':
                        /**
                         * @var ?Page $page
                         */
                        //Página. Posts, Produtos (retornam um array de items)
                        //$page = $site->pages()->find($this->id);
                        $this->attributes['data'] = $this->dataQuery()->get();
                        break;
                    case 'form':
                        //Formulário
                        $this->attributes['data'] = $this->dataQuery()->first();
                        break;
                    //Todo \/
                    case 'article':
                    case 'address':
                    case 'products':
                    default:
                        break;

                }

                if (empty($this->attributes['data']) && ($this->type === 'list' || Str::contains($this->type, 'list.'))) {
                    //Listas Customizadas
                    /**
                     * @var CustomListBase $customList
                     */
                    $customList = $site->lists()->where('id', $this->id)->first();

                    if ($customList) {
                        $this->attributes['data'] = $customList->mountModel();
                    }
                }


                //$this->attributes['data'] = self::getSourcesByType($this->type)->firstWhere('id', $this->id);
            }
        }
        else {
            $this->attributes['data'] = null;
        }


        return $this->attributes['data'];
    }

    protected function getSelectOptionListAttribute(): array
    {

        return $this->belongs_to_page ? $this->page?->select_option_list : ($this->data ? [$this->data->id => $this->data->text ?? $this->data->title] : []);
    }
    //endregion

}
