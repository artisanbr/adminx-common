<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics;

use Adminx\Common\Enums\CustomLists\CustomListType;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\CustomLists\Abstract\CustomListAbstract;
use Adminx\Common\Models\Form;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Sites\Site;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * @property Page|Form|Article|CustomListAbstract|null $data
 * @property Page|null                                 $page
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

    protected $dataCache = null;

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

        $dataType = $this->attributes['type'] ?? false;
        $dataId = $this->attributes['id'] ?? null;

        return match ($dataType) {
            'posts', 'articles' => $site->articles()->where('page_id', $dataId),
            'page' => $site->pages()->where('id', $dataId),
            'form' => $site->forms()->where('id', $dataId),
            default => null,
        };
    }

    protected function getSiteAttribute()
    {
        return FrontendSite::current();
    }

    protected function getBelongsToPageAttribute(): bool
    {
        return match ($this->attributes['type'] ?? false) {
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
                $this->attributes['page'] = $site->pages()->where('id', $this->attributes['id'])->first();
            }

            return $this->attributes['page'];
        }

        return null;
    }

    protected function getDataAttribute()
    {
        $dataType = $this->attributes['type'] ?? false;
        $dataId = $this->attributes['id'] ?? null;

        if ($dataType && $dataId) {
            if (!$this->dataCache) {

                $this->dataCache = null;

                $site = $this->getSiteAttribute();

                switch (true) {
                    //Todo:
                    case $dataType === 'page':
                        $this->dataCache = $this->dataQuery()->first();
                        break;
                    case $dataType === 'articles':
                        /**
                         * @var ?Page $page
                         */
                        //Página. Posts, Produtos (retornam um array de items)
                        $this->dataCache = $this->dataQuery()->get();
                        break;
                    case $dataType === 'form':
                        //Formulário
                        $this->dataCache = $this->dataQuery()->first();
                        break;
                    case $dataType === 'list' || Str::contains($dataType, 'list.'):
                        //Listas Customizadas
                        /**
                         * @var CustomListAbstract $customList
                         */
                        $customList = $site->lists()->where('id', $dataId)->first();

                        if ($customList) {
                            $this->dataCache = $customList->mountModel();
                        }
                        break;
                    /*//Todo \/
                    case 'article':
                    case 'address':
                    case 'products':*/
                    default:
                        break;

                }
            }
        }
        else {
            $this->dataCache = null;
            $this->attributes['data'] = null;
        }


        return $this->dataCache;
    }

    protected function getSelectOptionListAttribute(): array
    {

        return $this->belongs_to_page ? $this->page?->select_option_list : ($this->data ? [$this->data->id => $this->data->text ?? $this->data->title] : []);
    }
    //endregion

}
