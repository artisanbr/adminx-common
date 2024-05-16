<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Casts;

use Adminx\Common\Models\Collections\GenericCollection;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Support\Collection;

class AsCollectionOf extends AsCollection
{


    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class($arguments[0] ?? GenericCollection::class) implements CastsAttributes
        {
            public function __construct(
                protected string $itemClass,
            ) {}

            public function mappedCollection(mixed $items): Collection{
                return GenericCollection::wrap($items)->mapInto($this->itemClass)->values();
            }

            public function get($model, $key, $value, $attributes)
            {
                //dd($model, $key, $value, $attributes);

                $dataArray = is_string($value) ? json_decode($value, true) : (is_array($value) ? $value : null);

                if(empty($value) || is_array($dataArray)){
                    return $this->mappedCollection($dataArray ?? []);
                }

                return $value instanceof Collection ? $value->values() : collect();


                /*if (!isset($attributes[$key])) {
                    return collect();
                }

                $data = !is_string($attributes[$key]) ? $attributes[$key] : json_decode($attributes[$key], true);

                return GenericCollection::wrap($data ?? [])->mapInto($this->itemClass)->values();*/
            }

            public function set($model, $key, $value, $attributes): array
            {
                //dd($model, $key, $value, $attributes, $this->mappedCollection($value));
                /*$json_value = is_string($value) ? $value : GenericCollection::wrap($value ?? [])->map(fn($item) => ($item instanceof GenericModel) ? $item : new
                $this->itemClass($item))->values()->toJson();*/

                if(empty($value) || (!is_subclass_of($value, Collection::class) && !is_string($value))){
                    $value = $this->mappedCollection($value ?? []);
                }


                $json_value = is_string($value) ? $value : $value->toJson();
                //$json_value = is_string($value) ? $value : json_encode($value ?? []);

                if($key == 'items'){
                    /*dump([
                             'type'              => 'set Collection',
                             'key'               => $key,
                             'value'             => $value,
                             //'model'             => $model,
                             //'attributes'             => $attributes,
                             //'merge'             => $mergeResult,
                             'result'            => [$key => $json_value],

                             //'attributes_current' => $attributes[$key] ?? null,
                             //'trace'      => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 25),
                         ]);*/
                }


                return [$key => $json_value];
            }
        };
    }
}
