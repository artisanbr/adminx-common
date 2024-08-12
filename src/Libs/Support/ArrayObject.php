<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\Support;

class ArrayObject
{
    /**
     * Mescla recursivamente dois arrays.
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    public static function mergeRecursive(array $array1, array $array2)
    {
        foreach ($array2 as $key => $value) {
            // Se o valor no array2 é null, removemos a chave do array1
            if (is_null($value)) {
                unset($array1[$key]);
            }
            else if (isset($array1[$key]) && is_array($array1[$key]) && is_array($value)) {
                // Se a chave existe em ambos os arrays e os valores são arrays, mesclar recursivamente
                $array1[$key] = self::mergeRecursive($array1[$key], $array2[$key]);
            }
            else {
                // Caso contrário, sobrescrever o valor no primeiro array
                $array1[$key] = $value;
            }
        }

        return $array1;
    }
}