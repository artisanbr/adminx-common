<?php

namespace Adminx\Common\Models\CustomLists\Abstract;

use Adminx\Common\Enums\CustomLists\CustomListType;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItem;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Interfaces\UploadModel;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Pages\PageInternal;
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
use Adminx\Common\Observers\OwneredModelObserver;
use Adminx\Common\Observers\PublicIdModelObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Http\FormRequest;

abstract class CustomListBase extends EloquentModelBase implements PublicIdModel, OwneredModel, UploadModel
{
    use BelongsToAccount,
        //BelongsToPage,
        BelongsToSite,
        BelongsToUser,
        HasOwners,
        HasPublicIdAttribute,
        HasPublicIdUriAttributes,
        HasSelect2,
        HasUriAttributes,
        HasValidation;

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
        'created_at',
        'updated_at',
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
        //'text',
    ];

    //protected $with = ['page'];

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

    public function uploadPathTo(?string $path = null): string
    {
        $uploadPath = "lists/{$this->public_id}";
        return ($this->site ? $this->site->uploadPathTo($uploadPath) : $uploadPath) . ($path ? "/{$path}" : '');
    }

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
            $listType = CustomList::where('id',$id)->select(['type'])->first()->type->value ?? null;
        }
        else {
            $listType = $type;
        }

        $mountClass = $listType ? CustomListType::from($listType)->mountClass() : CustomList::class;

        return ($mountClass)::find($id);

    }

    public function mountModel(){

        $mountClass = ($this->type->value ? CustomListType::from($this->type->value)->mountClass() : CustomList::class);
        $mountModel = ($mountClass)::make($this->toArray());
        $mountModel->refresh();

        return $mountModel;

        //return self::findAndMount($this->id, $this->type->value);
    }

    public function itemUrl(CustomListItemBase $listItem): string
    {
        return $this->url . ($listItem->slug ?? $listItem->public_id) . '/';
    }

    public function itemUri(CustomListItemBase $listItem): string
    {
        return $this->uri . '/' . ($listItem->slug ?? $listItem->public_id);
    }

    //endregion

    //region ATTRIBUTES

    protected function text(): Attribute
    {
        return Attribute::make(
            get: fn() => "<h4>{$this->title}</h4>{$this->type->title()}",
        );
    }

    protected function getUrlAttribute()
    {
        if(!$this->page_internal){
            $this->load(['page_internal']);
        }

        return $this->page_internal?->url ?? '';
        //return ($this->page_internal->url ?? '') . '/' . ($this->slug ?? $this->public_id);
    }
    //endregion

    //region RELATIONS
    public function items()
    {
        return $this->hasMany($this->listItemClass, 'list_id', 'id')->orderBy('position');
    }

    /*public function pages(){
        return $this->morphEagerTo(Page::class, 'model', 'page_internals', 'model_id', 'page_id')->where('page_internals.model_type', 'list');
    }*/

    public function page_internal(){
        return $this->hasOne(PageInternal::class, 'model_id')->where('model_type', 'list')->latestOfMany();
    }

    public function page(){
        return $this->hasOneThrough(Page::class,PageInternal::class, 'model_id', 'id', 'id', 'page_id')->where('model_type', 'list');
    }


    public function page_internals(){
        return $this->hasMany(PageInternal::class, 'model_id')->where('model_type', 'list');
    }

    public function pages(){
        return $this->hasManyThrough(Page::class,PageInternal::class, 'model_id', 'id', 'id', 'page_id')->where('model_type', 'list');
    }
    //endregion
}
