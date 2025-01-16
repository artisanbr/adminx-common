<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\Schemas;

use Adminx\Common\Enums\CustomLists\CustomListSchemaType;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\CustomLists\Object\Values\ImageValue;
use Adminx\Common\Objects\Files\ImageFileObject;
use ArtisanLabs\GModel\GenericModel;

/**
 * @property mixed|ImageFileObject|null $default_value
 * @property CustomListSchemaType $type
 */
class CustomListSchemaColumn extends GenericModel
{

    protected $fillable = [
        'id',
        'name',
        'slug',
        'type',
        'order',
        'position',
        'data',
        'default_value',
        'editable',
    ];

    protected $casts = [
        'id'       => 'string', //ULID
        'position' => 'int',
        'name'     => 'string',
        'slug'     => 'string',
        'type'     => CustomListSchemaType::class,
        'data'     => 'collection',
    ];

    protected $attributes = [
        'slug' => null,
        'data' => [],
        'type' => CustomListSchemaType::Text->value,
    ];

    protected $temporary = [
        'editable',
    ];

    /*protected $appends = [
        //'id',
    ];
    */

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (empty($this->attributes['id'] ?? null)) {
            $this->getIdAttribute();
        }
    }


    //region Helpers
    public function generateSchemaValue(array $attributes = []): CustomListItemSchemaValue
    {
        return new CustomListItemSchemaValue([
                                                 'value'  => $this->attributes['default_value'] ?? null,
                                                 'name'   => $this->name,
                                                 'slug'   => $this->getSlugAttribute(),
                                                 'type'   => $this->type->value,
                                                 'column' => $this->toArray(),
                                                 ...$attributes,
                                             ]);
    }
    //endregion

    //region Attributes
    //region GET's
    protected function getIdAttribute(): string
    {

        if (blank($this->attributes["id"] ?? null)) {
            $this->attributes["id"] = (string) Str::ulid();
            //dd($this->attributes["id"]);
        }

        return $this->attributes["id"];
    }

    protected function getSlugAttribute(): ?string
    {

        if (!blank($this->attributes["slug"] ?? null)) {
            return $this->attributes["slug"];
        }


        if (!blank($this->name ?? null)) {
            $this->attributes["slug"] = str($this->name)->lower()->slug()->limit(24);

            return $this->attributes["slug"];
        }


        return null;
    }

    protected function getDefaultValueAttribute()
    {
        $value = $this->attributes["default_value"] ?? null;

        if (!$value) {
            return $value;
        }


        return match ($this->type) {
            CustomListSchemaType::Image => (is_array($value) || (is_string($value) && json_validate($value))) ? new ImageFileObject(is_string($value) ? json_decode($value) : $value) : $value,
            default => $value,
        };
    }

    protected function getExampleCodesAttribute(): array
    {
        return $this->type?->exampleCodes($this) ?? [];
    }
    //endregion

    //region SET's

    protected function setDefaultValueAttribute($value): void
    {

        if ($this->type->is(CustomListSchemaType::Image)) {
            if (is_string($value) && !json_validate($value)) {
                $this->attributes['default_value'] = ImageValue::make([
                                                                               'url'           => $value,
                                                                               'width'         => '',
                                                                               'height'        => '',
                                                                               'file_name'     => '',
                                                                               'relative_path' => '',
                                                                           ])->toArray();
            }
            else {
                $this->attributes['default_value'] = ImageValue::make(is_string($value) ? json_decode($value) : $value)->toArray();
            }
        }
        else {
            $this->attributes['default_value'] = $value;
        }


    }

    protected function setOrderAttribute($value)
    {
        $this->attributes['position'] = $value;
    }

    //endregion
    //endregion
}
