<?php

namespace ArtisanBR\Adminx\Common\App\Models\Casts;

use ArtisanBR\Adminx\Common\App\Models\Collections\GenericCollection;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class AsCollectionOf extends AsCollection
{


    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class($arguments[0] ?? GenericCollection::class) implements CastsAttributes
        {
            public function __construct(
                protected string $itemClass,
            ) {}

            public function get($model, $key, $value, $attributes)
            {

                if (!isset($attributes[$key])) {
                    return collect();
                }

                $data = !is_string($attributes[$key]) ? $attributes[$key] : json_decode($attributes[$key], true);

                return GenericCollection::wrap($data ?? [])->mapInto($this->itemClass);
            }

            public function set($model, $key, $value, $attributes): array
            {
                /*if($value) {
                    dd(GenericCollection::wrap($value ?? [])->map(fn($item) => ($item instanceof GenericModel) ? $item : new
                    $this->collectionClass($item))->toJson());
                }*/

                $json_value = is_string($value) ? $value : GenericCollection::wrap($value ?? [])->map(fn($item) => ($item instanceof GenericModel) ? $item : new
                $this->itemClass($item))->toJson();

                return [$key => $json_value];
            }
        };
    }
}
