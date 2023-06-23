<?php

namespace Adminx\Common\Models\Generics;

use Adminx\Common\Enums\CustomLists\CustomListType;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\CustomListBase;
use Adminx\Common\Models\Form;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Post;
use Adminx\Common\Models\Site;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * @property Page|Form|Post|CustomListBase|null $data
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


    protected $temporary = ['data', 'select_option_list'];
    protected $hidden = ['data'];

    public static function getSourcesByType($source_type, Site $site = null): Collection
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
            case 'posts':
                //Posts da Página
                $items = $pages->where('using_posts', true);
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

    public static function getSourceTypeConfig($sourceType, $config = null, $defaultValue = null, $model = 'widget')
    {

        $sourceTypeConfig = "adminx.data-sources.types.{$model}.$sourceType";

        return $config ? config("{$sourceTypeConfig}.{$config}", $defaultValue) : config($sourceTypeConfig, $defaultValue);
    }


    //region Attributes
    protected function getDataAttribute()
    {

        if (!isset($this->attributes['data']) || (empty($this->attributes['data']) || $this->attributes['data']->id !== $this->id)) {

            $this->attributes['data'] = null;

            $site = (Auth::check() && Auth::user()->site) ? Auth::user()->site : FrontendSite::current();

            switch ($this->type) {
                //Todo:
                case 'page':
                case 'posts':
                case 'products':
                    //Página. Posts, Produtos (retornam a página)
                    $this->attributes['data'] = $site->pages()->where('id', $this->id)->first();
                    break;
                case 'form':
                    //Formulário
                    $this->attributes['data'] = $site->forms()->where('id', $this->id)->first();
                    break;
                //Todo \/
                case 'post':
                case 'address':
                default:
                    break;

            }

            if(empty($this->attributes['data']) && ($this->type === 'list' || Str::contains($this->type, 'list.'))){
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

        return $this->attributes['data'];
    }

    protected function getSelectOptionListAttribute()
    {
        return $this->data ?? false ? [$this->data->id => $this->data->text ?? $this->data->title] : [];
    }
    //endregion

}
