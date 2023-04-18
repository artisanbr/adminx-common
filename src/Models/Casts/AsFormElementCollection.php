<?php

namespace Adminx\Common\Models\Casts;

use Adminx\Common\Elements\Collections\FormElementCollection;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class AsFormElementCollection extends AsCollection
{
    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes
        {
            public function get($model, $key, $value, $attributes)
            {
                if (! isset($attributes[$key])) {
                    return;
                }

                $data = !is_string($attributes[$key]) ? $attributes[$key] : json_decode($attributes[$key], true);

                return is_array($data) ? new FormElementCollection($data) : null;
            }

            public function set($model, $key, $value, $attributes)
            {

                $json_value = new FormElementCollection(is_array($value) ? $value : []);


                return [$key => $json_value->toJson( JSON_OBJECT_AS_ARRAY)];
            }
        };
    }
}
