<?php

namespace Adminx\Common\Models\Bases;

use Adminx\Common\Models\Interfaces\UploadModel;
use Adminx\Common\Observers\OwneredModelObserver;
use Adminx\Common\Observers\PublicIdModelObserver;
use Adminx\Common\Enums\CustomLists\CustomListItemType;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItem;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasPublicIdUriAttributes;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToAccount;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasFiles;
use Adminx\Common\Models\Traits\Relations\HasMorphAssigns;
use Adminx\Common\Models\Traits\Relations\HasParent;
use Illuminate\Support\Facades\DB;

abstract class CustomListItemBase extends EloquentModelBase implements OwneredModel, PublicIdModel, UploadModel
{
    use HasParent, HasOwners, BelongsToUser, BelongsToSite, BelongsToAccount, HasMorphAssigns, HasValidation, HasSelect2, HasPublicIdAttribute, HasPublicIdUriAttributes, HasUriAttributes, HasFiles;

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
    ];

    protected $attributes = [
        'type' => 'image',
        'data' => [],
    ];

    protected $casts = [
        'title' => 'string',
        'slug' => 'string',
        'public_id' => 'string',
        'position' => 'int',
        'type' => CustomListItemType::class,
        'config' => 'object',
        'data' => 'object',
        'created_at' => 'datetime:d/m/Y H:i:s',
    ];

    //protected $with = ['list'];

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

        return $mountClass::find($id);

    }

    public function mountModel(){
        return self::findAndMount($this->id, $this->type->value);
    }

    //endregion

    //region ATTRIBUTES
    protected function getUrlAttribute()
    {
        return $this->slug ?? $this->public_id;
    }
    //endregion

    //region RELATIONS
    public function list(){
        return $this->belongsTo($this->listClass);
    }

    public function listable()
    {
        return $this->morphTo(__FUNCTION__, __FUNCTION__ . '_type', __FUNCTION__ . '_id');
    }
    //endregion
}
