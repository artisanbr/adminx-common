<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Bases;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentModelBase extends Model
{

    public $timestamps = true;

    protected array  $ownerTypes         = ['user', 'site', 'account'];
    protected array $temporaryAttributes = [];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function toCleanArray(): array
    {
        //$this->makeHidden($this->with);
        //$this->append(['url','uri']);
        return $this->makeHidden($this->with ?? [])->toArray();
    }

    public function toCleanArrayWith($appends = []): array
    {
        return $this->append($appends)->toCleanArray();
    }

    //Testes


   /* public function attributesToArray()
    {
        // If an attribute is a date, we will cast it to a string after converting it
        // to a DateTime / Carbon instance. This is so we will get some consistent
        // formatting while accessing attributes vs. arraying / JSONing a model.
        $attributes = $this->addDateAttributesToArray(
            $attributes = $this->getArrayableAttributes()
        );


        $attributes = $this->addMutatedAttributesToArray(
            $attributes, $mutatedAttributes = $this->getMutatedAttributes()
        );

        // Next we will handle any casts that have been setup for this model and cast
        // the values to their appropriate type. If the attribute has a mutator we
        // will not perform the cast on those attributes to avoid any confusion.
        $attributes = $this->addCastAttributesToArray(
            $attributes, $mutatedAttributes
        );
        //dd($attributes, $mutatedAttributes);

        // Here we will grab all of the appended, calculated attributes to this model
        // as these attributes are not really in the attributes array, but are run
        // when we need to array or JSON the model for convenience to the coder.
        foreach ($this->getArrayableAppends() as $key) {
            $attributes[$key] = $this->mutateAttributeForArray($key, null);
        }

        return $attributes;
    }

    protected function mutateAttributeForArray($key, $value)
    {
        if ($this->isClassCastable($key)) {
            $value = $this->getClassCastableAttributeValue($key, $value);
        }
        else if (isset(static::$getAttributeMutatorCache[get_class($this)][$key]) &&
            static::$getAttributeMutatorCache[get_class($this)][$key] === true) {
            $value = $this->mutateAttributeMarkedAttribute($key, $value);

            $value = $value instanceof DateTimeInterface
                ? $this->serializeDate($value)
                : $value;
        }
        else {
            $value = $this->mutateAttribute($key, $value);
        }


        return $value instanceof Arrayable ? $value->toArray() : $value;
    }

    protected function addCastAttributesToArray(array $attributes, array $mutatedAttributes)
    {
        foreach ($this->getCasts() as $key => $value) {
            if (!array_key_exists($key, $attributes) ||
                in_array($key, $mutatedAttributes)) {
                continue;
            }

            // Here we will cast the attribute. Then, if the cast is a date or datetime cast
            // then we will serialize the date for the array. This will convert the dates
            // to strings based on the date format specified for these Eloquent models.
            $attributes[$key] = $this->castAttribute(
                $key, $attributes[$key]
            );
            //dump($attributes[$key]);


            // If the attribute cast was a date or a datetime, we will serialize the date as
            // a string. This allows the developers to customize how dates are serialized
            // into an array without affecting how they are persisted into the storage.
            if (isset($attributes[$key]) && in_array($value, [
                    'date',
                    'datetime',
                    'immutable_date',
                    'immutable_datetime',
                ])) {
                $attributes[$key] = $this->serializeDate($attributes[$key]);
            }

            if (isset($attributes[$key]) && ($this->isCustomDateTimeCast($value) ||
                    $this->isImmutableCustomDateTimeCast($value))) {
                $attributes[$key] = $attributes[$key]->format(explode(':', $value, 2)[1]);
            }

            if ($attributes[$key] instanceof DateTimeInterface &&
                $this->isClassCastable($key)) {
                $attributes[$key] = $this->serializeDate($attributes[$key]);
            }

            if (isset($attributes[$key]) && $this->isClassSerializable($key)) {
                $attributes[$key] = $this->serializeClassCastableAttribute($key, $attributes[$key]);
            }

            if ($this->isEnumCastable($key) && (!($attributes[$key] ?? null) instanceof Arrayable)) {
                $attributes[$key] = isset($attributes[$key]) ? $this->getStorableEnumValue($attributes[$key]) : null;
            }

            if ($attributes[$key] instanceof Arrayable) {
                $attributes[$key] = $attributes[$key]->toArray();
            }
        }

        return $attributes;
    }

    protected function castAttribute($key, $value)
    {
        $castType = $this->getCastType($key);

        if (is_null($value) && in_array($castType, static::$primitiveCastTypes)) {
            return $value;
        }

        // If the key is one of the encrypted castable types, we'll first decrypt
        // the value and update the cast type so we may leverage the following
        // logic for casting this value to any additionally specified types.
        if ($this->isEncryptedCastable($key)) {
            $value = $this->fromEncryptedString($value);

            $castType = Str::after($castType, 'encrypted:');
        }

        switch ($castType) {
            case 'int':
            case 'integer':
                return (int)$value;
            case 'real':
            case 'float':
            case 'double':
                return $this->fromFloat($value);
            case 'decimal':
                return $this->asDecimal($value, explode(':', $this->getCasts()[$key], 2)[1]);
            case 'string':
                return (string)$value;
            case 'bool':
            case 'boolean':
                return (bool)$value;
            case 'object':
                return $this->fromJson($value, true);
            case 'array':
            case 'json':
                return $this->fromJson($value);
            case 'collection':
                return new BaseCollection($this->fromJson($value));
            case 'date':
                return $this->asDate($value);
            case 'datetime':
            case 'custom_datetime':
                return $this->asDateTime($value);
            case 'immutable_date':
                return $this->asDate($value)->toImmutable();
            case 'immutable_custom_datetime':
            case 'immutable_datetime':
                return $this->asDateTime($value)->toImmutable();
            case 'timestamp':
                return $this->asTimestamp($value);
        }

        if ($this->isEnumCastable($key)) {
            return $this->getEnumCastableAttributeValue($key, $value);
        }

        if ($this->isClassCastable($key)) {
            return $this->getClassCastableAttributeValue($key, $value);
        }

        return $value;
    }

    protected function getClassCastableAttributeValue($key, $value)
    {
        $caster = $this->resolveCasterClass($key);


        $objectCachingDisabled = $caster->withoutObjectCaching ?? false;


        //dd($caster instanceof CastsInboundAttributes);

        if (isset($this->classCastCache[$key]) && !$objectCachingDisabled) {
            return $this->classCastCache[$key];
        }
        else {
            $value = $caster instanceof CastsInboundAttributes
                ? $value
                : $caster->get($this, $key, $value, $this->attributes);

            //dd($caster instanceof CastsInboundAttributes);

            if ($caster instanceof CastsInboundAttributes ||
                !is_object($value) ||
                $objectCachingDisabled) {
                unset($this->classCastCache[$key]);
            }
            else {
                $this->classCastCache[$key] = $value;
            }

            //dd($value);

            return $value;
        }
    }*/
}
