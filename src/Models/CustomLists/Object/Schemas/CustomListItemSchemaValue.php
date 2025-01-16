<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\Schemas;

use Adminx\Common\Enums\CustomLists\CustomListSchemaType;
use Adminx\Common\Models\CustomLists\Object\Values\ButtonValue;
use Adminx\Common\Models\CustomLists\Object\Values\ImageValue;
use Adminx\Common\Models\CustomLists\Object\Values\PDFValue;
use Adminx\Common\Models\Objects\Seo\Seo;
use Adminx\Common\Objects\Files\ImageFileObject;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;

/**
 * @property array|object|ImageValue|ButtonValue|null|string|Collection<ButtonValue>|Collection<ImageFileObject>|ButtonValue[]|ImageValue[]
 *           $value
 * @property ?CustomListSchemaType $type
 * @property null|string|ImageValue|Seo|ButtonValue|ImageValue[]|ButtonValue[]|Collection<ButtonValue>|Collection<ImageValue>|PDFValue|PDFValue[]|Collection<PDFValue> $value
 */
class CustomListItemSchemaValue extends GenericModel
{

    protected $fillable = [
        'value',
        'name',
        'slug',
        'type',
        'column',
    ];

    protected $casts = [
        'column' => CustomListSchemaColumn::class,
        'name'   => 'string',
        'slug'   => 'string',
        'type'   => CustomListSchemaType::class,
    ];

    public function __construct(array $attributes = [])
    {
        /*$this->addFillables([
                                'value',
                            ]);*/
        parent::__construct($attributes);

        if ($this->type?->valueCast() ?? false) {
            $this->addCasts([
                                'value' => $this->type?->valueCast(),
                            ]);
        }


        /*if ($this->type->isAny([
                                   CustomListSchemaType::Image,
                                   CustomListSchemaType::Button,
                                   CustomListSchemaType::ButtonCollection,
                                   CustomListSchemaType::ImageCollection,
                               ])) {
            $this->addCasts([
                                'value' => match ($this->type) {
                                    CustomListSchemaType::Image => ImageFileObject::class,
                                    CustomListSchemaType::Button => ButtonValue::class,
                                    CustomListSchemaType::ButtonCollection => AsCollectionOf::class . ':' . ButtonValue::class,
                                    CustomListSchemaType::ImageCollection => AsCollectionOf::class . ':' . ImageFileObject::class,
                                },
                            ]);
        }*/
    }


    //protected $appends = ['url'];

    //region Attributes
    //region GET's
    /*protected function getValueAttribute($value)
    {
        if ($this->type->isAny([
                                   CustomListSchemaType::ButtonCollection,
                               ])) {
            dd($value);
        }

        $value = $this->attributes["value"] ?? null;

        return match ($this->type) {
            CustomListSchemaType::Image => $this->getImageValue($value),
            default => $value,
        };
    }*/

    protected function getImageValue($value): ImageValue
    {

        if ($value instanceof ImageValue) {
            return $value;
        }

        $imageData = match (true) {
            (is_string($value) && json_validate($value)) => json_decode($value),
            is_null($value) => [],
            default => $value,
        };

        return new ImageValue($imageData);
    }

    protected function getUrlAttribute()
    {
        return match ($this->type) {
            CustomListSchemaType::Image, CustomListSchemaType::PDF => $this->value?->url ?? null,
            default => null,
        };
    }
    //endregion

    //region SET's
    /*protected function setValueAttribute($value): static
    {
        return $this->setValue($value);

    }*/

    public function setValue($value): static
    {

        if ($this->type && $this->type->isAny(CustomListSchemaType::Image, CustomListSchemaType::PDF)) {
            $this->fillFileObjectValue($value, $this->type->valueCast());
        }
        else {
            parent::setAttribute('value', $value);
        }

        return $this;

    }

    protected function fillFileObjectValue($value, $valueClass): void
    {
        if (is_string($value) && !json_validate($value)) {
            $this->attributes['value'] = $valueClass::make([
                                                              'url'           => $value
                                                          ])->toArray();
        }
        else if ($value instanceof $valueClass) {
            $this->attributes['value'] = $value->toArray();
        }
        else {
            $this->attributes['value'] = $valueClass::make(is_string($value) ? json_decode($value) : $value)->toArray();
        }
    }

    protected function getValueObjectInstance($valueClass, $attributes = []) {
        return new $valueClass($attributes);
    }

    //endregion
    //endregion

}
