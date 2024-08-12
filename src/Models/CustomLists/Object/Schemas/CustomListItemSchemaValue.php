<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\Schemas;

use Adminx\Common\Enums\CustomLists\CustomListSchemaType;
use Adminx\Common\Models\CustomLists\Object\Values\ButtonValue;
use Adminx\Common\Objects\Files\ImageFileObject;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;

/**
 * @property array|object|ImageFileObject|ButtonValue|null|string|Collection<ButtonValue>|Collection<ImageFileObject>|ButtonValue[]|ImageFileObject[]
 *           $value
 * @property ?CustomListSchemaType
 *                                                    $type
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

    protected function getImageValue($value): ImageFileObject
    {

        if ($value instanceof ImageFileObject) {
            return $value;
        }

        $imageData = match (true) {
            (is_string($value) && json_validate($value)) => json_decode($value),
            is_null($value) => [],
            default => $value,
        };

        return new ImageFileObject($imageData);
    }

    protected function getUrlAttribute()
    {
        return match ($this->type) {
            CustomListSchemaType::Image => $this->value?->url ?? null,
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

        if ($this->type && $this->type->is(CustomListSchemaType::Image)) {
            if (is_string($value) && !json_validate($value)) {
                $this->attributes['value'] = ImageFileObject::make([
                                                                       'url'           => $value,
                                                                       'width'         => '',
                                                                       'height'        => '',
                                                                       'file_name'     => '',
                                                                       'relative_path' => '',
                                                                   ])->toArray();
            }
            else if ($value instanceof ImageFileObject) {
                $this->attributes['value'] = $value->toArray();
            }
            else {
                $this->attributes['value'] = ImageFileObject::make(is_string($value) ? json_decode($value) : $value)->toArray();
            }
        }
        else {
            parent::setAttribute('value', $value);
        }

        return $this;

    }

    //endregion
    //endregion

}
