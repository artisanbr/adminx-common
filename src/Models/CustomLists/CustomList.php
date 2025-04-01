<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists;

use Adminx\Common\Enums\CustomLists\CustomListSchemaType;
use Adminx\Common\Enums\CustomLists\CustomListType;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\CustomLists\Abstract\CustomListAbstract;
use Adminx\Common\Models\CustomLists\Abstract\CustomListItemAbstract\CustomListItemAbstract;
use Adminx\Common\Models\CustomLists\Object\Configs\CustomListConfig;
use Adminx\Common\Models\CustomLists\Object\Schemas\CustomListSchemaColumn;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Interfaces\UploadModel;
use Adminx\Common\Models\Scopes\WhereSiteScope;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasPublicIdUriAttributes;
use Adminx\Common\Models\Traits\HasSelect2;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasValidation;
use Adminx\Common\Models\Traits\Relations\BelongsToAccount;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\Categorizable;
use Adminx\Common\Models\Traits\Relations\Pageable;
use Adminx\Common\Observers\OwneredModelObserver;
use Adminx\Common\Observers\PublicIdModelObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

/**
 * @property Collection|Collection<CustomListSchemaColumn>|CustomListSchemaColumn[] $schema
 */
class CustomList extends EloquentModelBase implements PublicIdModel, OwneredModel, UploadModel
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
        HasValidation, Categorizable, SoftDeletes, Pageable;

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
        'schema',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'type' => null,
    ];

    protected $casts = [
        'title'            => 'string',
        'slug'             => 'string',
        'description'      => 'string',
        'public_id'        => 'string',
        'has_image_column' => 'boolean',
        'type'             => CustomListType::class,
        'config'           => CustomListConfig::class,
        'schema'           => AsCollectionOf::class . ':' . CustomListSchemaColumn::class,
        'created_at'       => 'datetime:d/m/Y H:i:s',
        'updated_at'       => 'datetime:d/m/Y H:i:s',
    ];

    /*protected $guarded = [
        'url',
        'uri',
    ];*/


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
            'title'         => ['required'],
            //'type'          => ['required'],
            'schema'        => ['array', 'nullable'],
            'schema.*.type' => ['required'],
            'schema.*.name' => ['required'],
            'schema.*.slug' => ['nullable', 'distinct'],
        ];
    }

    public static function createMessages(FormRequest $request = null): array
    {
        return [
            'title.required'         => 'O título da lista é obrigatório',
            'schema.*.type.required' => 'O campo #:position precisa de um tipo.',
            'schema.*.name.required' => 'O campo #:position precisa de um nome.',
            'schema.*.slug.distinct' => 'O campo #:position tem um apelido repetido.',
        ];
    }
    //endregion

    //region HELPERS

    public function uploadPathTo(?string $path = null): string
    {
        $uploadPath = "lists/{$this->public_id}";

        //return ($this->site ? $this->site->uploadPathTo($uploadPath) : $uploadPath) . ($path ? str($path)->start('/')->toString() : '');

        return str($this->site ? $this->site->uploadPathTo($uploadPath) : $uploadPath)
            ->finish('/')
            ->when(!empty($path), fn($str) => $str->append(str($path)->replaceStart('/', '')->toString()))
            ->toString();
    }

    public function findSchemaColumn(string $slugOrId)
    {
        return $this->schema->firstWhere('id', $slugOrId) ?? $this->schema->firstWhere('slug', $slugOrId);
    }

    public function itemUrl(CustomListItem $listItem): string
    {
        return $this->url . str($listItem->slug ?? $listItem->public_id)->finish('/')->toString();
    }

    public function itemUri(CustomListItem $listItem): string
    {
        return $this->uri . '/' . ($listItem->slug ?? $listItem->public_id);
    }

    //endregion

    //region ATTRIBUTES
    protected function hasImageColumn(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->schema->whereIn('type', [
                CustomListSchemaType::Image,
                CustomListSchemaType::Image->value,
            ])->count(),
        );
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn($value) => !blank($value) ? $value : null,
            set: fn($value, array $attributes) => str(!blank($value) ? $value : ($attributes['title'] ?? ''))->trim()->toString(),
        );
    }

    protected function text(): Attribute
    {
        return Attribute::make(
            get: fn() => str("<h4>{$this->title}</h4><small>{$this->slug}</small>")->append(str($this->description)->limit()), //$this->type->title()
        );
    }

    protected function getUriAttribute()
    {

        if(blank($this->attributes['uri'] ?? null)){

            $this->attributes['uri'] = $this->page?->uri ?? '';
        }
        return $this->attributes['uri'] ?? '';
    }

    protected function getUrlAttribute()
    {

        if(blank($this->attributes['url'] ?? null)){

            $this->attributes['url'] = $this->page?->url ?? '';
        }

        return $this->attributes['url'] ?? '';
    }
    //endregion


    public function items()
    {
        return $this->hasMany(CustomListItem::class, 'list_id', 'id')->ordered();//->orderBy('position');
    }

/*
    public function getCategoriesAttribute()
    {
        return CustomList::find($this->id)->categoriesMorph()->get();
    }

    public function categoriesMorph()
    {
        //return $this->morphToMany(Category::class, 'categorizable');
        return $this->morphToMany(Category::class, 'categorizable', 'categorizables');
    }*/
    //endregion

}
