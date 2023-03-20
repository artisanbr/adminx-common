<?php

namespace ArtisanBR\Adminx\Common\App\Libs\Helpers;

use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use Illuminate\Support\Collection;

class MorphHelper
{
    /**
     * Mapa de Morphs
     * @return Collection
     */
    public static function morphMaps(): Collection{
        return collect(config('adminx.defines.morphs.map'));
    }

    /**
     * Retorna o Morph Type de uma Class
     * @param $class
     *
     * @return bool|mixed
     */
    public static function getMorphTypeTo($class): mixed
    {
        if(is_object($class)){
            $class = get_class($class);
        }
        //return self::morphMaps()->map(fn($value, $key) => Str::lower($value))->search(Str::lower($class));
        return self::morphMaps()->search(fn($value, $key) => Str::lower($value) === Str::lower($class));
    }

    /**
     * Retorna a Class de um Morph Type
     * @param $type
     *
     * @return mixed
     */
    public static function getMorphClassTo($type): mixed
    {
        return self::morphMaps()->first(fn($value, $key) => Str::lower($key) === Str::lower($type));
    }

    /**
     * Retorna o inverso do Morph informado, Type para Class e vice-versa.
     * @param $morph
     *
     * @return bool|mixed
     */
    public static function reverseMappedMorph($morph): mixed
    {
        return self::isMappedMorphClass($morph) ? self::getMorphTypeTo($morph) : self::getMorphClassTo($morph);
    }

    /**
     * Retorna o Morph Type para o parâmetro informado
     * @param $morph
     *
     * @return mixed
     */
    public static function resolveMorphType($morph): mixed
    {
        $morph = self::traitMorphClass($morph);

        if(self::isMappedMorphClass($morph)){
            return self::getMorphTypeTo($morph);
        }

        return $morph;
    }

    /**
     * Retorna a Morph Class para o parâmetro informado
     * @param $morph
     *
     * @return mixed
     */
    public static function resolveMorphClass($morph): mixed
    {
        $morph = self::traitMorphClass($morph);

        if(self::isMappedMorphType($morph)){
            return self::getMorphClassTo($morph);
        }

        return self::isMappedMorphClass($morph) ? $morph : null;
    }

    /**
     * Verifica se o Morph informado está mapeado como Type ou Class
     * @param $morph
     *
     * @return bool
     */
    public static function isMappedMorph($morph): bool{
        return self::isMappedMorphClass($morph) ?? self::isMappedMorphType($morph) ?? false;
    }

    /**
     * Verifica se o parâmetro informado é uma Classe mapeada como Morph
     * @param $morph
     *
     * @return bool
     */
    public static function isMappedMorphClass($morph): bool{
        $morph = self::traitMorphClass($morph);
        $morph = Str::lower($morph);

        return Str::contains($morph, ['models/', 'models\\']) && self::getMorphTypeTo($morph) ?? false;
    }

    /**
     * Verifica se o parâmetro informado é um Type mapeado de Morph
     * @param $type
     *
     * @return bool
     */
    public static function isMappedMorphType($type): bool{
        return self::getMorphClassTo($type) ?: false;
    }

    protected static function traitMorphClass($mophClass): string{
        return is_object($mophClass) ? get_class($mophClass) : $mophClass;
    }
}
