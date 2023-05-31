<?php

namespace Adminx\Common\Models\Bases;

use Adminx\Common\Observers\OwneredModelObserver;
use Adminx\Common\Observers\PublicIdModelObserver;
use Adminx\Common\Enums\CustomLists\CustomListType;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItem;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Scopes\WhereSiteScope;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasPublicIdUriAttributes;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToAccount;
use Adminx\Common\Models\Traits\Relations\BelongsToPage;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

abstract class CustomListBase extends EloquentModelBase implements PublicIdModel, OwneredModel
{
    use HasValidation, HasOwners, BelongsToUser, BelongsToSite, BelongsToAccount, BelongsToPage, HasPublicIdAttribute, HasPublicIdUriAttributes, HasUriAttributes, HasSelect2;

    protected $table = 'custom_lists';

    protected string $listItemClass = CustomListItem::class;

    protected $fillable = [
        'id',
        'site_id',
        'user_id',
        'account_id',
        'page_id',
        'public_id',
        'title',
        'slug',
        'type',
        'description',
        'config',
    ];

    protected $attributes = [
        'type' => 'gallery.image',
    ];

    protected $casts = [
        'title'       => 'string',
        'slug'       => 'string',
        'description' => 'string',
        'public_id'   => 'string',
        'type'        => CustomListType::class,
        'config'      => 'object',
    ];

    protected $appends = [
        'text',
    ];

    public static function boot()
    {
        parent::boot();

        self::observe([OwneredModelObserver::class, PublicIdModelObserver::class]);
    }

    protected static function booted()
    {
        static::addGlobalScope(new WhereSiteScope);
    }

    //region VALIDATIONS
    public static function createRules(FormRequest $request = null): array
    {
        return [
            'title' => ['required'],
            'type'  => ['required'],
        ];
    }

    public static function createMessages(FormRequest $request = null): array
    {
        return [
            'title.required' => 'O título da lista é obrigatório',
            'type.required'  => 'O tipo de lista é obrigatório',
        ];
    }
    //endregion

    //region HELPERS

    public function mountAppends(){

    }

    /**
     * Encontra uma model e monta conforme o tipo
     *
     * @param $id
     * @param $type
     *
     * @return CustomListBase
     */
    public static function findAndMount($id = null, $type = null): CustomListBase
    {
        if (!$type && $id) {
            //dd(CustomList::where('id',$id)->select(['type'])->get());
            $listType = CustomList::where('id',$id)->select(['type'])->get()->type->value ?? null;
        }
        else {
            $listType = $type;
        }

        $mountClass = $listType ? CustomListType::from($listType)->mountClass() : CustomList::class;

        return $mountClass::find($id);

    }

    public function mountModel(){

        $mountClass = $this->type->value ? CustomListType::from($this->type->value)->mountClass() : CustomList::class;

        $mountModel = $mountClass::make($this->toArray());
        $mountModel->refresh();

        return $mountModel;

        //return self::findAndMount($this->id, $this->type->value);
    }

    public function itemUrl(CustomListItemBase $listItem): string
    {
        return $this->url . '/i/' . ($listItem->slug ?? $listItem->public_id);
    }

    public function itemUri(CustomListItemBase $listItem): string
    {
        return $this->uri . '/i/' . ($listItem->slug ?? $listItem->public_id);
    }

    //endregion

    //region ATTRIBUTES
    protected function getUrlAttribute()
    {
        return ($this->page->url ?? '') . '/l/' . ($this->slug ?? $this->public_id);
    }
    //endregion

    //region RELATIONS
    public function items()
    {
        return $this->hasMany($this->listItemClass, 'list_id', 'id')->orderBy('position');
    }


    //endregion
}
