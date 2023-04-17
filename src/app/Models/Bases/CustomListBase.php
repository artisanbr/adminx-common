<?php

namespace ArtisanBR\Adminx\Common\App\Models\Bases;

use ArtisanBR\Adminx\Common\App\Observers\OwneredModelObserver;
use ArtisanBR\Adminx\Common\App\Observers\PublicIdModelObserver;
use ArtisanBR\Adminx\Common\App\Enums\CustomLists\CustomListType;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomList;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems\CustomListItem;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\OwneredModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\PublicIdModel;
use ArtisanBR\Adminx\Common\App\Models\Scopes\WhereSiteScope;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasOwners;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasPublicIdAttribute;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasPublicIdUriAttributes;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasSelect2;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasUriAttributes;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasValidation;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToAccount;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToPage;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToSite;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

abstract class CustomListBase extends EloquentModelBase implements PublicIdModel, OwneredModel
{
    use HasValidation, HasOwners, BelongsToUser, BelongsToSite, BelongsToAccount, BelongsToPage, HasPublicIdAttribute, HasPublicIdUriAttributes, HasUriAttributes, HasSelect2;

    protected $table = 'custom_lists';

    protected string $listItemClass = CustomListItem::class;

    protected $fillable = [
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
            $listType = CustomList::find($id)->type->value ?? null;
        }
        else {
            $listType = $type;
        }

        $mountClass = $listType ? CustomListType::from($listType)->mountClass() : CustomList::class;

        return $mountClass::find($id);

    }

    public function mountModel(){
        return self::findAndMount($this->id, $this->type->value);
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
