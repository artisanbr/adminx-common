<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Casts;

use Adminx\Common\Libs\Support\ArrayObject;
use Adminx\Common\Models\Collections\GenericCollection;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Support\Collection;

class AsMergedCollectionOf extends AsCollection
{


    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class($arguments[0] ?? GenericCollection::class) implements CastsAttributes {
            public function __construct(
                protected string $itemClass,
            ) {}

            public function get($model, $key, $value, $attributes)
            {

                if (!isset($attributes[$key])) {
                    return collect();
                }

                //dd($value);

                //$data = !is_string($attributes[$key]) ? $attributes[$key] : json_decode($attributes[$key], true);
                $data = !is_string($value) ? $value : json_decode($value, true);

                return GenericCollection::wrap($data ?? [])->mapInto($this->itemClass)->values();
            }

            public function set($model, $key, $value, $attributes): array
            {

                if ($value instanceof Collection) {
                    $value = $value->toArray();
                }

                $currentValue = $attributes[$key] ?? [];

                if (is_string($currentValue)) {
                    $currentValue = json_decode($currentValue, true);
                }

                $mergeValue = ArrayObject::mergeRecursive($currentValue, $value);

                //dd($mergeValue);

                //$json_value = is_string($value) ? $value : GenericCollection::wrap($value ?? [])->toJson();

                return [$key => json_encode($mergeValue)];
            }
        };
    }
}
