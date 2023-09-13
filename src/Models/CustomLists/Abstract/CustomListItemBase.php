<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Abstract;

use Adminx\Common\Enums\CustomLists\CustomListItemType;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItem;
use Adminx\Common\Models\CustomLists\Object\Configs\CustomListItems\CustomListItemConfig;
use Adminx\Common\Models\CustomLists\Object\CustomListItemDatas\CustomListItemHtmlData;
use Adminx\Common\Models\CustomLists\Object\CustomListItemDatas\CustomListItemImageSliderData;
use Adminx\Common\Models\CustomLists\Object\CustomListItemDatas\CustomListItemTestimonialsData;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Interfaces\UploadModel;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasPublicIdUriAttributes;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasSiteRoutes;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToAccount;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasFiles;
use Adminx\Common\Models\Traits\Relations\HasMorphAssigns;
use Adminx\Common\Models\Traits\Relations\HasParent;
use Adminx\Common\Observers\OwneredModelObserver;
use Adminx\Common\Observers\PublicIdModelObserver;
use Illuminate\Support\Facades\DB;

/**
 * @property CustomListItemHtmlData|CustomListItemImageSliderData|CustomListItemTestimonialsData|null $data
 */
abstract class CustomListItemBase extends EloquentModelBase implements OwneredModel, PublicIdModel, UploadModel
{
    use HasParent, HasOwners, BelongsToUser, BelongsToSite, BelongsToAccount, HasMorphAssigns, HasValidation, HasSelect2, HasPublicIdAttribute, HasPublicIdUriAttributes, HasUriAttributes, HasFiles, HasSiteRoutes;

    protected $table = 'custom_list_items';

    protected string $listClass = CustomList::class;

    protected $fillable = [
        'list_id',
        'parent_id',
        'site_id',
        'user_id',
        'account_id',
        'public_id',
        'position',
        'title',
        'type',
        'slug',
        'config',
        'listable_id',
        'listable_type',
        'data',
        'created_at',
        'updated_at'
    ];

    protected $attributes = [
        'type' => 'image',
        'data' => [],
    ];

    protected $casts = [
        'title'      => 'string',
        'slug'       => 'string',
        'public_id'  => 'string',
        'position'   => 'int',
        'type'       => CustomListItemType::class,
        'config'     => CustomListItemConfig::class,
        'data'       => 'object',
        'created_at' => 'datetime:d/m/Y H:i:s',
    ];

    protected $with = ['list'];

    public function __construct(array $attributes = [])
    {
        $this->attributes['data'] = [];
        parent::__construct($attributes);
    }

    //protected $dates = ['created_at'];

    public static function boot()
    {
        parent::boot();

        self::observe([OwneredModelObserver::class, PublicIdModelObserver::class]);
    }

    //region HELPERS

    public function uploadPathTo(?string $path = null): string
    {
        return ($this->list ? $this->list->uploadPathTo('items') : 'items') . ($path ? "/{$path}" : '');
    }

    public function newPosition(): void
    {
        if (is_null($this->position) || !$this->id) {
            $this->load(['list']);
            $this->position = $this->list->items()->where('parent_id', $this->parent_id)->count();
        }
    }

    public static function findAndMount($id = null, $type = null): CustomListItemBase
    {
        if ($id) {
            $listType = DB::table('custom_list_items')->where('id', $id)->select('type')->first()->type ?? $type ?? null;
        }
        else {
            $listType = $type;
        }

        $mountClass = $listType ? CustomListItemType::from($listType)->mountClass() : CustomListItem::class;

        return ($mountClass)::find($id);

    }

    public function mountModel(): CustomListItemBase
    {
        return self::findAndMount($this->id, $this->type->value);
    }

    public function prepareFrontendBuild($buildMeta = false): FrontendBuildObject
    {
        $frontendBuild = $this->list->page_internal->prepareFrontendBuild();


        //Inicio do body
        $frontendBuild->body->id = "list-item-{$this->public_id}";
        $frontendBuild->body->class .= " list-item-{$this->public_id}";
        //$frontendBuild->body->addBefore($this->assets->js->before_body_html ?? '');

        //Fim do body
        //$frontendBuild->body->addAfter($this->assets->js->after_body_html ?? '');


        $frontendBuild->seo->fill([
                                      ...($this->data->seo?->toArray() ?? []),
                                      'title'       => $this->getTitle(),
                                      'description' => $this->getDescription(),
                                      'keywords'    => $this->getKeywords(),
                                      'image_url'   => $this->seoImage(),
                                  ]);

        if ($buildMeta) {
            $frontendBuild->meta->reset();
            $frontendBuild->meta->registerSeoForPageInternal($this->list->page_internal, $this);
            //$frontendBuild->head->addBefore($frontendBuild->meta->toHtml());
            $frontendBuild->seo->html = $frontendBuild->meta->toHtml();
        }

        return $frontendBuild;
    }
    //endregion

    //region ATTRIBUTES
    protected function getUrlAttribute()
    {
        return ($this->list->url ?? '') . ($this->slug ?? $this->public_id);
    }
    protected function getUriAttribute()
    {
        return ($this->list->uri ?? '') . ($this->slug ?? $this->public_id);
    }
    //endregion

    //region OVERRIDES
    public function getAttribute($key) {
        $value = parent::getAttribute($key);

        if(empty($value) && @$this->data && (method_exists($this->data, 'getAttribute'))){
            $value = $this->data->getAttribute($key);
        }

        return $value;
    }

    public function save(array $options = [])
    {
        if(parent::save($options)){
            $this->data->frontend_build = $this->prepareFrontendBuild(true);
            $this->data->seo->html = $this->data->frontend_build->seo->html;
            return parent::save($options);
        }


        return false; // TODO: Change the autogenerated stub
    }
    //endregion

    //region RELATIONS
    public function list()
    {
        return $this->belongsTo($this->listClass);
    }

    public function listable()
    {
        return $this->morphTo(__FUNCTION__, __FUNCTION__ . '_type', __FUNCTION__ . '_id');
    }
    //endregion
}
