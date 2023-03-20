<?php

namespace ArtisanBR\Adminx\Common\App\Models\Bases;

use ArtisanBR\Adminx\Common\App\Observers\OwneredModelObserver;
use ArtisanBR\Adminx\Common\App\Observers\PublicIdModelObserver;
use ArtisanBR\Adminx\Common\App\Enums\CustomLists\CustomListItemType;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomList;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems\CustomListItem;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\OwneredModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\PublicIdModel;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasOwners;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasPublicIdAttribute;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasPublicIdUriAttributes;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasSelect2;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasUriAttributes;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasValidation;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToAccount;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToSite;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToUser;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasFiles;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasMorphAssigns;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasParent;
use Illuminate\Support\Facades\DB;

abstract class CustomListItemBase extends EloquentModelBase implements OwneredModel, PublicIdModel
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

    //protected $dates = ['created_at'];

    public static function boot()
    {
        parent::boot();

        self::observe([OwneredModelObserver::class, PublicIdModelObserver::class]);
    }

    //region HELPERS

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
        return "{$this->list->url}/i/" . ($this->slug ?? $this->public_id);
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
