<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists;

use Adminx\Common\Enums\CustomLists\CustomListItemType;
use Adminx\Common\Enums\CustomLists\CustomListSchemaType;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\CustomLists\Object\Configs\CustomListItems\CustomListItemConfig;
use Adminx\Common\Models\CustomLists\Object\Schemas\CustomListItemSchemaValue;
use Adminx\Common\Models\CustomLists\Object\Schemas\CustomListSchemaColumn;
use Adminx\Common\Models\CustomLists\Object\Values\ImageValue;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Interfaces\UploadModel;
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
use Adminx\Common\Models\Traits\Relations\Categorizable;
use Adminx\Common\Models\Traits\Relations\HasMorphAssigns;
use Adminx\Common\Models\Traits\Relations\HasParent;
use Adminx\Common\Observers\OwneredModelObserver;
use Adminx\Common\Observers\PublicIdModelObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property Collection|Collection<CustomListItemSchemaValue>|CustomListItemSchemaValue[] $schema
 */
class CustomListItem extends EloquentModelBase implements OwneredModel, PublicIdModel, UploadModel
{
    use HasParent,
        HasOwners,
        BelongsToUser,
        BelongsToSite,
        BelongsToAccount,
        HasMorphAssigns,
        HasValidation,
        HasSelect2,
        HasPublicIdAttribute,
        HasPublicIdUriAttributes,
        HasUriAttributes,
        //HasFiles,
        Categorizable,
        HasSiteRoutes,
        SoftDeletes;

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
        'schema',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'type'   => 'image',
        'schema' => [],
    ];

    protected $casts = [
        'title'      => 'string',
        'slug'       => 'string',
        'public_id'  => 'string',
        'position'   => 'int',
        'type'       => CustomListItemType::class,
        'config'     => CustomListItemConfig::class,
        'data_old'   => 'object',
        'has_image'  => 'bool',
        'schema'     => AsCollectionOf::class . ':' . CustomListItemSchemaValue::class,
        'created_at' => 'datetime:d/m/Y H:i:s',
        'updated_at' => 'datetime:d/m/Y H:i:s',
    ];

    //protected $guarded = ['url', 'uri'];

    //protected $with = ['list'];

    public function __construct(array $attributes = [])
    {
        /*$this->attributes['schema'] = [];

        if ($attributes) {
            dd($attributes);
        }*/
        parent::__construct($attributes);
    }

    //protected $dates = ['created_at'];

    public static function boot()
    {
        parent::boot();

        self::observe([OwneredModelObserver::class, PublicIdModelObserver::class]);
    }

    //region Scopes
    public function scopeSearch(Builder $query,
                                string  $searchTerm,
                                array   $columns = [
                                    'public_id',
                                    'title',
                                    'slug',
                                    'schema',
                                ],
                                bool    $searchInCategories = false,
                                array   $categoryColumns = [
                                    'title',
                                    'slug',
                                    'description',
                                ]): Builder
    {

        if (!blank($searchTerm)) {
            $query = $query->whereLike($columns, $searchTerm);

            if ($searchInCategories) {
                $query = $query->orWhereHas(
                    'categories',
                    fn(Builder $query) => $query->whereLike($categoryColumns, $searchTerm));
            }
        }

        return $query;
    }

    public function scopeWhereUrl(Builder $query, string $url): Builder
    {

        return $query->where(function (Builder $q) use ($url) {
            return $q->where('slug', $url)
                     ->orWhere('public_id', $url)
                     ->orWhere('id', $url);
        });
    }

    public function scopeWhereUrlIn(Builder $query, string|array $urls): Builder
    {

        $urls = is_array($urls) ? $urls : [$urls];

        return $query->where(function (Builder $q) use ($urls) {
            return $q->whereIn('slug', $urls)
                     ->orWhereIn('public_id', $urls)
                     ->orWhereIn('id', $urls);
        });
    }

    public function scopeWithRelations(Builder $query): Builder
    {
        return $query->with(['list', 'list.page']);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderByDesc('created_at')->orderBy('title');
    }

    public function scopeOrderedDesc(Builder $query): Builder
    {
        return $query->orderByDesc('position')->orderBy('created_at')->orderBy('title');
    }
    //endregion

    //region HELPERS

    protected function getDataClass(): string
    {
        return $this->type->dataClass();
    }

    public function uploadPathTo(?string $path = null): string
    {

        return str($this->list ? $this->list->uploadPathTo('items') : 'items')
            ->finish('/')
            ->append($this->public_id)
            ->finish('/')
            ->when(!empty($path), fn($str) => $str->append(str($path)->replaceStart('/', '')))
            ->toString();
    }

    public function newPosition(): void
    {
        if (is_null($this->position) || !$this->id) {
            $this->load(['list']);
            $this->position = $this->list->items()->where('parent_id', $this->parent_id)->whereNotNull('position')->count() + 1;
        }
    }

    public function getSchemaValueForColumn(CustomListSchemaColumn|string $column, array $defaultData = []): ?CustomListItemSchemaValue
    {

        if (is_string($column)) {
            $column = $this->list->findSchemaColumn($column);
        }

        if (!($column instanceof CustomListSchemaColumn)) {
            return null;
        }

        return $this->findSchemaValue($column->slug) ?? $column->generateSchemaValue($defaultData);
    }

    public function findSchemaValue(string $slugOrColumnId, array $wheres = [
        'column.id',
        'slug',
    ]): ?CustomListItemSchemaValue
    {

        foreach ($wheres as $where) {
            $find = $this->schema->firstWhere($where, $slugOrColumnId);
            if ($find) {
                return $find;
            }
        }

        return null;
    }


    public function setSchemaValueBySlug($slug, $value): bool
    {

        return $this->transformSchemaBySlug($slug, fn(CustomListItemSchemaValue $item) => $item->setValue($value));
    }

    public function setSchemaValueByColumnId($columnId, $value): bool
    {

        return $this->transformSchemaByColumnId($columnId, fn(CustomListItemSchemaValue $item) => $item->setValue($value));
    }


    public function transformSchemaBySlug($slug, callable $transform): bool
    {

        if ($this->schema->where('slug', $slug)->count()) {
            $this->schema->where('slug', $slug)->transform($transform);

            return true;
        }

        return false;
    }

    public function transformSchemaByColumnId($columnId, callable $transform): bool
    {

        if ($this->schema->where('column.id', $columnId)->count()) {
            $this->schema->where('column.id', $columnId)->transform($transform);

            return true;
        }

        return false;
    }


    public function nextItem($orderColumn = 'position'): ?static
    {
        return self::where('list_id', $this->list_id)
                   ->where($orderColumn, '>', $this->{$orderColumn})
                   ->orderBy('id', 'asc')
                   ->first();
    }

    public function previousItem($orderColumn = 'position'): ?static
    {
        return self::where('list_id', $this->list_id)
                   ->where($orderColumn, '<', $this->{$orderColumn})
                   ->orderBy($orderColumn, 'desc')
                   ->first();
    }

    public function slugOrPublicId()
    {
        return !blank($this->slug) ? $this->slug : $this->public_id;
    }

    //endregion

    //region ATTRIBUTES

    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $value,
            set: fn($value, array $attributes) => str(!blank($value) ? $value : ($attributes['title'] ?? ''))->slug()->toString(),
        );
    }

    protected array $dataCache = [];

    protected function data(): Attribute
    {
        if (!$this->dataCache) {
            $this->dataCache = $this->schema->mapWithKeys(fn(CustomListItemSchemaValue $schemaItem, int $key) => match (true) {
                $schemaItem->type?->is(CustomListSchemaType::Image) => [
                    $schemaItem->slug                => $schemaItem,
                    $schemaItem->slug . "_url"       => $schemaItem->url,
                    $schemaItem->slug . "_alt"       => $schemaItem->value->alt,
                    $schemaItem->slug . "_html"      => $schemaItem->value->html,
                    $schemaItem->slug . "_full_html" => $schemaItem->value->full_html,
                ],
                $schemaItem->type?->is(CustomListSchemaType::PDF) => [
                    $schemaItem->slug          => $schemaItem,
                    $schemaItem->slug . "_url" => $schemaItem->url,
                ],
                $schemaItem->slug === 'content' &&
                $schemaItem->type?->is(CustomListSchemaType::Html) &&
                !$this->schema->where('slug', 'html')->count() => [
                    $schemaItem->slug => $schemaItem->value,
                    'html'            => $schemaItem->value,
                ],
                default => [$schemaItem->slug => $schemaItem->value],
            })->all();
        }


        return Attribute::make(
            get: fn($value, array $attributes) => $this->dataCache,
        );
    }

    protected function hasImage(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->schema->whereIn('type', [
                CustomListSchemaType::Image,
                CustomListSchemaType::Image->value,
            ])->count(),
        );
    }

    protected function firstImage(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->schema->whereIn('type', [
                CustomListSchemaType::Image,
                CustomListSchemaType::Image->value,
            ])->first()?->value ?? new ImageValue(),
        );
    }

    protected function firstImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->first_image->url,
        );
    }

    protected function html(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->data['html'] ?? $this->data['content'],
        );
    }

    protected function generateUrl()
    {

        return ($this->list?->url ?? '') . $this->slugOrPublicId();
    }

    protected function generateUri()
    {

        return ($this->list?->uri ?? '') . $this->slugOrPublicId();
    }
    //endregion

    //region OVERRIDES
    public function getAttribute($key)
    {
        //Debugbar::debug($key);
        $value = parent::getAttribute($key);

        if (is_null($value)
        ) {

            if ($key == 'html') {
                dd($key);
            }


            if (isset($this->data[$key]) && !is_null($this->data[$key])) {
                return $this->data[$key];
            }
            else if (@$this->schema &&
                method_exists($this->schema, 'where') &&
                $this->schema->where('slug', $key)->count()
            ) {
                $value = $this->schema->firstWhere('slug', $key)->value;
            }

        }

        return $value;
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

    /*public function getCategoriesAttribute()
    {
        return CustomListItem::find($this->id)->categoriesMorph()->get();
    }*/

    /*public function categoriesMorph()
    {
        //return $this->morphToMany(Category::class, 'categorizable');
        return $this->morphToMany(Category::class, 'categorizable', 'categorizables');
    }*/
    //endregion


}
